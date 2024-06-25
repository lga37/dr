<?php

namespace App\Livewire;

use App\Models\Canal;
use Livewire\Component;
use Livewire\Attributes\Layout;
use LivewireUI\Modal\ModalComponent;


class AnosWebarx extends ModalComponent
{

    public Canal $canal;

    #[Layout("layouts/app")]
    public function render()
    {
        return view('livewire.anos-webarx');
    }
}
