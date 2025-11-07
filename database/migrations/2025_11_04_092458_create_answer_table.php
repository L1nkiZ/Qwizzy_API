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
        Schema::create('answer', function (Blueprint $table) {
            $table->id();
            $table->string('answer');
            $table->foreignId('question_id')->constrained('question');
            $table->timestamps();
        });

        // Question 1: Quel est le nom du personnage principal de la série The Witcher ?
        DB::table('answer')->insert([
            'answer' => '4',
            'question_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Question 2: Quel est le réalisateur de la saga Star Wars ?
        DB::table('answer')->insert([
            'answer' => '1',
            'question_id' => 2,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Question 3: Qui est l'acteur principal du film "Blade Runner" ?
        DB::table('answer')->insert([
            'answer' => '3',
            'question_id' => 3,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Question 4: Quelle marque de moto est célèbre pour son logo en forme d'aile ?
        DB::table('answer')->insert([
            'answer' => '1',
            'question_id' => 4,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Question 5: Comment appelle-t-on le type de moto conçu pour la course sur circuit ?
        DB::table('answer')->insert([
            'answer' => '3',
            'question_id' => 5,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Question 6: Quand est sorti le troisième opus de la sage de jeux vidéo "The Witcher" ?
        DB::table('answer')->insert([
            'answer' => '4',
            'question_id' => 6,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer');
    }
};
