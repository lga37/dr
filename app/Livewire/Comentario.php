<?php

namespace App\Livewire;

use App\Models\Video;
use App\Models\Comentario as ModelsComentario;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Comentario extends Component
{

    public Video $video;


    public function setTox($comentario_id){
        dd($comentario_id);
    }

    #[Layout("layouts/app")]
    public function render()
    {
        $comentarios = ModelsComentario::all();
        return view('livewire.comentario',compact('comentarios'));
    }
}
