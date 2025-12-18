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
        Schema::create('sizing_operations', function (Blueprint $table) {
            $table->id();

            //Plant
            $table->foreignId('plant_id')
                  ->constrained('plants')
                  ->restrictOnDelete();

            //Machine Type
             $table->foreignId('machine_type_id')
                   ->constrained('machine_types')
                   ->restrictOnDelete();

            //Machine Number
            $table->foreignId('machine_number_id')
                  ->constrained('machine_numbers')
                  ->restrictOnDelete();

            //Task
            $table->foreignId('task_id')
                  ->constrained('tasks')
                  ->restrictOnDelete();

            // Time fields
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('total_time')->default('00:00:00');
                
           
            // Employee (nullable)
            $table->foreignId('employee_id')
                  ->nullable()
                  ->constrained('employees')
                  ->nullOnDelete();

             // Department
            $table->foreignId('department_id')
                  ->constrained('departments')
                  ->restrictOnDelete();

            // Small Task (nullable)
            $table->foreignId('small_task_id')
                  ->nullable()
                  ->constrained('small_tasks')
                  ->nullOnDelete();

              // Others
            $table->string('description')->nullable();
            $table->string('status')->default('0');

            // Completed By → Set NULL
            $table->foreignId('completed_by')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            // Uncompleted By → Set NULL
            $table->foreignId('uncompleted_by')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->timestamps();

        });
            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizing_operations');
    }
};
