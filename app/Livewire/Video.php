<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Comentario;
use Livewire\WithPagination;
use Termwind\Components\Dd;
use Livewire\Attributes\Layout;

use App\Models\Video as ModelsVideo;
use Alaouy\Youtube\Facades\Youtube;
use Illuminate\Support\Facades\Process;

class Video extends Component
{

    use WithPagination;

    function getInfo($slug){
        $result = Process::path("/var/www/dr")->timeout(0)
        ->run('/usr/bin/php artisan dusk --without-tty --filter videoTest');
        $res = $result->exitCode() === 0? $result->output() : $result->errorOutput();
        
        
        dump('result:'. $res);

    }


    public function getComments($slug){


        #/watch?v=DSOMRi1HJeQ&pp=ygUGZHJvZ2Fz

        if (preg_match('![?&]{1}v=([^&]+)!', $slug . '&', $m)){
            $video_id = $m[1];
        }
        
        #dd($slug);
        #dd($video_id);

        $res = $this->getAllComments($video_id,null,100); #max e 100 mesmo
      
        $comments = [];
        foreach($res as $comment['items']){
            foreach($comment['items']['items'] as $c){
                #dump($c['snippet']['topLevelComment']['snippet']);
                $user = $c['snippet']['topLevelComment']['snippet']['authorDisplayName']??'qq coisa - tirar';
                $texto = $c['snippet']['topLevelComment']['snippet']['textDisplay'];
                $dt = $c['snippet']['topLevelComment']['snippet']['publishedAt'];

                $dt = date('Y-m-d h:i:s', strtotime($dt));

                $likes = $c['snippet']['topLevelComment']['snippet']['likeCount'];
                
                $comment = compact('user','texto','dt','likes','video_id');
                $comments[] = $comment;

            }
        }

        $video = \App\Models\Video::where('slug',$slug)->first();
        
        #dd($video); 
        $tot = 0;
        foreach($comments as $comentario){
            $c = new Comentario($comentario);
            $r = $video->comentarios()->save($c);
            #dump($r);
            if($r){
                $tot++;
            }
        }        
        session()->flash('status', $tot . ' comentarios adicionados para '.$slug);

            

        #dd($comments);
        
        // id bigint, autoincrement .................................................................................. bigint unsigned  
        // user varchar, utf8mb4_unicode_ci ............................................................................. varchar(255)  
        // texto text, utf8mb4_unicode_ci ....................................................................................... text  
        // likes int, nullable .......................................................................................... int unsigned  
        // dislikes int, nullable ....................................................................................... int unsigned  
        // dt timestamp .................................................................................................... timestamp  
        // perspective double, nullable ....................................................................................... double  
        // video_id bigint ........................................................................................... bigint unsigned  
        // created_at timestamp, nullable .................................................................................. timestamp  
        // updated_at 


// "channelId" => "UCugK_9W-5o56H0pEnJtFQnw"
//   "videoId" => "5BtnVGJfqkU"
//   "textDisplay" => "
// Ninguém parece ver o lado positivo nesta PL fracassada.    Esta é a primeira vez que a bancada religiosa admite o aborto legal e tenta regularizá-lo por lei. Ag
//  ▶
// "
//   "textOriginal" => "
// Ninguém parece ver o lado positivo nesta PL fracassada.    Esta é a primeira vez que a bancada religiosa admite o aborto legal e tenta regularizá-lo por lei. Ag
//  ▶
// "
//   "authorDisplayName" => "@smart-ytvideos"
//   "authorProfileImageUrl" => "https://yt3.ggpht.com/ytc/AIdro_nsIJXHiaFSVjTQWLbfP3gqwwzrwVbSBHhHO4JNkb0=s48-c-k-c0x00ffffff-no-rj"
//   "authorChannelUrl" => "http://www.youtube.com/@smart-ytvideos"
//   "authorChannelId" => array:1 [▶]
//   "canRate" => true
//   "viewerRating" => "none"
//   "likeCount" => 0
//   "publishedAt" => "2024-06-19T15:55:14Z"
//   "updatedAt" => "2024-06-19T15:55:14Z"

        
    }



    public function getAllComments($videoId,$pageToken=null,$maxResults){
        $url = "https://www.googleapis.com/youtube/v3/commentThreads";

        static $all =[];
        $params =[
            'key' => env('YOUTUBE_API_KEY'),
            'part' => 'snippet',
            'maxResults' => $maxResults,
            'videoId' => $videoId,
            'pageToken' => $pageToken
        ];

        $call = $url.'?'.http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $call);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        
        $data = json_decode($output,true);
        $all[] = $data;
        // if(isset($data['nextPageToken'])){
        //     if($data['nextPageToken'] != NULL ){
        //         $pageToken = $data['nextPageToken'];
        //         $this->getAllComments($videoId,$pageToken,$maxResults);
        //     }
        // }
        curl_close($ch);
        return $all;


    }




    #[Layout("layouts/app")]
    public function render()
    {
       
        return view('livewire.video',[
            'videos'=>ModelsVideo::paginate(30),
        ]);
    }
}
