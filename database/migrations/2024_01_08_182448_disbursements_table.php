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
        Schema::create('disbursements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->float('amount',9, 2);
            $table->integer('nb_of_orders')->default(0);
            $table->uuid('merchant_id');
            $table->float('commission',8, 2);
            $table->date('orders_start');
            $table->date('orders_end');
            $table->uuid('reference')->unique();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('disbursements');
    }
};
