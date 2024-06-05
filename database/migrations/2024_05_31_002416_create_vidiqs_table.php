<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Canal;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vidiqs', function (Blueprint $table) {
            $table->id();


            $table->char('score');

            $table->unsignedInteger('views');
            $table->unsignedInteger('subscribers');

           
            $table->float('rate');
            $table->float('frequency');
            $table->float('length');
            $table->float('money');
        
        
            $table->foreignIdFor(Canal::class);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vidiqs');
    }
};
