<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('additional_fees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->float('fee', 9, 2);
            $table->dateTime('date');
            $table->uuid('merchant_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('additional_fees');
    }
};
