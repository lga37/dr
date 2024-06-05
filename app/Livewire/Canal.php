<?php

namespace App\Livewire;

use App\Models\Canal as ModelsCanal;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Canal extends Component
{
    #[Layout("layouts/app")]
    public function render()
    {
       
        return view('livewire.canal',[
            'canals'=>ModelsCanal::all(),
        ]);
    }
}
