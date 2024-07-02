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
    
    public $perPage = 10;
    public $search = '';
    public $sortDirection = 'ASC';
    public $sortColumn = 'id';

    public function doSort($column){
        if($this->sortColumn == $column){
            $this->sortDirection = ($this->sortDirection=='ASC')? 'DESC':'ASC';
            return;
        }
        $this->sortColumn = $column;
        $this->sortDirection = 'ASC';
    
    }

    public function updatedSearch(){
        $this->resetPage();
    }

    public function del($id)
    {
        ModelsVideo::find($id)->delete();
        #dd($id);
    }



    function Url($slug){
        
        
        $result = Process::path("/var/www/dr")
        ->timeout(0)
        #->run('/usr/bin/php artisan dusk --without-tty --filter videoTest 123')
        ->run('/usr/bin/php artisan dusk --without-tty --filter videoTest::urlSpider')
        ;

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


        
    }


    public function API(){
        
        #atencao maximo de 50/request .... nao sei pq 
        $array_id_videoid = ModelsVideo::whereNull('views')->take(50)->pluck('slug','id')->map(function($slug) {
            #/watch?v=4KzsMcxA6Q8&pp=ygUGYWJvcnRv
            if (preg_match('/[?&]{1}v=([^&]+)/', $slug, $m)){
                $video_id = $m[1];
                return $video_id;
            }
            return false;
    
        })
        ->reject(function ($value) {
            return $value === false;
        })
        ->toArray();

        $url = "https://www.googleapis.com/youtube/v3/videos";

        $array_videoid_id = array_flip($array_id_videoid);
        
        #dump($array_videoid_id);

        $videos_id = array_values($array_id_videoid);

        
        $videos = implode(",",$videos_id);  

        #dd($videos);

        $params =[
            'order'=> 'date',
            'key' => env('YOUTUBE_API_KEY'),
            'part' => 'snippet,statistics,contentDetails',
            #'maxResults' => 100,
            'id' => $videos,
            #'pageToken' => $pageToken
        ];

        $call = $url.'?'.http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $call);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        $vs = json_decode($output,true);

        $coluna_vs = $vs['items'];
        
        $videos=[];
        $tot = 0;
        foreach($coluna_vs as $i=>$v) { 
            $video_id = $v['id'];
            $id_na_tabela_videos = $array_videoid_id[$video_id]; #atencao aqui, to so pegando de volta o id
            extract($v); # snippet,statistics,contentDetails
            $nome = $snippet['title'];
            $desc = $snippet['description'];
            $dt = $snippet['publishedAt'];
            $lang = $snippet['defaultLanguage']??null;
            $categ_id = $snippet['categoryId'];
            
            $views = $statistics['viewCount']??null;
            $likes = $statistics['likeCount']??null;
            $dislikes = $statistics['dislikeCount']??null;
            $favorites = $statistics['favoriteCount']??null;
            $comments = $statistics['commentCount']??null;
           
            $duration = $contentDetails['duration'];
            $caption = $contentDetails['caption'];

            $duration = ISO8601ToSeconds($duration);
            $dt = date('Y-m-d H:i:s', strtotime( $dt ) );

            #dd($dt);

            $dados=compact('nome','desc','dt','lang','categ_id','views','likes','dislikes','favorites','comments','duration','caption');
            $videos[$i]=$dados;
            #dump($dados);

            $res = ModelsVideo::find($id_na_tabela_videos);
            #dump($res);
            if($atualizou=$res->update($dados)){
                $tot++;
            }
            dump($atualizou);
            dump($res);
            dump('------------------------');
        }

        if($tot == count($array_id_videoid)){
            $msg = "Todos os $tot registros atualizados";
        } else {
            $msg = "Atualizados $tot registros, porem com ". count($array_id_videoid) ." no total";
        }

        #dd($videos);
        session()->flash('success', $msg);

        
        

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
            'videos'=>ModelsVideo::search($this->search)
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage),
        ]);

        
    }
}
