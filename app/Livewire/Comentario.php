<?php

namespace App\Livewire;

use App\Models\Video;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

use Illuminate\Support\Facades\Route;
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

    public int $video_id;
    public function mount($video_id=0)
    {
        $this->video_id = $video_id;
    }
    
    public Video $video;


    public function setTox($id){
        $tox = mt_rand( 0, 10 ) / 10;
        #ModelsComentario::find($id)->update(compact('tox'));
        ModelsComentario::find($id)->update(['perspective'=>$tox]);


    }

    #[Layout("layouts/app")]
    public function render()
    {
        #$comentarios = ModelsComentario::all();
        #return view('livewire.comentario',compact('comentarios'));

        if($this->video_id==0){
            $modoBusca = ModelsComentario::search($this->search)->orderBy($this->sortColumn, $this->sortDirection)->paginate($this->perPage);
        } else {
            $modoBusca = ModelsComentario::where('video_id',$this->video_id)->orderBy('dt')->paginate($this->perPage);
        }

        return view('livewire.comentario',[
            'comentarios'=> $modoBusca,
        ]);



    }
}
