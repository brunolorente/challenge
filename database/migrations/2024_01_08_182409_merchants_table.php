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
        Schema::create('merchants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('external_id');
            $table->string('reference')->unique();
            $table->string('email')->unique();
            $table->date('live_on');
            $table->enum('disbursement_frequency', ["DAILY", "WEEKLY"]);
            $table->float('minimum_monthly_fee', 4, 2);
            $table->timestamp('ingest_date');
            $table->string('origin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('merchants');
    }
};
