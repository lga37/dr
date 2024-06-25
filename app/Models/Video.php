<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['nome','slug','canal_id','busca_id'];


    public function comentarios(){
        return $this->hasMany(Comentario::class);
    }

    public function busca(){
        return $this->belongsTo(Busca::class);
    }




}
