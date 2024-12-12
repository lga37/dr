<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Comentario;
use Livewire\WithPagination;
use Termwind\Components\Dd;
use Livewire\Attributes\Layout;

use App\Models\Video as ModelsVideo;
use Alaouy\Youtube\Facades\Youtube;
use App\Models\Busca;
use Illuminate\Support\Facades\Process;

class Video extends Component
{

    use WithPagination;

    public $perPage = 10;
    public $search = '';
    public $sortDirection = 'ASC';
    public $sortColumn = 'id';

    public int $busca_id = 0;
    public $out;

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



    function Url($id)
    {
        if (is_array($id)) {
            $busca_ids = implode(" ", $id);
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

    function nlp1($id)
    {
        $video = ModelsVideo::find($id);
        $txt = $video->nome;

        #dd($txt);
        #Username: voipgus@gmail.com
        #Password: sz..12
        #2528-J74nA8GU
        #ESipI0YPYdNI86Y0UAFY/tXYeCTn/9OiZFJS

        $url = 'https://api.gotit.ai/NLU/v1.5/Analyze';
        $basic = env('GOTIT_API_KEY') . ':' . env('GOTIT_SECRET_KEY');

        $data_array = [];
        $data_array["T"] = $txt;
        $data_array["S"] = true; #sentim
        $data_array["EM"] = true; #emocoes
        #$data_array["E"] = true; #PERSON LOCATION ORGANIZATION DATE EVENT CONSUMER_GOOD OTHER UNKNOWN
        $post_fields = json_encode($data_array);

        $headers =  [
            "Content-type: application/json",
            "Authorization: Basic " . base64_encode($basic),
        ];

        $result = $this->cUrlGetData($url, $post_fields, $headers);
        #echo "<pre>";
        #print_r($res['sentiment']['score']);
        #print_r($res['emotions']);

        $res = json_decode($result, true);

        #dd($res);

        if (!is_array($res)) {
            return;
        }
        $score = round($res['sentiment']['score'], 2);
        // $b = array_merge($a, $res['emotions']);

        $video->update(['nlp1' => $score]);

        #dd($comm);
        #die;
    }

    function nlp2($id)
    {
        $video = ModelsVideo::find($id);
        $txt = $video->caption;

        #dump($txt);

        $url = 'https://api.gotit.ai/NLU/v1.5/Analyze';
        $basic = env('GOTIT_API_KEY') . ':' . env('GOTIT_SECRET_KEY');

        $data_array = [];
        $data_array["T"] = $txt;
        $data_array["S"] = true; #sentim
        $data_array["EM"] = true; #emocoes
        #$data_array["E"] = true; #PERSON LOCATION ORGANIZATION DATE EVENT CONSUMER_GOOD OTHER UNKNOWN
        $post_fields = json_encode($data_array);

        $headers =  [
            "Content-type: application/json",
            "Authorization: Basic " . base64_encode($basic),
        ];

        $result = $this->cUrlGetData($url, $post_fields, $headers);
        #echo "<pre>";
        #print_r($res['sentiment']['score']);
        #print_r($res['emotions']);

        $res = json_decode($result, true);

        #dd($res);

        if (!is_array($res)) {
            return;
        }
        $score = round($res['sentiment']['score'], 2);
        // $b = array_merge($a, $res['emotions']);

        $video->update(['nlp2' => $score]);

        #dd($comm);
        #die;
    }

    function cUrlGetData($url, $post_fields = null, $headers = null)
    {

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);

        if (!empty($post_fields)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);
        return $data;
    }

    public function Gpt($id)
    {
        $video = ModelsVideo::find($id);
        $caption = $video->caption;
        $nome = $video->nome;

        $prompt = "Vou te dar a transcricao de um video no YouTube cujo titulo é : $nome. 
        Eu quero que voce classifique se a narrativa do video representa uma das 5 possiveis ideologias politicas.
        Eu gostaria que voce me entregasse os dados em formato json, com a seguinte chave: ideologia e o correspondente valor.
        1) Esquerda Radical = ER
        2) Esquerda = E
        3) Centro = C
        4) Direita = D
        5) Direita Radical = DR
        Caso nao consiga solucionar, voce deve responder com o caracter X.
        Nao e necessario justificar, apenas informe um do 5 possiveis resultados ou X. Use o formato JSON com a chave: ideologia e o valor correspondente.
        A transcrição do video é essa :" . $caption;
        $res = $this->getChatGptFromText($prompt);

        #dd($res);
        $gpt = $res['ideologia'];
        $video->update(['gpt' => $gpt]);

    }


    function getChatGptFromText($prompt)
    {

        $url = 'https://api.openai.com/v1/chat/completions';
        $key = env('OPENAI_API_KEY');
        $httpHeaders = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $key,
        ];

        $res = $this->curl2($url, $httpHeaders, $prompt, 'POST');

        #dump($res);
        $json = json_decode($res, true);
        #$completion = $json->choices[0]->message->content;
        if (isset($json['choices'][0]['message']['content'])) {
            $completion = $json['choices'][0]['message']['content'];
            $res = json_decode($completion, true);
            #dd($resp);
            return $res;
        } else {
            return false;
        }
    }


    function curl2($url, $httpHeaders = [], $prompt = '', $verb = 'GET')
    {

        $curl = curl_init();

        if (!empty($prompt) && $verb == 'POST') {
            $post_fields = [
                "model" => "gpt-3.5-turbo",
                "messages" => [
                    [
                        "role" => "user",
                        "content" => $prompt
                    ]
                ],
                #"max_tokens" => 12,
                #"temperature" => 0
            ];
            $postFields = json_encode($post_fields);
        } else {
            $postFields = null;
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $verb,
            CURLOPT_POSTFIELDS => $postFields,

            CURLOPT_HTTPHEADER => $httpHeaders,

            CURLOPT_HEADER         => false,            // don't return headers
            CURLOPT_FOLLOWLOCATION => true,             // follow redirects
            CURLOPT_ENCODING       => '',               // handle all encodings
            CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)',      // who am i
            CURLOPT_AUTOREFERER    => true,             // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 30,              // timeout on connect
            CURLOPT_TIMEOUT        => 30,              // timeout on response
            CURLOPT_MAXREDIRS      => 5,                // stop after 5 redirects

        ]);

        $res = curl_exec($curl);
        #dump($res);

        if (!curl_errno($curl)) {
            switch ($httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
                case 200:  # OK
                    break;
                default:
                    echo 'Unexpected HTTP code: ', $httpcode, "\n";
            }
        }
        curl_close($curl);
        return ($httpcode >= 200 && $httpcode < 300) ? $res : false;
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

                $comment = compact('user', 'texto', 'dt', 'likes', 'video_id'); #esse video_id aqui ta no formato do yt
                $comments[] = $comment;
            }
        }

        $video = ModelsVideo::where('cod', $cod)->first();

        #dd($video); 
        $tot = 0;
        foreach ($comments as $comentario) {
            extract($comentario); #video_id texto user likes dt
            $video_id = $video->id;
            if (!Comentario::where('video_id', $video_id)->where('texto', $texto)->where('user', $user)->where('dt', $dt)->exists()) {
                Comentario::create(compact('video_id', 'texto', 'user', 'likes', 'dt'));
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

        #dump($vs);
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
            #$caption = $contentDetails['caption']; #nao vem na api


            $dados = compact('nome', 'desc', 'dt', 'lang', 'categ_id', 'views', 'likes', 'favorites', 'comments', 'duration');
            $videos[$i] = $dados;
            #dump($dados);

            $res = ModelsVideo::find($id_na_tabela_videos);
            #dump($res);
            if ($atualizou = $res->update($dados)) {
                $tot++;
            }
            #dump($atualizou);
            #dump($res);
            #dump('------------------------');
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
        #dump($data);
        $all[] = $data;

        if (isset($data['nextPageToken'])) {
            #dump($data['nextPageToken']);
            if ($data['nextPageToken'] != NULL) {
                $pageToken = $data['nextPageToken'];
                $this->getAllComments($videoId, $pageToken, $maxResults);
            }
        }

        curl_close($ch);

        #dd($all);
        return $all;
    }



    #[Layout("layouts/app")]
    public function render()
    {

        $videos = ModelsVideo::search($this->search)

            ->when($this->busca_id > 0, function ($q) {
                return $q->where('busca_id', $this->busca_id);
            })

            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        $buscas = Busca::all();

        #dd($buscas);

        return view('livewire.video', [
            'videos' => $videos,
            'buscas' => $buscas,
        ]);
    }
}
