<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $blueprint) {
            $blueprint->string('payment_method')->default('Cash')->after('total_price');
            $blueprint->integer('amount_paid')->default(0)->after('payment_method');
            $blueprint->integer('amount_change')->default(0)->after('amount_paid');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['payment_method', 'amount_paid', 'amount_change']);
        });
    }
};