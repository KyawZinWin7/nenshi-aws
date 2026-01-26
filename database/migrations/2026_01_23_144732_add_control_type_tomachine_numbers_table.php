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
         Schema::table('machine_numbers', function (Blueprint $table) {
            $table->enum('control_type', ['manual', 'auto'])
                  ->default('manual')
                  ->after('number');

            $table->integer('auto_stop_hours')
                  ->nullable()
                  ->after('control_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_numbers', function (Blueprint $table) {
            $table->dropColumn(['control_type', 'auto_stop_hours']);
        });
    }
};
