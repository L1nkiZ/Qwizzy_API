<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('difficulty', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->smallInteger('point');
            $table->timestamps();
        });

        DB::table("difficulty")->insert([
            ['name' => "Facile", 'point' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Moyen", 'point' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Difficile", 'point' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('difficulty');
    }
};
