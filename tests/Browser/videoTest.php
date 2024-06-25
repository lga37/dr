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


            $ids = Video::all()->pluck('slug')->toArray();
           

            // dd($ids);
            foreach ($ids as $chave => $slug_video) {
                $slug_video = trim($slug_video,"/");
                echo "\n ============= INICIANDO $chave -> $slug_video";
                if (str_contains($slug_video, '/shorts/') || str_contains($slug_video, '&list=')) {
                    echo " ============= shorts/list : ";
                    #$id_div = "shorts-container";
                    continue;
               
                }
                $comments = $likes = $views = 0;
                $content = "";

                $url = "https://www.youtube.com/$slug_video";

                #dump($url);

                try {
                    $browser->visit($url)->maximize()->waitFor('#below');
                    #$browser->script('window.scrollTo(0,500);');

                    $content = $browser->element('div > #below');
                    $html = $content->getDomProperty('innerHTML');

                   
                    $re = 'com mais (.+?) pessoas';
                    if(preg_match("/". $re ."/",$html,$out)){
                        $likes = $out[1];
                        #dump($out);
                    }

                    dump($slug_video.' likes: '.$likes);

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