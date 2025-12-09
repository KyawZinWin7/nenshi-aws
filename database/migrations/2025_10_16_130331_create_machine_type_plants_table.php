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
        Schema::create('machine_type_plants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('plant_id')->constrained()->restrictOnDelete();
            $table->integer('start_number')->default(0);
            $table->integer('end_number')->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_type_plants');
    }
};
