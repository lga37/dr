<?php

namespace App\Livewire;

use App\Models\Canal as ModelsCanal;

use Illuminate\Support\Facades\Process;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Canal extends Component
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
        ModelsCanal::find($id)->delete();
        #dd($id);
    }


    public function getCanal(){
        $result = Process::path("/var/www/dr")->timeout(0)->run('/usr/bin/php artisan dusk --without-tty --filter canalTest');
        $res = $result->exitCode() === 0? $result->output() : $result->errorOutput();
        dump('result:'. $res);
    }


    public function vidiq(){

        $result = Process::path("/var/www/dr")->timeout(0)->run('/usr/bin/php artisan dusk --without-tty --filter vidiqTest');
        $res = $result->exitCode() === 0? $result->output() : $result->errorOutput();
        dump('result:'. $res);

    }

    #[Layout("layouts/app")]
    public function render()
    {
       
        return view('livewire.canal',[
            'canals'=>ModelsCanal::search($this->search)
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage),
        ]);
    }
}
