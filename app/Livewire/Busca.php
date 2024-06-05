<?php

namespace App\Livewire;

use App\Models\Busca as ModelsBusca;

use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Exception\ProcessFailedException;



use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Busca extends Component
{

    public $query;

    public function add($id){
        ModelsBusca::create(['q'=>$this->query,'slug'=>Str::slug($this->query)]);
        #dd($id);
    }

    public function del($id){
        ModelsBusca::find($id)->delete();
        #dd($id);
    }

    public function craw(){
        
        Artisan::call('dusk2');
        #Artisan::call($command, $params);
        $output = Artisan::output();
        dump($output);

        #$process = new Process(['ls', '-lsa'],"/var/www/dr");
        #$process = new Process(['php', 'artisan', 'dusk', '--filter', 'buscaTest'],"/var/www/dr");
        // $process = new Process(['php', 'artisan', 'dusk']);

        // $process->setWorkingDirectory("/var/www/dr");
        // #$process->resetProcessData();
        // $process->setTty(true);
        // #dd($process);
        // $process->setTimeout(0);
        // $process->run();

        // // executes after the command finishes
        // if (!$process->isSuccessful()) {
        //     throw new ProcessFailedException($process);
        // }

        // dd($process->getOutput());
        
        
        
        #Artisan::call('dusk2');
        #Artisan::call($command, $params);
        #$output = Artisan::output();
        #dump($output);

        #exec('php artisan dusk --filter buscaTest');
        // $process = new Process('php artisan dusk --filter buscaTest');
        // $process->setPTY(true);
        // $process->run();
    
        // if (!$process->isSuccessful()) {
        //     throw new ProcessFailedException($process);
        // }
    
        #echo '<pre>'.$process->getOutput();

    }

    #[Computed()]
    public function getAll(){
        return ModelsBusca::all();
    }

    #[Layout("layouts/app")]
    public function render()
    {
       
        return view('livewire.busca',[
            'buscas'=>$this->getAll(),
        ]);
    }

   
    
}
