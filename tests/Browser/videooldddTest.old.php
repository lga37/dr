<?php

namespace Tests\Browser;

use App\Models\Video;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class videoTest extends DuskTestCase
{



    protected static $url = 'https://youtube.com/';
    const PROD = false;


    /** @test */
    public function urlSpider()
    {
        $doc = new \DOMDocument('1.0', 'utf-8');
        libxml_use_internal_errors(true);
        $doc->validateOnParse = true;
        $doc->preserveWhiteSpace = false;
        $this->browse(function (Browser $browser) use ($doc) {


            $ids = Video::all()->pluck('slug')->toArray();
           

            foreach ($ids as $chave => $slug_video) {
                echo "\n ============= INICIANDO $chave   $slug_video";
                if (str_contains($slug_video, '/shorts/') || str_contains($slug_video, '&list=')) {
                    echo " ============= shorts/list : ";
                    #$q = "UPDATE videos set parse=2 WHERE slug='$slug_video';";
                    continue;
                }
                $comments = $likes = $views = 0;
                $content = "";

                if (self::PROD) {
                    $start_time = microtime(true);

                    if (str_contains($slug_video, '/shorts/')) {
                        $id_div = "shorts-container";
                    } else {
                        $id_div = "below";
                    }

                    $url = "https://www.youtube.com$slug_video";

                    dd($url);

                    try {
                        $browser->visit($url);
                        #$driver->get($url);

                    } catch (\Exception $e) {
                        echo "\n\n----- Timeout - pulou " . $e->getMessage();
                        continue;
                    }

                    try {

                        #$driver->manage()->timeouts()->implicitlyWait(5);
                        #$driver->manage()->window()->maximize();
                        $browser->maximize();
                        #sleep(2);

                        #dd($browser);
                        #$driver->executeScript('window.scrollTo(0,500);');
                        $browser->script('window.scrollTo(0,500);');
                        sleep(3);
                    } catch (\Exception $e) {
                        echo "\n\n----- maximize " . $e->getMessage();
                        continue;
                    }

                    ####################################### transcript

                    ###########################################################################

                    // $legendas = [];
                    // try {

                    //     #content - videos indisponiveis, nao pode ser content pq senao da pau la embaixo
                    //     #$content_invalidos = $driver->findElement(WebDriverBy::id('content'));
                    //     $content_invalidos = $browser->element('#content');
                    //     $html = $content_invalidos->getDomProperty('innerHTML');
                    //     $doc->loadHTML(utf8_decode($html));
                    //     $divs = $doc->getElementsByTagName('div');

                    //     foreach ($divs as $div) {
                    //         #Vídeo privado
                    //         if ($div->getAttribute('id') == 'reason' && $div->getAttribute('class') == 'style-scope yt-player-error-message-renderer') {
                    //             echo '...............', $div->nodeValue;
                    //             #if(trim($div->nodeValue) == 'Vídeo indisponível'){
                    //             #setVideoIndisponiovel($slug_video);

                    //             #}
                    //         }

                    //         #Video Indisponivel
                    //         if ($div->getAttribute('class') == 'promo-title style-scope ytd-background-promo-renderer') {
                    //             echo '...............', $div->nodeValue;
                    //             #if(trim($div->nodeValue) == 'Vídeo indisponível'){
                    //             #setVideoIndisponiovel($slug_video);
                    //             #}
                    //         }
                    //     }


                        // <div class="yt-spec-button-shape-next__button-text-content">
                        // <span class="yt-core-attributed-string yt-core-attributed-string--white-space-no-wrap" role="text">Mostrar transcrição</span>
                        // </div>

                        ###################################### transcript
                        #$browser->driver->findElement(WebDriverBy::xpath('//div[@id="menu"]//ytd-menu-renderer//yt-button-shape[@id="button-shape" and @version="modern"]//button[@aria-label="Mais ações"]'))->click();

                        ############################################# esses 3 sao do antigo
                        #$driver->findElement(WebDriverBy::xpath('//div[@id="menu"]//ytd-menu-renderer//yt-button-shape[@id="button-shape" and @version="modern"]//button[@aria-label="Mais ações"]'))->click();
                        #$driver->findElement(WebDriverBy::xpath('//ytd-menu-service-item-renderer[@tabindex="-1"]'))->click();
                        #$transcript = $driver->findElement(WebDriverBy::xpath('//ytd-transcript-segment-list-renderer//div[@id="segments-container"]'));
                        ##############################################
                        #$transcript = $browser->driver->findElement(WebDriverBy::xpath('//ytd-transcript-segment-list-renderer//div[@id="segments-container"]'));


                        #$html = $transcript->getDomProperty('innerHTML');
                        // if (!empty($html)) {
                        //     mb_convert_encoding("\x5A\x6F\xEB", 'UTF-8', 'ISO-8859-1');


                        //     $doc->loadHTML(utf8_decode($html));
                        //     $ytsr_s = $doc->getElementsByTagName('ytd-transcript-segment-renderer');

                        //     foreach ($ytsr_s as $i => $y) {
                        //         $divs = $y->getElementsByTagName('div');
                        //         foreach ($divs as $div) {
                        //             if ($div->getAttribute('class') == 'segment-timestamp style-scope ytd-transcript-segment-renderer') {
                        //                 $tempo = trim($div->nodeValue);
                        //                 $legendas[$i]['tempo'] = $tempo;
                        //                 break;
                        //             }
                        //         }
                        //         $yfs_s = $y->getElementsByTagName('yt-formatted-string');
                        //         foreach ($yfs_s as $y) {
                        //             if ($y->getAttribute('class') == 'segment-text style-scope ytd-transcript-segment-renderer') {
                        //                 $texto = trim($y->nodeValue);
                        //                 if (!isset($legendas[$i]['tempo'])) {
                        //                     echo "Erro ao inserir legenda $i";
                        //                 } else {
                        //                     $legendas[$i]['texto'] = $texto;
                        //                 }
                        //                 break;
                        //             }
                        //         }
                        //     }
                        // }
                    // } catch (\Exception $e) {
                    //     echo $e->getMessage();
                    // }



                    // $transcript = '';
                    // $duration = 0;
                    // if (!empty($legendas)) {

                    //     $transcript = implode(' ', array_column($legendas, 'texto'));
                    //     var_dump($transcript);
                    //     $tempo_total = array_pop($legendas)['tempo'];
                    //     var_dump($tempo_total);
                    //     $duration = (int) $this->timeToSeconds($tempo_total);
                    // }


                    ####################################### fim transcript

                    try {
                        #sleep(5);
                        #$contents = $browser->driver->findElement(WebDriverBy::id($id_div));
                        dd($id_div);
                        $contents = $browser->element($id_div);
                    } catch (\Exception $e) {
                        echo "\n\n----- nao achou o id below no HTML " . $e->getMessage();
                        continue;
                    }

                    $end_time = microtime(true);
                    $tempo = ($end_time - $start_time);
                    dd($contents);
                    $html = $contents->getDomProperty('innerHTML');
                } else {
                    echo "<h1>Interface Web</h1>";
                    $html = file_get_contents('storage/app/public/html_youtube/detalhe.html');
                    $html = trim($html);
                    $tempo = 0;
                    #echo $html;
                }
                $doc->loadHTML(utf8_decode($html));



                $h1s = $doc->getElementsByTagName('h1');
                if ($h1s->count() > 0) {
                    $h1 = $doc->getElementsByTagName('h1')->item(0);
                    $nome_video = $h1->getElementsByTagName('yt-formatted-string')->item(0)->nodeValue;
                } else {
                    echo "\nNao pegou o nome do video - checar mais tarde $slug_video";
                    continue;
                }

                $as = $h1->getElementsByTagName('a');
                $hashtags = [];
                foreach ($as as $a) {
                    $hashtags[] = $a->nodeValue;
                }


                #view-count style-scope ytd-video-view-count-renderer
                if ($views == 0) {
                    $ys = $doc->getElementsByTagName('ytd-video-view-count-renderer');

                    if ($ys instanceof \DOMDocument) {
                        $v = $ys->item(0)->getElementsByTagName('span')->item(0)->nodeValue;
                        $views = preg_replace('/[^0-9]/', "", $v);
                    }
                }


                $divs = $doc->getElementsByTagName('div');
                foreach ($divs as $div) {
                    if ($div->getAttribute('id') == 'info-strings') {
                        $dt_video = $div->getElementsByTagName('yt-formatted-string')->item(0)->nodeValue; #data
                        #var_dump($dt_video);die;
                    }



                    if ($div->getAttribute('id') == 'menu') {
                        $yfs = $div->getElementsByTagName('yt-formatted-string');
                        foreach ($yfs as $aa => $y) {
                            if ($y->getAttribute('id') == 'text' && $y->getAttribute('class') == 'style-scope ytd-toggle-button-renderer style-text') {
                                if ($y->hasAttribute('aria-label')) {
                                    $txt = $y->getAttribute('aria-label');
                                    $likes = preg_replace('/[^0-9]/', "", $txt);
                                    break;
                                }
                            }
                        }
                    }

                    if ($likes == 0) {
                        #likes secundario
                        if ($div->getAttribute('class') == 'cbox yt-spec-button-shape-next--button-text-content') {
                            $spans = $div->getElementsByTagName('span');
                            #echo "<h1>aaa</h1>";
                            foreach ($spans as $span) {
                                if ($span->getAttribute('class') == 'yt-core-attributed-string yt-core-attributed-string--white-space-no-wrap' && $span->hasAttribute('role')) {
                                    $txt = $span->nodeValue;
                                    if (preg_match('/([\d|,|\.]+)(\s|&nbsp;)?(k|K|M|mil|mi|m|bi|B)?/', $txt, $m)) {
                                        #var_dump($m);
                                        $likes = $txt;
                                        break;
                                    }
                                    #echo "<hr>$likes<hr>";
                                }
                            }
                        }
                    }
                }



                if ($comments == 0) {
                    $h2s = $doc->getElementsByTagName('h2'); #comments
                    foreach ($h2s as $h2) {
                        if ($h2->getAttribute('id') == 'count' && $h2->getAttribute('class') == 'style-scope ytd-comments-header-renderer') {
                            $spa = $h2->getElementsByTagName('span');
                            $c = trim($spa->item(0)->nodeValue);
                            $comments = preg_replace('/[^0-9]/', "", $c);
                            break;
                        }
                    }
                }




                $yas = $doc->getElementsByTagName('yt-attributed-string');
                foreach ($yas as $y) {
                    if (empty($content)) {                
                        #if ($y->getAttribute('class') == 'content style-scope ytd-video-secondary-info-renderer') {
                        if ($y->getAttribute('class') == 'content style-scope ytd-expandable-video-description-body-renderer') {
                            $content = squish($y->nodeValue); #content
                        }
                    }

                }

                $nome_canal=$slug_canal=0;
                $yfs = $doc->getElementsByTagName('yt-formatted-string');
                foreach ($yfs as $y) {
                    if (!isset($slug_canal)) {
                        if ($y->getAttribute('id') == 'text' && $y->getAttribute('class') == 'style-scope ytd-channel-name') {
                            if($nome_canal == 0){
                                $nome_canal = $y->getAttribute('title');
                            }
                            $as = $y->getElementsByTagName('a');
                            #dd($as);
                            if ($as->count() > 0) {
                                #$nome_canal = $as->item(0)->nodeValue; #nome canal /watch?v=fqLgAu3UR08&list=PLrOD4-Gf9PHHloJzp13mZRIRj_fLYxvNh
                                $slug_canal = urldecode($as->item(0)->getAttribute('href')); #link slug canal
                                #break;
                                #dump($slug_canal);

                            } else {
                                echo "nao pegou canal";
                            }
                        }
                    }

                    // if (empty($content)) {                
                    //     #if ($y->getAttribute('class') == 'content style-scope ytd-video-secondary-info-renderer') {
                    //     if ($y->getAttribute('class') == 'content style-scope ytd-expandable-video-description-body-renderer') {
                    //         $content = $y->nodeValue; #content
                    //     }
                    // }

                    if (empty($hashtags)) {
                        if ($y->getAttribute('class') == 'super-title style-scope ytd-video-primary-info-renderer') {
                            $as = $y->getElementsByTagName('a'); #todos os links a
                            $hashtags = [];
                            foreach ($as as $a) {
                                $hashtags[] = $a->nodeValue;
                            }
                        }
                    }
                }

                $likes = $this->parseNumLikes($likes);

                $dt_video = $this->parseDataUploadVideo($dt_video);
                $hashtags = json_encode($hashtags);

                #atencao slug_video tem identificador -- Ke--lWv3q6A
                # transcript e duration tao saindo
                $res = compact('tempo', 'nome_video', 'views', 'dt_video', 'comments', 'nome_canal', 'slug_canal', 'content', 'hashtags', 'likes');

                dd($res);

                // $limpo = array_map('formataString', $res);
                // $limpo['slug_video'] = $slug_video;
                // #print_r($limpo);
                // extract($limpo); #so pra liberar p causa da funcao de limpastr
                // $upsert_id = saveDetalhes($slug_video, $nome_video, $views, $dt_video, $comments, $nome_canal, $slug_canal, $content, $hashtags, $likes, $transcript, $duration);
                // var_dump($upsert_id);
                // showMessageUpsertRow($upsert_id, $slug_video);

                
            }

            echo "\n\n\FIMMMMMMMMMMMMM";
        });
    }



    function timeToSeconds(string $time): int
    {
        $arr = explode(':', $time);
        if (count($arr) === 3) {
            return $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
        }
        return $arr[0] * 60 + $arr[1];
    }


    function parseNumLikes(string $likes)
    {
        #var_dump($likes);

        $likes = strtolower(trim($likes));
        if (preg_match('/([\d|,|\.]+)\s?(k|K|M|mil|mi|m|bi|B)?/', $likes, $m)) {
            #print_r($m);
            $num = $m[1];
            $num = str_replace(',', '.', $num);
            #$txt = $m[2]??'sem'; deu pau aqui
        } elseif (preg_match('/[\d\.,]/', $likes, $m)) { #no caso de so nums ou 23.344 ou 23,344
            $novo_likes = preg_replace('/[^\d]/', "", $likes);
            return $novo_likes;
        } else {
            echo "\n\n ************************** Atencao checar isso em parseNumLikes : $likes";
        }
        $x = 1;
        if (str_contains($likes, 'mil') || str_contains($likes, 'k')) {
            $x = 1000;
        } elseif (str_contains($likes, 'mi') || str_contains($likes, 'm')) {
            $x = 1000000;
        }

        $res = $num * $x;
        #echo "\n\nparseNumLikes likes:$likes , res:$res ; x:$x";
        #echo $res;die;
        return $res;
    }

    function parseDataUploadVideo(string $dt_video): string|bool
    {
        #echo $dt_video;
        if (preg_match('/([\d]{1,2})?\s?de\s?(.+)\s?de\s?([\d]{4})?/s', $dt_video, $m)) {
            $dia = $m[1];
            $mes = $m[2];
            $ano = $m[3];
        } elseif (preg_match('/([a-zA-Z]{3,})\s?([\d]{1,2}),(\s+)?([\d]{4})/', $dt_video, $m)) {
            #Sep 24, 2013
            $dia = $m[2];
            $mes = $m[1];
            $ano = $m[4];
        } else {
            echo "\n\n<br>parseDataUploadVideo passou batido : $dt_video<hr>";
            return false;
        }

        $mes = retornaMes($mes);
        $nova_dt = sprintf("%d/%d/%d", $dia, $mes, $ano);
        #echo $nova_dt;
        return $nova_dt;
    }
}
