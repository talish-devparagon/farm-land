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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->string('tag_number');
            $table->string('name')->nullable();
            $table->string('breed')->nullable();
            $table->string('sex');
            $table->date('date_of_birth')->nullable();
            $table->foreignId('mother_id')->nullable()->constrained('animals')->nullOnDelete();
            $table->foreignId('father_id')->nullable()->constrained('animals')->nullOnDelete();
            $table->string('status')->default('alive')->index();
            $table->decimal('current_weight', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['farm_id', 'tag_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
