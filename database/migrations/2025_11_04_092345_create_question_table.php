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
        Schema::create('question', function (Blueprint $table) {
            $table->id();
            $table->string("question")->unique();
            $table->foreignId('subject_id')->constrained('subject');
            $table->foreignId('difficulty_id')->constrained('difficulty');
            $table->foreignId('question_type_id')->constrained('question_type');
            $table->string("proposal_1");
            $table->string("proposal_2");
            $table->string("proposal_3");
            $table->string("proposal_4");
            $table->timestamps();
        });

        DB::table('question')->insert([
            'question' => 'Quel est le nom du personnage principal de la série The Witcher ?',
            'subject_id' => 1,
            'difficulty_id' => 1,
            'question_type_id' => 1,
            'proposal_1' => 'Yennefer',
            'proposal_2' => 'Ratchet',
            'proposal_3' => 'Mario',
            'proposal_4' => 'Geralt de Riv',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('question')->insert([
            'question' => 'Quel est le réalisateur de la saga Star Wars ?',
            'subject_id' => 2,
            'difficulty_id' => 2,
            'question_type_id' => 1,
            'proposal_1' => 'George Lucas',
            'proposal_2' => 'Steven Spielberg',
            'proposal_3' => 'James Cameron',
            'proposal_4' => 'Peter Jackson',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('question')->insert([
            'question' => 'Qui est l\'acteur principal du film "Blade Runner" ?',
            'subject_id' => 2,
            'difficulty_id' => 3,
            'question_type_id' => 1,
            'proposal_1' => 'Tom Cruise',
            'proposal_2' => 'Ryan Gosling',
            'proposal_3' => 'Harrison Ford',
            'proposal_4' => 'Arnold Schwarzenegger',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('question')->insert([
            'question' => 'Quelle marque de moto est célèbre pour son logo en forme d\'aile ?',
            'subject_id' => 3,
            'difficulty_id' => 2,
            'question_type_id' => 1,
            'proposal_1' => 'Harley-Davidson',
            'proposal_2' => 'Yamaha',
            'proposal_3' => 'Ducati',
            'proposal_4' => 'Kawasaki',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('question')->insert([
            'question' => 'Comment appelle-t-on le type de moto conçu pour la course sur circuit ?',
            'subject_id' => 3,
            'difficulty_id' => 2,
            'question_type_id' => 1,
            'proposal_1' => 'Moto-cross',
            'proposal_2' => 'Roadster',
            'proposal_3' => 'Sportive',
            'proposal_4' => 'Custom',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('question')->insert([
            'question' => 'Quand est sorti le troisième opus de la sage de jeux vidéo "The Witcher" ?',
            'subject_id' => 1,
            'difficulty_id' => 3,
            'question_type_id' => 1,
            'proposal_1' => '2013',
            'proposal_2' => '2014',
            'proposal_3' => '2015',
            'proposal_4' => '2016',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question');
    }
};
