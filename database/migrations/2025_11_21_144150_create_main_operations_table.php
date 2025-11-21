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

            // Plant
            $table->unsignedBigInteger('plant_id');
            $table->foreign('plant_id')
                ->references('id')->on('plants')
                ->onDelete('restrict');

            // Machine Type
            $table->unsignedBigInteger('machine_type_id');
            $table->foreign('machine_type_id')
                ->references('id')->on('machine_types')
                ->onDelete('restrict');

            // Machine Number
            $table->unsignedBigInteger('machine_number_id');
            $table->foreign('machine_number_id')
                ->references('id')->on('machine_numbers')
                ->onDelete('restrict');

            // Task
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')
                ->references('id')->on('tasks')
                ->onDelete('restrict');

            // Time fields
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('total_time')->default('00:00:00');

            // Employee
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('employee_id')
                ->references('id')->on('employees')
                ->onDelete('set null');



            // Department
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')
                ->references('id')->on('departments')
                ->onDelete('restrict');

              // Small Task
            $table->unsignedBigInteger('small_task_id')->nullable();
            $table->foreign('small_task_id')
                ->references('id')->on('small_tasks')
                ->onDelete('set null');  



            // Others
            $table->string('description')->nullable();
            $table->string('status')->default('0');

            // Completed By → Set NULL
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->foreign('completed_by')
                ->references('id')->on('employees')
                ->onDelete('set null');

            // Uncompleted By → Set NULL
            $table->unsignedBigInteger('uncompleted_by')->nullable();
            $table->foreign('uncompleted_by')
                ->references('id')->on('employees')
                ->onDelete('set null');

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
