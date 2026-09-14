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
        Schema::create('parc_informatique_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parc_informatique_id')->constrained('parc_informatiques')->cascadeOnDelete();
            $table->foreignId('parc_informatique_note_id')->constrained('parc_informatique_notes')->restrictOnDelete();
            $table->string('autre_precision')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parc_informatique_note_items');
    }
};
