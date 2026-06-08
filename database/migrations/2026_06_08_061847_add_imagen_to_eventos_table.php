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
        Schema::table('eventos', function (Blueprint $table) {
            $table->string('imagen')->nullable()->after('nombre');
            $table->string('imagen_mime')->nullable()->after('imagen');
            $table->integer('imagen_size')->nullable()->after('imagen_mime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['imagen', 'imagen_mime', 'imagen_size']);
        });
    }
};
