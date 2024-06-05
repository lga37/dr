<?php

namespace App\Livewire;

use App\Models\Video as ModelsVideo;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Video extends Component
{
    #[Layout("layouts/app")]
    public function render()
    {
       
        return view('livewire.video',[
            'videos'=>ModelsVideo::all(),
        ]);
    }
}
