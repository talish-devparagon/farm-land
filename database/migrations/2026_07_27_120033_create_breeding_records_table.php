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
        Schema::create('breeding_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doe_id')->constrained('animals')->cascadeOnDelete();
            $table->foreignId('buck_id')->nullable()->constrained('animals')->nullOnDelete();
            $table->date('mating_date');
            $table->date('expected_kidding_date')->index();
            $table->date('actual_kidding_date')->nullable();
            $table->unsignedInteger('number_of_offspring')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breeding_records');
    }
};
