<?php

use App\Livewire\Busca;
use App\Livewire\Canal;

use App\Livewire\Video;
use App\Livewire\Vidiq;
use App\Livewire\Archive;
use App\Livewire\Comentario;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('ai', function(){
    $res = app('openai')->chat()->create([
        'model'=>'gpt-3.5-turbo-16k',
        'messages'=>[
            ['role'=>'system','content'=>'Voce e um bot academico que quer ajudar'],
            ['role'=>'user','content'=>'link me to the laravel docs'],
        ]
    ]);

    dd($res);
});


Route::get('busca', Busca::class)->name('busca');
Route::get('video', Video::class)->name('video');
Route::get('canal', Canal::class)->name('canal');
Route::get('vidiq', Vidiq::class)->name('vidiq');
Route::get('archive', Archive::class)->name('archive');
Route::get('comentario', Comentario::class)->name('comentario');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
