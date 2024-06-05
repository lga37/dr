<?php

namespace Tests\Browser;

use App\Models\Busca;
use App\Models\Canal;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class buscaTest extends DuskTestCase
{


    #protected static $domain = 'laravel.com';
    protected static $url = 'https://youtube.com/';
    protected static $keys = ['restaurante',];
    const PROD = true;


    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    /** @test */
    public function urlSpider()
    {
        $this->browse(function (Browser $browser) {
            $this->getLinks($browser, self::$keys);
        });
    }



    protected function getLinks(Browser $browser, $keys)
    {

        $doc = new \DOMDocument('1.0', 'utf-8');
        libxml_use_internal_errors(true);
        $doc->validateOnParse = true;
        $doc->preserveWhiteSpace = false;


        #dd($keys);
        foreach ($keys as $busca) {
            $url = self::$url . 'results?search_query=' . $busca;
            $browser->visit($url);
            $browser->maximize();
            #$query_id = insertQuery($key);
            $varreu_tudo = false;
            $i = 0;
            $h = 2000;

            while (!$varreu_tudo) {
                $i++;
                $h += 2000;

                $browser->script('window.scrollTo(0,' . $h . ');');

                try {
                    #$contents = $browser->driver->findElement(WebDriverBy::id('content'));
                    $contents = $browser->element('#content');
                    $html = $contents->getDomProperty('innerHTML');

                    #dd($html);

                } catch (\Exception $e) {
                    echo $e->getMessage();
                    continue;
                }


                #$html2 = array_map(fn($html) => mb_convert_encoding($html, "UTF-8", mb_detect_encoding($html)), ["\x5A\x6F\xEB"]);
                $doc->loadHTML(utf8_decode($html));
                
                
                $ysr_s = $doc->getElementsByTagName('ytd-item-section-renderer');

                if ($this->chegouNoFim($ysr_s) || !self::PROD) {
                #if ($i==5) {
                    $yvr_s = $doc->getElementsByTagName('ytd-video-renderer');

                    #$ypr_s = $doc->getElementsByTagName('ytd-playlist-renderer');
                    #echo "\n\n Iniciando Playlists \n\n";
                    #$playlists = savePlaylists($ypr_s);

                    #foreach ($playlists as $p) {
                    #    extract($p); #nome_video,nome_canal,slug_video,slug_canal,playlist,tipo
                    #    $desc_video = $playlist;
                    #    echo "\n", saveHistBuscaToBD($query_id, $nome_video, $nome_canal, $slug_video, $slug_canal, $desc_video, $tipo);
                    #}

                    echo "\n\n Iniciando Videos \n\n";
                    $this->saveVideos($yvr_s,$busca);
                    $varreu_tudo = true;
                }
            }
        }

    
    }

    private function saveVideos($yvr_s,$busca)
    {

        $arr = [];
        $user = $link = $title = $desc = $canal = $tipo = "";
        foreach ($yvr_s as $key => $yvr) {
            $as = $yvr->getElementsByTagName('a');
            $slug_canal = $slug_video = $nome_video = $desc_video = $nome_canal = "";
            foreach ($as as $k => $a) {
                #echo "<h2>$k</h2>";
                if ($a->getAttribute('id') == 'video-title') {
                    if(empty($slug_video)){
                        $txt = trim($a->getAttribute('href'));
                        if(preg_match('/\/watch\?v=(.+)/',$txt,$m)){
                            #$slug_video = $m[1];
                            $slug_video = $txt;
                            $tipo = "v";
                        } elseif(preg_match('/\/shorts\/(.+)/',$txt,$m)){
                            #$slug_video = $m[1];
                            $slug_video = $txt;
                            $tipo = "s";
                        }
                    }
    
                    if(empty($nome_video)){
                        $nome_video = formataString($a->getAttribute('title'));
                    }
                } elseif ($a->getAttribute('id') == 'channel-thumbnail') {
                    if(empty($slug_canal)) $slug_canal = urldecode(trim($a->getAttribute('href'))); 
                    if(empty($nome_canal)) $nome_canal = formataString($a->getAttribute('title')); 
    
                } elseif ($a->getAttribute('class') == 'yt-simple-endpoint style-scope yt-formatted-string') {
                    if(empty($slug_canal)) $slug_canal = urldecode(trim($a->getAttribute('href'))); 
                    if(empty($nome_canal)) $nome_canal = formataString($a->nodeValue); 
                }
    
                if (empty($desc_video)) {
                    $yfs = $a->getElementsByTagName('yt-formatted-string');
                    foreach ($yfs as $y) {
                        if ($y->getAttribute('class') == "metadata-snippet-text-navigation style-scope ytd-video-renderer") {
                            $current_encoding = mb_detect_encoding($y->nodeValue);
                            $desc_video = empty($desc_video) ? formataString($y->nodeValue) : "";
                        }
                    }
                }
    
            }
            $res = compact('slug_canal', 'slug_video', 'nome_video', 'desc_video', 'nome_canal','tipo');
            $arr[] = $res;
        }

        $b = Busca::updateOrCreate(
            ['slug'=>Str::slug($busca)],
            ['q'=>$busca]
        );

        foreach ($arr as $k => $v) {
            extract($v); #slug_canal slug_video nome_video desc_video nome_canal tipo (v ou s)

            $canal = Canal::updateOrCreate(
                ['slug'=>$slug_canal],
                ['nome'=>$nome_canal,'busca_id'=>$b->id]
            );

            $video = Video::updateOrCreate(
                ['slug'=>$slug_video],
                ['nome'=>$nome_video,'canal_id'=>$canal->id,'busca_id'=>$b->id]
            );    
        
        }

        #return $arr;
    }
    
    ######################################################################





    #em canal.php
 

    private function processaVideos($doc, $slug_canal)
    {
        $h3s = $doc->getElementsByTagName('h3');
        $videos_do_canal = [];
        foreach ($h3s as $h3) {
            $as = $h3->getElementsByTagName('a');
            if (count($as) == 1) {
                $a = $h3->getElementsByTagName('a')->item(0);
                $slug_video = $a->getAttribute('href');
                $nome_video = $a->getAttribute('title');
                $videos_do_canal[] = compact('nome_video', 'slug_video', 'slug_canal');
            } else {
                echo "\nErro ao processar - nao tem links de title " . __FUNCTION__ . " em " . $slug_canal;
                isolaTrechoHtml($doc);
            }
        }

        #print_r($videos_do_canal);die;
        return $videos_do_canal;
    }



    function chegouNoFim($ysr_s): bool
    {
        $tot = $ysr_s->count();

        echo "\n Total de ysr_s : $tot ---------- ";
        #<yt-formatted-string id="message" class="style-scope ytd-message-renderer">Sem mais resultados

        $ultimo = $ysr_s->item($tot - 1);
        $yfs = $ultimo->getElementsByTagName('yt-formatted-string');
        foreach ($yfs as $y) {
            if ($y->getAttribute('id') == 'message') {
                if (trim($y->nodeValue) == 'Sem mais resultados' || trim($y->nodeValue) == 'No more results') {
                    echo "\n\n\n BATEUUUUUUUUUUUUUUUUUU";
                    return true;
                }
            }
        }
        return false;
    }


    function savePlaylists($ypr_s)
    {
        $arr = [];
        $tipo = 'p';
        #$user = $slug_video = $title = $desc = $canal = "";
        foreach ($ypr_s as $key => $ypr) {
            $as = $ypr->getElementsByTagName('a');
            $slug_canal = $slug_video = $nome_video = $playlist = $nome_canal = "";

            foreach ($as as $k => $a) {
                #echo "<h2>$k</h2>";
                if ($a->getAttribute('class') == 'yt-simple-endpoint style-scope ytd-playlist-renderer') {
                    if (empty($slug_video)) {
                        $slug_video = urldecode(trim($a->getAttribute('href')));
                        if (preg_match('/\/watch\?v=(.+)&list=(.+)/', $slug_video, $matches)) {
                            $slug_video = $matches[1];
                            $playlist = $matches[2];
                        }
                    }
                    if (empty($nome_video)) {
                        $spans = $a->getElementsByTagName('span');
                        foreach ($spans as $span) {
                            if ($span->getAttribute('id') == 'video-title') {
                                $nome_video = formataString($span->nodeValue);
                            }
                        }
                    }
                } elseif ($a->getAttribute('class') == 'yt-simple-endpoint style-scope yt-formatted-string') {
                    $href = urldecode(trim($a->getAttribute('href')));
                    $node = formataString($a->nodeValue);
                    if (preg_match('/^\/channel\/(.+)/', $href, $matches)) {
                        $slug_canal = urldecode($matches[1]);
                        $nome_canal = $node;
                    } elseif (preg_match('/^\/playlist\?list=(.+)/', $href, $m)) {
                        $playlist = $m[1];
                    } elseif (preg_match('/\/c\/(.+)\/playlist\?list=(.+)/', $href, $ma)) {
                        $slug_canal = urldecode($ma[1]);
                        #/c/RosângelaPaulaTavares/playlist?list=PLYdA3m_BBjSkvyoo4WooQGQvSmOJi37j
                        $playlist = $ma[2];
                    } elseif (preg_match('/^\/c\/(.+)/', $href, $m)) {
                        $slug_canal = urldecode($href);
                        $nome_canal = formataString($node);
                    } else {
                        echo "<hr>dentro do else ----------- $href -------------<br>";
                    }
                }
            }
            $res = compact('slug_canal', 'slug_video', 'nome_video', 'playlist', 'nome_canal', 'tipo');
            $arr[] = $res;
        }
        return $arr;
        #var_dump($arr);
    }
}
