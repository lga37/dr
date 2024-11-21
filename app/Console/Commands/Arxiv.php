<?php

namespace App\Console\Commands;

use DateTime;
use App\Models\Canal;
use App\Models\Video;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use HeadlessChromium\Exception\OperationTimedOut;
use HeadlessChromium\Exception\ElementNotFoundException;

class Arxiv extends Bot
{

    protected $signature = 'arxiv {canal_ids?*} {--acao=craw}';


    public function craw(array $canal_ids)
    {


        $queries = Canal::whereIn('id', $canal_ids)->select('id', 'youtube_id')->get()->toArray();

        #dd($queries);
        foreach ($queries as $query) {
            $canal_id = $query['id'];
            $youtube_id = $query['youtube_id'];
            ######### api
            $url = "http://www.youtube.com/channel/$youtube_id";
            echo $url . "\n\n";

            ########### pra teste
            $canal = Canal::find($canal_id);
            $arxivs = $canal->arxivs;
            #dd($arxivs);
            foreach ($arxivs as $arx) {
                $ts = filtraDigitos($arx['ts']);
                $url_final = "http://web.archive.org/web/$ts/$url";
                echo $url_final . "\n\n";
                // http://web.archive.org/web/20220913021547/https://www.youtube.com/channel/UC_VZ-oF_pAgz-h8xVWXypaA

                try {
                    $this->initBrowser(true);
                    $page = $this->browser->createPage();
                    $page->navigate($url_final);

                    sleep(6);
                    $seletor = 'span.yt-subscription-button-subscriber-count-branded-horizontal.yt-uix-tooltip';
                    $elem = $page->dom()->querySelector($seletor);
                    $inscritos = $elem->getAttribute('title');
                    $inscritos = filtraDigitos($inscritos);
                    dd($inscritos);

                } catch (OperationTimedOut $e) {
                    echo 'OperationTimedOut : ' . $e->getMessage();
                } catch (ElementNotFoundException $e) {
                    echo '::::33333' . $e->getMessage();
                } catch (\Exception $e) {
                    echo 'Exception : ' . $e->getMessage();
                } finally {
                    $this->closeBrowser();
                }
            }

            dd('fimmmmmmmmmm teste');



            $site = "https://archive.org/wayback/available?url=$url";
            $out = file_get_contents($site);
            $res = json_decode($out, true);
            if (isset($res['archived_snapshots']['closest']['available']) && $res['archived_snapshots']['closest']['available'] == 'true') {
                $snaps = "http://web.archive.org/cdx/search/cdx?url=$url";
                echo "\n" . $snaps;
                $txt = file_get_contents($snaps);
                $re = '\s([\d]{12,})\s(.+?)\s';
                if (preg_match_all('/' . $re . '/', $txt, $res)) {

                    $canal = Canal::find($canal_id);

                    $tss = [];
                    foreach ($res[1] as $k => $ts) {
                        #eliminar a coluna dt pois o mysql ja insere no formato
                        #fazer o upsert
                        $date = DateTime::createFromFormat('YmdHis', $ts);
                        $dt = $date->format('Y-m-d');

                        $tss[] = ['dt' => $dt, 'ts' => $ts];
                    }

                    $canal->arxivs()->createMany($tss);
                } else {
                    echo "nao parseou";
                }

                ######## craw
                try {
                    $canal->fresh();
                    $arxivs = $canal->arxivs;

                    #dd($arxivs);
                    foreach ($arxivs as $arx) {
                        $url_final = "http://web.archive.org/web/$ts/$url";
                        // http://web.archive.org/web/20220913021547/https://www.youtube.com/channel/UC_VZ-oF_pAgz-h8xVWXypaA
                        $this->initBrowser(true);
                        $page = $this->browser->createPage();
                        $page->navigate($url_final);
                        sleep(6);
                    }
                } catch (OperationTimedOut $e) {
                    echo 'OperationTimedOut : ' . $e->getMessage();
                } catch (ElementNotFoundException $e) {
                    echo '::::33333' . $e->getMessage();
                } catch (\Exception $e) {
                    echo 'Exception : ' . $e->getMessage();
                } finally {
                    $this->closeBrowser();
                }
            } else {
                echo "\nnao tem arxiv para ele $url";
            }
        }
    }


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



    // org,eserver,tc)/ 20180515033912 http://tc.eserver.org:80/ text/html 302 RK36SX4X6VJ44FMUWDK4QYFPYGBYUJUH 404
    // org,eserver,tc)/ 20180716082607 http://tc.eserver.org:80/ text/html 302 RK36SX4X6VJ44FMUWDK4QYFPYGBYUJUH 405
    // org,eserver,tc)/ 20180915160723 http://tc.eserver.org:80/ text/html 302 RK36SX4X6VJ44FMUWDK4QYFPYGBYUJUH 404
    // org,eserver,tc)/ 20181014163006 http://tc.eserver.org/ warc/revisit - RK36SX4X6VJ44FMUWDK4QYFPYGBYUJUH 502
    // org,eserver,tc)/ 20181115172501 http://tc.eserver.org:80/ text/html 302 RK36SX4X6VJ44FMUWDK4QYFPYGBYUJUH 404
    // org,eserver,tc)/ 20181228210547 http://tc.eserver.org/ warc/revisit - RK36SX4X6VJ44FMUWDK4QYFPYGBYUJUH 500





    public function handle()
    {

        $canal_ids = $this->argument('canal_ids');
        $acao = $this->option('acao');

        if (method_exists($this, $acao)) {
            $this->{$acao}($canal_ids);
        } else {
            echo "Este metodo nao existe";
        }

        #$this->info('Processado com sucesso!');
        return self::SUCCESS;
    }
}
