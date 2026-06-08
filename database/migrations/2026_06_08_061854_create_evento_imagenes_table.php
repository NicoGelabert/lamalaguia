<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evento_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained()->cascadeOnDelete();
            $table->string('ruta');
            $table->string('mime')->nullable();
            $table->integer('size')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_imagenes');
    }
};
