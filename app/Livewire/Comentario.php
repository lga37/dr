<?php

namespace App\Livewire;

use App\Models\Video;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Comentario as ModelsComentario;

class Comentario extends Component
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
        ModelsComentario::find($id)->delete();
        #dd($id);
    }


    
    public Video $video;


    public function setTox($comentario_id){
        dd($comentario_id);
    }

    #[Layout("layouts/app")]
    public function render()
    {
        #$comentarios = ModelsComentario::all();
        #return view('livewire.comentario',compact('comentarios'));

        return view('livewire.comentario',[
            'comentarios'=>ModelsComentario::search($this->search)
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage),
        ]);



    }
}
