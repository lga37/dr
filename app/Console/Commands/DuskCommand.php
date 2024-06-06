<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Dusk\Console\DuskCommand as VendorDuskCommand;

use RuntimeException;

use Illuminate\Support\Facades\Process;



class DuskCommand extends VendorDuskCommand
{
    protected $signature = 'dusk2';

    protected $description = 'rodar youtube';


    public function handle()
    {

        if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
        $process = Process::path("/var/www/dr")->tty()->timeout(0)->run('php artisan dusk --filter buscaTest');
        #$process = Process::run('ls -la');
        dd($process->output());
        // $process->setTimeout(null);
        // $process->setWorkingDirectory("/var/www/dr");
        // if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));

        // #$process->setEnv(["STDOUT"=> "aaa"]);
        // $process->setTty($process->isTtySupported());
        // $process->run();
        // dd($process->getOutput());


        #$this->purgeConsoleLogs();

        // $options = [];
        // // This line checks if it is a direct call or if has been called from Artisan (we assume is Artisan, more checks can be added) 
        // if ($_SERVER['argv'][1] != 'dusk') {
        //     $filter = $this->input->getParameterOption('--filter');
        //     // $filter returns 0 if has not been set up 
        //     if ($filter) {
        //         $options[] = '--filter';
        //         $options[] = $filter;
        //         // note: --path is a custom key, check how I use it in Commands\CommunicationsTest.php 
        //         #$options[] = $this->input->getParameterOption('--path');
        //     }
        // } else {
        //     $options = array_slice($_SERVER['argv'], 2);
        // }

        // return $this->withDuskEnvironment(function () {
        //     $process = (new Process(['php', 'artisan', 'dusk', '--filter', 'buscaTest']))
        //         ->setTimeout(null)
        //         ->setWorkingDirectory("/var/www/dr")
        //         #->setPrefix($this->binary())
        //         #->setArguments($this->phpunitArguments($options))
        //         #->getProcess()
        //         ;

        //     // try {
        //     //     $process->setTty(true);

        //     // } catch (RuntimeException $e) {
        //     //     $this->output->writeln('Warning: ' . $e->getMessage());
        //     // }

        //     return $process->run(function ($type, $line) {
        //         $this->output->write($line);
        //     });
        // });
    }



    // #$process = new Process(['ls', '-lsa']);
    // $process = new Process(['php', 'artisan', 'dusk', '--filter', 'buscaTest']);
    // $process->setTimeout(null);
    // $process->setWorkingDirectory("/var/www/dr");
    // #$process->setTty(true);
    // $process->run();

    // // executes after the command finishes
    // if (!$process->isSuccessful()) {
    //     throw new ProcessFailedException($process);
    // }

    // echo $process->getOutput();


    // public function handle()
    // {
    //     $options = [];
    //     if($_SERVER['argv'][1] != 'dusk'){
    //         $filter = $this->input->getParameterOption('--filter');
    //         if($filter){
    //             $options[] = '--filter';
    //             $options[] = $filter;
    //             $options[] = $this->input->getParameterOption('--path');
    //         }
    //     } else {
    //         $options = array_slice($_SERVER['argv'],2);
    //     }
    //     return $this->withDuskEnvironment(function() use($options) {
    //         $process = (new Process($options))
    //         ->setTimeout(null)
    //         #->setPrefix($this->binary())
    //         #->setArguments($this->phpunitArguments($options))
    //         #->getProcess()
    //         ;

    //         try{
    //             $process->setTty(true);
    //         }catch(\Exception $e){
    //             $this->output->writeln('ccc');
    //         }
    //         return $process->run(function($type, $line) {
    //             $this->output->write($line);
    //         });
    //     });
    // }
}
