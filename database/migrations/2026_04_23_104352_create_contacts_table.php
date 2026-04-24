<?php

use App\Enums\ContactStatut;
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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partenaire_id')
                ->constrained('partenaires')
                ->onDelete('cascade');
            $table->string('conseiller_nom')->nullable();
            $table->string('conseiller_prenom')->nullable();
            $table->date('date_premier_contact')->nullable();
            $table->text('commentaires')->nullable();
            $table->string('poste')->nullable();
            $table->string('tel', 20)->nullable();
            $table->string('statut')
                  ->default(ContactStatut::A_CONTACTER->value);

            $table->string('fonction')->nullable();
            $table->date('date_rdv')->nullable();
            $table->time('heure_rdv')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
