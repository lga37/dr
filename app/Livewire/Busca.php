<?php

namespace App\Livewire;

use App\Models\Busca as ModelsBusca;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#use Symfony\Component\Process\Exception\ProcessFailedException;
#use Symfony\Component\Process\Process;

class Busca extends Component
{

    #[Rule('required|min:3')]
    public $query;

    public function add()
    {
        $this->validate();
        ModelsBusca::create(['q' => $this->query, 'slug' => Str::slug($this->query)]);
        session()->flash('success', 'Query added');

        $this->reset('query');
        #dd($id);
    }

    public function del($id)
    {
        ModelsBusca::find($id)->delete();
        #dd($id);
    }

    public function craw()
    {
        $path = "/var/www/dr";
        $result = Process::path($path)->timeout(0)->run('/usr/bin/php artisan dusk --without-tty --filter buscaTest');

        dump( 's:'. $result->successful());
        dump( 'f:'. $result->failed());
        dump( 'e:'. $result->exitCode());
        dump( 'o:'. $result->output());
        dump( 'e:'. $result->errorOutput());
        dump( 'result:'. $result);

        echo $result->exitCode() === 0? $result->output() : $result->errorOutput();

    }

    #[Computed()]
    public function getAll()
    {
        return ModelsBusca::all();
    }

    #[Layout("layouts/app")]
    public function render()
    {
        return view('livewire.busca', [
            'buscas' => $this->getAll(),
        ]);
    }
}
