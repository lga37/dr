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
}
