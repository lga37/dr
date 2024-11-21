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

    public function doSort($column)
    {
        if ($this->sortColumn == $column) {
            $this->sortDirection = ($this->sortDirection == 'ASC') ? 'DESC' : 'ASC';
            return;
        }
        $this->sortColumn = $column;
        $this->sortDirection = 'ASC';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function del($id)
    {
        ModelsVideo::find($id)->delete();
        #dd($id);
    }

    public $out;


    function Url($id)
    {
        if(is_array($id)){
            $busca_ids = implode(" ",$id);    
        } else {
            $busca_ids = $id;

        }
        $acao = 'craw';
        $signature = '/usr/bin/php artisan video ' . $busca_ids . ' --acao=' . $acao;

        $res = Process::path("/var/www/dr")->timeout(0)->run($signature);

        #dd($res->output());
        $this->out = $signature . "\n\n" . $res->output();

        $this->stream(
            to: 'out',
            content: $this->out,
            replace: true,
        );

        return $this->out;
    }

    public function Gpt($id)
    {
        dd($id);
    }


    # colocar aqui a f de qdo vier erro e pq o dono do video desabilitou os comentarios
    public function getComments($cod)
    {

        if (preg_match('/[?]{1}v=([^&]+)/', $cod . '&', $m)) {
            $video_id = $m[1];
        }

        $res = $this->getAllComments($video_id, null, 100); #max e 100 mesmo

        $comments = [];
        #dd($res);
        foreach ($res as $comment['items']) {
            foreach ($comment['items']['items'] as $c) {
                #dump($c['snippet']['topLevelComment']['snippet']);
                $user = $c['snippet']['topLevelComment']['snippet']['authorDisplayName'] ?? 'qq coisa - tirar';
                $texto = $c['snippet']['topLevelComment']['snippet']['textDisplay'];
                $dt = $c['snippet']['topLevelComment']['snippet']['publishedAt'];

                $dt = date('Y-m-d h:i:s', strtotime($dt));

                $likes = $c['snippet']['topLevelComment']['snippet']['likeCount'];

                $comment = compact('user', 'texto', 'dt', 'likes', 'video_id');
                $comments[] = $comment;
            }
        }

        $video = ModelsVideo::where('cod', $cod)->first();

        #dd($video); 
        $tot = 0;
        foreach ($comments as $comentario) {
            $c = new Comentario($comentario);
            $r = $video->comentarios()->save($c);
            #dump($r);
            if ($r) {
                $tot++;
            }
        }
        session()->flash('status', $tot . ' comentarios adicionados para ' . $cod);
    }


    public function API($ids)
    {

        #atencao maximo de 50/request .... nao sei pq 
        $array_id_videoid = ModelsVideo::whereNull('comments')->take(50)->pluck('cod', 'id')->map(function ($cod) {
            #/watch?v=4KzsMcxA6Q8&pp=ygUGYWJvcnRv
            if (preg_match('/[?]{1}v=([^&]+)/', $cod, $m)) {
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


        $videos_sep_virgulas = implode(",", $videos_id);

        #dd($videos);

        $params = [
            'order' => 'date',
            'key' => env('YOUTUBE_API_KEY'),
            'part' => 'snippet,statistics,contentDetails',
            #'maxResults' => 100,
            'id' => $videos_sep_virgulas,
            #'pageToken' => $pageToken
        ];

        $call = $url . '?' . http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $call);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        $vs = json_decode($output, true);

        $coluna_vs = $vs['items'];

        $videos = [];
        $tot = 0;
        foreach ($coluna_vs as $i => $v) {
            $video_id = $v['id'];
            $id_na_tabela_videos = $array_videoid_id[$video_id]; #atencao aqui, to so pegando de volta o id
            extract($v); # snippet,statistics,contentDetails
            $nome = $snippet['title'];
            $desc = $snippet['description'];
            $dt = $snippet['publishedAt'];
            $dt = date('Y-m-d H:i:s', strtotime($dt));
            $lang = $snippet['defaultLanguage'] ?? null;
            $categ_id = $snippet['categoryId'];

            $views = $statistics['viewCount'] ?? null;
            $likes = $statistics['likeCount'] ?? null;
            #$dislikes = $statistics['dislikeCount'] ?? null; #nao tem mais dilikes
            $favorites = $statistics['favoriteCount'] ?? null;
            $comments = $statistics['commentCount'] ?? null;

            $duration = $contentDetails['duration'];
            $duration = ISO8601ToSeconds($duration);
            $caption = $contentDetails['caption'];


            $dados = compact('nome', 'desc', 'dt', 'lang', 'categ_id', 'views', 'likes', 'favorites', 'comments', 'duration', 'caption');
            $videos[$i] = $dados;
            #dump($dados);

            $res = ModelsVideo::find($id_na_tabela_videos);
            #dump($res);
            if ($atualizou = $res->update($dados)) {
                $tot++;
            }
            dump($atualizou);
            dump($res);
            dump('------------------------');
        }

        if ($tot == count($array_id_videoid)) {
            $msg = "Todos os $tot registros atualizados";
        } else {
            $msg = "Atualizados $tot registros, porem com " . count($array_id_videoid) . " no total";
        }

        #dd($videos);
        session()->flash('success', $msg);
    }






    public function getAllComments($videoId, $pageToken = null, $maxResults)
    {
        $url = "https://www.googleapis.com/youtube/v3/commentThreads";

        static $all = [];
        $params = [
            'key' => env('YOUTUBE_API_KEY'),
            'part' => 'snippet',
            'maxResults' => $maxResults,
            'videoId' => $videoId,
            'pageToken' => $pageToken
        ];

        $call = $url . '?' . http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $call);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);

        $data = json_decode($output, true);
        $all[] = $data;

        if(isset($data['nextPageToken'])){
            if($data['nextPageToken'] != NULL ){
                $pageToken = $data['nextPageToken'];
                $this->getAllComments($videoId,$pageToken,$maxResults);
            }
        }

        curl_close($ch);

        #dd($all);
        return $all;
    }




    #[Layout("layouts/app")]
    public function render()
    {


        return view('livewire.video', [
            'videos' => ModelsVideo::search($this->search)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate($this->perPage),
        ]);
    }
}
