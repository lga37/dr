<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Busca;
use App\Models\Canal;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();

            #$table->string('cod')->unique();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('desc')->nullable();
            $table->text('transcript')->nullable();
            $table->json('hashtags')->nullable();
            $table->boolean('parse')->default(false);

            $table->unsignedInteger('likes')->nullable();
            $table->unsignedInteger('dislikes')->nullable();
            $table->unsignedInteger('views')->nullable();
            $table->unsignedInteger('duration')->nullable();

            $table->date('dt')->nullable();

            $table->foreignIdFor(Canal::class);
            $table->foreignIdFor(Busca::class);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
