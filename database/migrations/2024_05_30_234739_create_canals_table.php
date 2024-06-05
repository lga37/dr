<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Busca;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('canals', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('desc')->nullable();
            $table->json('links')->nullable();
            $table->boolean('parse')->default(false);
            $table->boolean('verificado')->default(false);
            $table->unsignedInteger('inscritos')->nullable();
            $table->unsignedInteger('views')->nullable();
            $table->date('dt')->nullable();
            $table->string('local')->nullable();

            $table->foreignIdFor(Busca::class);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canals');
    }
};
