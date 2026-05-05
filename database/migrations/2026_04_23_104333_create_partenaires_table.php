<?php

use App\Enums\PartenaireStatut;
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
        Schema::create('partenaires', function (Blueprint $table) {
            $table->id();
            $table->string('raison_sociale');
            $table->string('adresse')->nullable();
            $table->string('cp', 10)->nullable();
            $table->string('ville')->nullable();
            $table->integer('nbrs_salaries')->nullable();
            $table->text('secteur_activite')->nullable();
            $table->string('telephone_1', 20)->nullable();
            $table->string('telephone_2', 20)->nullable();
            $table->decimal('ca', 15, 2)->nullable();
            $table->string('siret', 20)->nullable()->unique();
            $table->string('statut')
                ->default(PartenaireStatut::A_CONTACTER->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partenaires');
    }
};
