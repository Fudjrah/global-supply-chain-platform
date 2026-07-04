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
       Schema::create('risk_scores', function (Blueprint $table) {
        $table->id();
        $table->foreignId('country_id')->constrained()->onDelete('cascade');
        $table->integer('weather_risk');
        $table->integer('inflation_risk');
        $table->integer('currency_risk');
        $table->integer('news_sentiment_risk');
        $table->integer('total_risk_score'); // Hasil akhir 0-100
        $table->string('risk_status'); // Low, Medium, High
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
