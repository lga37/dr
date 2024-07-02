<?php

namespace Tests\Browser;

use App\Models\Canal;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class vidiqTest extends DuskTestCase
{

    #protected static $url = 'https://youtube.com/';
    const PROD = true;


    /** @test */
    public function urlSpider()
    {

        $this->browse(function (Browser $browser) {

            $canais = Canal::whereNotNull('youtube_id')->whereNull('min')->pluck('youtube_id')->toArray();

            #dd($canais);
            #$canais = ["UCrp0zmecZ3TNV8FSR-tjv7A", "UCLTWPE7XrHEe8m_xAmNbQ-Q"];
            #$canais = ["UCLTWPE7XrHEe8m_xAmNbQ-Q",];
            $res = [];
            foreach ($canais as $k => $youtube_id) {

                if (self::PROD) {
                    $url = "https://vidiq.com/youtube-stats/channel/$youtube_id/";

                    try {

                        $browser->visit($url);
                        $browser->maximize()->fitContent();
                        #sleep(3);

                        #dd($browser);
                        #$browser->driver->findElement(WebDriverBy::xpath('/html/body/div[1]/div/section/div[3]/div[2]/div/table/tbody/tr['.$i.']/td[2]'))!==null) 
                        #$browser->driver->findElements(WebDriverBy::xpath('//*[@class="selectable"][@class="custom-attribute"]'));
                        #$browser->keys('textarea[data-auto="notes"]', 'some notes');

                        #pega tb a desc
                        $rs = $browser->elements('.css-b4e621');#->getDomProperty('innerHTML');
                        #dd($rs);
                        $dt = $local = $categ = $videos = $desc = null;
                        $score = $inscritos = $views = $earns = $min = $max = $engagement = $frequency = $length = null;

                        foreach ($rs as $k=>$r) {
                            
                            $html = $r->getDomProperty('innerHTML');
                            $re = '<p class="mb-0 text-right text-white">(.+?)<\/p>';
                            if(preg_match_all("|". $re ."|",$html,$out)){
                                if(!empty($out)){
                                    $dt = $out[1][0];
                                    $local = $out[1][1];
                                    $categ = $out[1][2];
                                    $videos = $out[1][3];
                                    
                                }

                            }
                            #subscribers ja tem

                            #pegar a desc
                            $re = '<\/h2><p class="scrollbar-hide mb-4 h-full text-ellipsis text-sm font-medium text-white transition-all duration-150 ease-in-out line-clamp-3 max-h-20">(.+?)<\/p>';
                            if(!isset($desc) && preg_match("|". $re ."|",$html,$out)){
                                $desc = $out[1];
                                #dump('descccccccccccccccccccccccc:'.$desc);

                            }

                            #echo "\n\n\n";
                        }
                        #dd($r);
                        echo "\n\n\n\n ------------------------ 2 parte ---------------------- \n\n\n";
                        ################## 2 parte ######################################
                        $rs = $browser->elements('.h-full .css-nb30dx'); #->getDomProperty('innerHTML');
                        foreach ($rs as $k=>$r) {

                            $html = $r->getDomProperty('innerHTML');

                            $re = '<p class="mb-1 text-sm font-bold">Overall Score:<\/p>(.+?)<span value="(.+?)" (.+?)<\/span><\/p>';
                            if(!isset($score) && preg_match("/". $re ."/",$html,$out)){
                                $score = $out[2];
                                #dump($score);
                            }

                            $re = '<span>Subscribers<\/span>(.+?)<\/p><\/div><\/div><\/div><\/div><\/div><\/div><p class="mb-1 text-xl font-extrabold text-white lg:text-\[26px] lg:leading-\[30px]">(.+?)<\/p>';
                            if(!isset($inscritos) && preg_match("/". $re ."/",$html,$out)){
                                $inscritos = $out[2];
                                #dump($inscritos);
                            }


                            $re = '<span>Video Views<\/span> <div class="group">(.+?)<p class="mb-1 text-xl font-extrabold text-white lg:text-\[26px] lg:leading-\[30px]">(.+?)<\/p>';
                            if(!isset($views) && preg_match("/". $re ."/",$html,$out)){
                                $views = $out[2];
                                #dump($views);
                            }

                            $re = '<span>Est. Monthly Earnings<\/span>(.+?)<p class="mb-1 text-xl font-extrabold text-white lg:text-\[26px] lg:leading-\[30px]">(.+?) - (.+?)<\/p>';
                            if(!isset($earns) && preg_match("/". $re ."/",$html,$out)){
                                $min = $out[2];
                                $max = $out[3];
                                // if(isset($min,$max)){
                                //     $earns = "converter";

                                // }
                                #dump($min,$max,$earns);
                            }

                            $re = '<span>Engagement Rate<\/span> <div class="group">(.+?)<p class="mb-1 text-xl font-extrabold text-white lg:text-\[26px] lg:leading-\[30px]">(.+?)<\/p>';
                            if(!isset($engagement) && preg_match("/". $re ."/",$html,$out)){
                                $engagement = $out[2];
                                #dump($engagement);
                            }

                            $re = '<span>Video Upload Frequency<\/span> <div class="group">(.+?)<p class="mb-1 text-xl font-extrabold text-white lg:text-\[26px] lg:leading-\[30px]">(.+?)<\/p>';
                            if(!isset($frequency) && preg_match("/". $re ."/",$html,$out)){
                                $frequency = $out[2];
                                #dump($frequency);
                            }

                            $re = '<span>Average Video Length<\/span> <div class="group">(.+?)<p class="mb-1 text-xl font-extrabold text-white lg:text-\[26px] lg:leading-\[30px]">(.+?)<\/p>';
                            if(!isset($length) && preg_match("/". $re ."/",$html,$out)){
                                $length = $out[2];
                                #dump($length);
                            }
                        }

                        #$res = compact('dt','local','categ','videos','desc','score','inscritos','views','min','max','engagement','frequency','length');
                        #dump($res);
                        
                        $dt = parseDataUploadVideo($dt);
                        $videos = return_kmb_to_integer($videos);
                        $inscritos = return_kmb_to_integer($inscritos);
                        $views = return_kmb_to_integer($views);
                        $min = return_kmb_to_integer($min);
                        $max = return_kmb_to_integer($max);
                        $engagement = retorna_float($engagement);
                        $frequency = retorna_float($frequency);
                        $length = retorna_float($length);

                        $res = compact('dt','local','categ','videos','desc','score','inscritos','views','min','max','engagement','frequency','length');
                        #dd($res);

                        $canal = Canal::where('youtube_id', $youtube_id)->first();
                        #dd($canal);
                        $res = $canal->update($res);
                        if($res){
                            dump('canal '. $youtube_id .' atualizado OK');
                        }
                        
                    } catch (\Exception $e) {
                        echo $e->getFile() . "\n erro: " . $e->getMessage() .' linha:'. $e->getLine();
                        continue;
                    }

                    # div com grid grid-flow-row grid-cols-2 gap-4 lg:grid-cols-[0.5fr_0.6fr_0.7fr_1fr] lg:gap-0

                } else {
                    $doc = new \DOMDocument('1.0', 'utf-8');
                    libxml_use_internal_errors(true);
                    $doc->validateOnParse = true;
                    $doc->preserveWhiteSpace = false;

                    $html = file_get_contents('storage/app/public/html_youtube/meta.html');
                    $html = trim($html);
                    $doc->loadHTML(utf8_decode($html));
                }

                echo "\n-----------------============ fimmm vidiqqqqq";
            }
        });
    }
}
