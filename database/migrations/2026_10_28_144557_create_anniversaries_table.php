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
        Schema::create('anniversaries', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('first_username')->nullable();
            $table->string('second_name');
            $table->string('second_username')->nullable();
            $table->unsignedTinyInteger('day');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedSmallInteger('last_congratulated_year')->nullable();
            $table->timestamps();

            $table->index(['month', 'day']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anniversaries');
    }
};
