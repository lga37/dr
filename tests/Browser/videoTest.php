<?php

namespace Tests\Browser;

use App\Models\Video;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class videoTest extends DuskTestCase
{

    /** @test */
    public function urlSpider()
    {
       
        $this->browse(function (Browser $browser) {


            $ids = Video::whereNull('likes')->pluck('slug')->toArray();
            #$canais = Canal::whereNotNull('youtube_id')->whereNull('min')->pluck('youtube_id')->toArray();


            // dd($ids);
            foreach ($ids as $chave => $slug_video) {
                $slug_video = trim($slug_video,"/");
                
                
                $comments = $likes = $views = $dt_upload = "";
                $content = "";

                $url = "https://www.youtube.com/$slug_video";

                dump($url);

                try {
                    $browser->visit($url)->maximize()->waitFor('#below');
                    #sleep(2);
                    $browser->script('window.scrollTo(0,500);');
                    #############################################################
                    $content = $browser->element('div > #below');
                    $html = $content->getDomProperty('innerHTML');

                    #dd($html);
                   
                    $re = 'com mais (.+?) pessoas';
                    if(preg_match("/". $re ."/",$html,$out)){
                        $likes = $out[1];
                        #dump($out);
                    }
                    dump($slug_video.' likes: '.$likes);

                    $re = '<span class="view-count style-scope ytd-video-view-count-renderer">(.+?) visualizações<\/span>';
                    if(preg_match("/". $re ."/",$html,$out)){
                        $views = $out[1];
                        #dump($out);
                    }
                    dump($slug_video.' views: '.$views);

                    

                    $re = '<yt-formatted-string class="style-scope ytd-video-primary-info-renderer">(.+?)<\/yt-formatted-string>';
                    if(preg_match("/". $re ."/",$html,$out)){
                        $dt_upload = $out[1];
                        #dump($out);
                    }
                    dump($slug_video.' dt_upload: '.$dt_upload);


                    $re = '<yt-formatted-string class="count-text style-scope ytd-comments-header-renderer"><span dir="auto" class="style-scope yt-formatted-string">(.+?)<\/span>';
                    if(preg_match("/". $re ."/",$html,$out)){
                        $comments = $out[1];
                        #dump($out);
                    }
                    dump($slug_video.' comments: '.$comments);


                    $dt_upload = parseDataUploadVideo($dt_upload);


                    #comments2
                    // $content = $browser->element('#leading-section');
                    // $html = $content->getDomProperty('innerHTML');
                    // dd($html);



                    $content = $browser->element('#description-inline-expander');
                    $html = $content->getDomProperty('innerHTML');
                    $re = '<span class="yt-core-attributed-string--link-inherit-color" dir="auto" style="color: rgb\(19, 19, 19\);">(.+)<\/span><\/span><\/yt-attributed-string>';
                    if(preg_match("/". $re ."/",$html,$out)){
                        $desc = $out[1];
                        #dump($out);
                    }
                    dump($slug_video.' desc: '.$desc);

                    $res = compact('likes','views','dt_upload','comments','desc');
                    dd($res);

                    #dd($html);

                    // $canal = Video::where('youtube_id', $youtube_id)->first();
                    // #dd($canal);
                    // $res = $canal->update($res);
                    // if($res){
                    //     dump('canal '. $youtube_id .' atualizado OK');
                    // }



                } catch (\Exception $e) {
                    echo "\n\n----- Timeout - pulou " . $e->getMessage();
                    continue;
                }

            }

            echo "\n\n\FIMMMMMMMMMMMMM";
        });
    }

}