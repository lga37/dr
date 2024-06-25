<?php

namespace Tests\Browser;

use App\Models\Canal;
use TimeoutException;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class canalTest extends DuskTestCase
{


    protected static $url = 'https://youtube.com/';
    const PROD = true;


    /** @test */
    public function urlSpider()
    {
        
        $this->browse(function (Browser $browser) {

            $canais = Canal::all()->pluck('slug');

            $canais_unicos = getCanaisUnicos();

            #$canais = ['/@cafefilosofico_cpfl'];
    
            #$abas = ['videos', 'playlists', 'channels', 'about'];
            $abas = ['about'];
            
            $res = [];
            foreach ($canais as $k => $slug_canal) {
                #pegar o id header
                echo "\n----------------- INICIANDO CANAL $slug_canal";
                if (in_array($slug_canal, array_values($canais_unicos))) {
                    echo "\n----------------- PULANDO CANAL em canais_unicos: [$k] - $slug_canal \n";
                    echo getKeyCanaisUnicos($slug_canal) . "\n\n";
                    continue;
                }
                if (self::PROD) {
                    $url = "https://www.youtube.com$slug_canal/about";
            
                    #dd($url);
                    try {
                        $browser->visit($url);#->waitFor('#content');
                        sleep(2);
                        #$browser->maximize()->fitContent();
                        #youtube_id tem muitas coisas em meta
                        #<meta itemprop="identifier" content="UCLjpUjWJQRo3tiRzYh23q3A">                        $canonical = $browser->element('link');
                        $metas = $browser->elements("meta");
                        $res = [];
                        $youtube_id = $desc = $name = null;
                        $tags = []; #essas tags sao do meta
                        #dd(count($metas));
                        foreach ($metas as $i=>$meta) {
                            #$html = $meta->getDomProperty('innerHTML');
                            
                            $property = $meta->getAttribute('property')??'';
                            $itemprop = $meta->getAttribute('itemprop')??'';
                            $name = $meta->getAttribute('name')??'';
                            $content = $meta->getAttribute('content')??'';
                            
                            $res[$i]=compact('property','itemprop','name','content');
                            #$content = $meta->getAttribute('content')??'';
                            #dump("\n$i:".$content);
                            if($itemprop == 'identifier'){
                                $youtube_id = $content;
                                $upd = Canal::where('slug',$slug_canal)->update(compact('youtube_id'));
                                dump("upd1:$upd");
                            }

                            if($itemprop == 'description'){
                                $desc = $content;
                                $upd = Canal::where('slug',$slug_canal)->update(compact('desc'));
                                dump("upd2:$upd");
                            }
                            if($itemprop == 'name'){
                                $nome = $content;
                                $upd = Canal::where('slug',$slug_canal)->update(compact('nome'));
                                dump("upd3:$upd");
                            }
                            // if($property == 'og:video:tag'){
                            //     $tags[] = $content;
                            // }

                        }
                        dump(count($res));
                        #$res = compact('youtube_id','desc','name','tags');
                        #dd($tags);
                        #sleep(3);
                        #tem 6 mas so 1 e a tag do modal com as info
                        // $contents = $browser->elements('#content');
                        // #dump(count($contents));
                        // foreach ($contents as $k=>$content) {
                        //     $class = $content->getAttribute('class');
                        //     if ($class == 'style-scope ytd-engagement-panel-section-list-renderer') {
                        //         $html = $content->getDomProperty('innerHTML');
                        //         #dump("\n\n$k:".$html);
                        //     }
                        // }
                        #dd('fimmmmmmmmmmmmmmmmmmmmmmmm');
                    
                    } catch (\Exception $e) {
                        echo "\n----- erro: " . $e->getMessage();
                        continue;
                    }
            
                    #$html = $contents->getDomProperty('innerHTML');
                    #$doc->loadHTML(utf8_decode($html));
                } else {
                    $doc = new \DOMDocument('1.0', 'utf-8');
                    libxml_use_internal_errors(true);
                    $doc->validateOnParse = true;
                    $doc->preserveWhiteSpace = false;
                    $html = file_get_contents('storage/app/public/html_youtube/meta.html');
                    $html = trim($html);
                    $doc->loadHTML(utf8_decode($html));
                }
            
                // echo "\n-----------------============ Processando Meta Info";
                // $res['meta'] = $this->processaMeta($doc, $slug_canal);
                // print_r($res['meta']);
                // if (empty($res['meta']['nome_canal']) && $res['meta']['inscritos'] == 0) {
                //     $msg = "\n ------------------- Nao pegou nome nem inscritos - pulando " . $slug_canal;
                //     colorLog($msg, 'e');
                //     continue;
                // }
            
                // foreach ($abas as $aba) {
                //     echo "\n-----------------===================== ABA $aba";
                //     if (self::PROD) {
                //         $url = "https://www.youtube.com/$slug_canal/$aba";
            
                //         try {
                //             $browser->visit($url);
                //             sleep(2);
                //         } catch (\Exception $e) {
                //             $msg = "\n ------------------- Nao pegou o get da URL ------------ Exception : " . $e->getMessage();
                //             colorLog($msg, 'e');
                //             continue;
                //         }
            
                //         try {
                //             sleep(3);
                //             $contents = $browser->element("#contents"); #atencao aqui content pega tudo e contents em MUSIC so o header
                //             $html = $contents->getDomProperty('innerHTML');
            
                //         } catch (\Exception $e) {
                //             $msg = "\n ------------------- Nao pegou o HTML ------------ Exception : " . $e->getMessage();
                //             colorLog($msg, 'e');
                //             continue;
                //         }
            
                //     } else {
                //         $html = file_get_contents('storage/app/public/html_youtube/meta.html_' . $aba . '.html');
                //         $html = trim($html);
                //     }
            
                //     $doc->loadHTML(utf8_decode($html)); #id= ou content [MUSIC por ex.]
            
                //     switch ($aba) {
                //         case 'videos':
                //             $res['videos'] = $this->processaVideos($doc, $slug_canal);
                //             break;
                //         case 'playlists':
                //             #$res['playlists'] = $this->processaPlaylists($doc, $slug_canal);
                //             break;
                //         case 'channels':
                //             $res['channels'] = $this->processaCanaisFilhos($doc, $slug_canal);
                //             break;
                //         case 'about':
                //             $res['about'] = $this->processaSobre($doc, $slug_canal);
                //             break;
                //     }
                // }
            
                // if (!self::PROD) {
                //     dd($res['meta']);
                   
                // }
            
                // if (empty($res['about']['dt']) && $res['about']['views'] == 0) {
                //     $msg = "\n ------------------- Nao pegou dt nem views - pulando " . $slug_canal;
                //     colorLog($msg, 'e');
                //     continue;
                // }
                
                #$this->saveCanal($res, $slug_canal); #aqui faz o parse e finaliza
            
            
                
            }
            
    
        });
    }
    

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


    function saveCanal($res, $slug_canal)
    {
        extract($res); #meta videos playlists channels about

        if (!isset($meta, $videos, $playlists, $channels, $about)) {
            echo "variaves nao setadas - saveCanal";
            return;
        }

        echo "\n--------- entrando em videos ------------------------";
        foreach ($videos as $video) {
            extract($video); #nome_video slug_video slug_canal
            echo PHP_EOL . insertVideoViaCanal($nome_video, $slug_video, $slug_canal);

            saveVideosViaCanalMxM($slug_video, $slug_canal);
        }

        echo "\n--------- entrando em channels -----------------------";
        foreach ($channels as $channel) {
            extract($channel); #nome_canal_filho slug_canal_filho slug_canal
            echo PHP_EOL . insertCanalByPai($nome_canal_filho, $slug_canal_filho, $slug_canal);
        }

        echo "\n--------- entrando em playlists ---------------------------";
        foreach ($playlists as $playlist) {
            extract($playlist); #nome_playlist slug_playlist slug_canal tipo
            echo PHP_EOL . insertVideoViaCanal($nome_playlist, $slug_playlist, $slug_canal, $tipo);
            saveVideosViaCanalMxM($slug_playlist, $slug_canal);
        }

        #meta about e parse=1

        echo "\n--------- salvando meta -----------------------";
        extract($meta);
        echo insertCanalByMeta($nome_canal, $slug_canal, $inscritos, $verificado); #incluir tb os views

        echo "\n--------- salvando about ------------------------";
        if (!is_array($about)) {
            echo "about - Nao e array";
            return;
        }
        extract($about); #desc local links dt views

        upsertCanalSobre($slug_canal, $desc, $local, $links, $dt, $views);
        echo saveHistCanal($slug_canal, $inscritos);

        echo "\n--------- salvando a flag parse\n";
        global $cn;
        $q = "UPDATE canais set parse=1 WHERE slug='$slug_canal';";
        $stmt = $cn->prepare($q);
        echo "\nFinalizou: ";
        echo $stmt->execute() ? 'OK' : 'Err';
    }


    function processaMeta($doc, $slug_canal)
    {
        $inscritos = $verificado = 0;
        $nome_canal = "";

        $divs = $doc->getElementsByTagName('div');
        foreach ($divs as $div) {

            if (empty($nome_canal)) {
                if ($div->getAttribute('id') == 'text-container') {
                    $yfs = $div->getElementsByTagName('yt-formatted-string');
                    foreach ($yfs as $y) {
                        if ($y->getAttribute('id') == 'text') {
                            $nome_canal = formataString($y->nodeValue);
                            #echo 'nome_canal :',$nome_canal;
                        }
                    }
                }
            }

            if ($inscritos == 0) {
                $yfs = $div->getElementsByTagName('yt-formatted-string');
                foreach ($yfs as $y) {
                    if ($y->getAttribute('id') == 'subscriber-count') {
                        $v = $y->nodeValue;
                        if (empty($v)) {
                            echo "\n --- checar isso inscritos=0 em canal: $slug_canal";
                            $inscritos = 0;
                        } else {
                            $inscritos = preg_replace("/&nbsp;/", " ", $inscritos);
                            $inscritos = parseNumLikes($v);
                        }
                    }
                }
            }

            if ($verificado == 0) {
                if (
                    $div->getAttribute('class') == "badge badge-style-type-verified style-scope ytd-badge-supported-renderer" &&
                    ($div->getAttribute('aria-label') == "Verificado" || $div->getAttribute('aria-label') == "Verified")
                ) {
                    $verificado = 1;
                }
            }
            /*
        if($verificado==0){
            if($div->getAttribute('id') == 'meta'){
                $divs2 = $div->getElementsByTagName('div');
                foreach($divs2 as $d){
                    if($d->getAttribute('id') == 'tooltip'){
                        #&& $d->getAttribute('class') == 'hidden style-scope tp-yt-paper-tooltip'){
                        if(trim($d->nodeValue) == 'Verificado' || trim($d->nodeValue) == 'Verified'){
                            $verificado = 1;
                            break;
                        }
                    }
                }
            }
        }
        */
        }


        $meta = compact('nome_canal', 'slug_canal', 'inscritos', 'verificado');

        return $meta;
    }

    function processaCanaisFilhos($doc, $slug_canal)
    {
        $divs = $doc->getElementsByTagName('div');
        $canais_do_canal = [];
        foreach ($divs as $div) {
            if ($div->getAttribute('id') == 'channel') {
                $as = $div->getElementsByTagName('a');
                foreach ($as as $a) {
                    $slug_canal_filho = $a->getAttribute('href');
                    $spans = $a->getElementsByTagName('span');
                    $nome_canal_filho = false;
                    foreach ($spans as $span) {
                        if ($span->getAttribute('id') == 'title') {
                            $nome_canal_filho = $span->nodeValue;
                            break;
                        }
                    }
                    if ($nome_canal_filho) {
                        $canais_do_canal[] = compact('nome_canal_filho', 'slug_canal_filho', 'slug_canal');
                    }
                }
            }
        }
        return $canais_do_canal;
    }

    function processaSobre($doc, $slug_canal)
    {
        #isolaTrechoHtml($doc);

        $desc = $local = $dt = "";
        $views = 0;
        $links = [];
        $divs = $doc->getElementsByTagName('div');
        foreach ($divs as $div) {
            if ($div->getAttribute('id') == 'description-container') {
                #echo "\n entrou em desc";
                $yfs = $div->getElementsByTagName('yt-formatted-string');
                foreach ($yfs as $y) {
                    if ($y->getAttribute('id') == 'description') {
                        $desc = formataString($y->nodeValue);
                    }
                }
            }

            if ($div->getAttribute('id') == 'bio-container') {
                #$bio = $div;
            }
            if ($div->getAttribute('id') == 'photos-container') {
                #$photos = $div;
            }
            if ($div->getAttribute('id') == 'details-container') {
                $details = trim($div->nodeValue);
                if (preg_match('/(Local|Location):?([\n|\s]+)?([a-zA-Z\n\s]+)/', $details, $m)) {  #Local ou Location
                    $local = formataString($m[3]);
                }
            }
            if ($div->getAttribute('id') == 'links-container') {
                $as = $div->getElementsByTagName('a');
                $links = [];
                foreach ($as as $a) {
                    $link = urldecode($a->getAttribute('href'));
                    if (preg_match('/&q=(.+)/', $link, $m)) {
                        $link = $m[1];
                        #} else {
                        #    continue;
                        $nome = formataString($a->nodeValue);
                        $links[$nome] = $link;
                    }
                }
                $links = json_encode($links);
            }

            if ($div->getAttribute('id') == 'right-column') {
                $yfs = $div->getElementsByTagName('yt-formatted-string');
                foreach ($yfs as $y) {
                    $spans = $y->getElementsByTagName('span');
                    foreach ($spans as $span) {
                        if ($span->getAttribute('class') == 'style-scope yt-formatted-string') {
                            $res = trim($span->nodeValue);
                            if ($d = parseDataUploadVideo($res)) {
                                $dt = $d;
                                break;
                            }
                        }
                    }
                    if ($views == 0) {
                        if ($y->getAttribute('class') == 'style-scope ytd-channel-about-metadata-renderer') {
                            $res = trim($y->nodeValue);
                            if (preg_match('/views|visuali/', $res)) {
                                $views = preg_replace('/[^\d]/', '', $res);
                                break;
                            }
                        }
                    }
                }
            }
        }

        $about = compact('desc', 'local', 'links', 'dt', 'views');
        #dd($about);  

        #upsertCanalSobre($desc,$local,$links,$dt,$views);

        return $about;
    }



}
