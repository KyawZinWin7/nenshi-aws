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
        Schema::create('main_operations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('plant_id');
            $table->foreign('plant_id')->references('id')->on('plants')->onDelete('cascade');

            $table->unsignedBigInteger('machine_type_id');
            $table->foreign('machine_type_id')->references('id')->on('machine_types')->onDelete('cascade');

            $table->unsignedBigInteger('machine_number_id');
            $table->foreign('machine_number_id')->references('id')->on('machine_numbers')->onDelete('cascade');
          
            

            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');

          

            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('total_time')->default('00:00:00');


            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');

            $table->string('status')->default('0');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_operations');
    }
};
