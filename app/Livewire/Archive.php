<?php

namespace App\Livewire;

use App\Models\Archive as ModelsArchive;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Archive extends Component
{
    #[Layout("layouts/app")]
    public function render()
    {
       
        return view('livewire.archive',[
            'archives'=>ModelsArchive::all(),
        ]);
    }


    public function has($url):bool{

        $site = "https://archive.org/wayback/available?url=$url";

        
        // {
        //     "url": "http://tc.eserver.org/",
        //     "archived_snapshots": {
        //         "closest": {
        //             "status": "200",
        //             "available": true,
        //             "url": "http://web.archive.org/web/20180427130634/https://tc.eserver.org/",
        //             "timestamp": "20180427130634"
        //         }
        //     }
        // }

        return true;
    }

    private function doCurl($url, $verb='GET', $res='json', $params=[]){

        $call = $url.'?'.http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $call);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        
        $data = json_decode($output,true);
        return $data;
    }




    public function get($url): array{

        $url = "http://web.archive.org/cdx/search/cdx?url=$url";

        $txt = "";
        #parse pelo espaco
        $re = '"\s([\d]{12,})\s(.+?)\s"';
        preg_match_all('/'.$re.'/',$txt,$res);


        // org,eserver,tc)/ 20180515033912 http://tc.eserver.org:80/ text/html 302 RK36SX4X6VJ44FMUWDK4QYFPYGBYUJUH 404
        // org,eserver,tc)/ 20180716082607 http://tc.eserver.org:80/ text/html 302 RK36SX4X6VJ44FMUWDK4QYFPYGBYUJUH 405
        // org,eserver,tc)/ 20180915160723 http://tc.eserver.org:80/ text/html 302 RK36SX4X6VJ44FMUWDK4QYFPYGBYUJUH 404
        // org,eserver,tc)/ 20181014163006 http://tc.eserver.org/ warc/revisit - RK36SX4X6VJ44FMUWDK4QYFPYGBYUJUH 502
        // org,eserver,tc)/ 20181115172501 http://tc.eserver.org:80/ text/html 302 RK36SX4X6VJ44FMUWDK4QYFPYGBYUJUH 404
        // org,eserver,tc)/ 20181228210547 http://tc.eserver.org/ warc/revisit - RK36SX4X6VJ44FMUWDK4QYFPYGBYUJUH 500

    }

    public function parseAll(array $res){

        $inscritos = $this->parseOne($url,$ts);
    }
    
    public function parseOne($url, $ts){
        $url = "http://web.archive.org/web/$ts/$url";
        // http://web.archive.org/web/20220913021547/https://www.youtube.com/channel/UC_VZ-oF_pAgz-h8xVWXypaA

        #num de inscritos / ts
    }
    

}
