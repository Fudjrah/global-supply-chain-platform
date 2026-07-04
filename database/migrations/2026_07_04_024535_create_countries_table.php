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
        Schema::create('countries', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('country_code', 5)->unique(); // Misal: ID, US, AU
        $table->string('currency', 10)->nullable();
        $table->string('region')->nullable();
        $table->string('language')->nullable();
        // Kolom data ekonomi untuk dashboard analitik
        $table->double('gdp')->nullable();
        $table->double('inflation')->nullable();
        $table->bigInteger('population')->nullable();
        $table->timestamps();
     });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
