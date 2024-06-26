<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = ['user','dt','likes','texto','video_id'];

    public function video(){
        return $this->belongsTo(Video::class);
    }




}
