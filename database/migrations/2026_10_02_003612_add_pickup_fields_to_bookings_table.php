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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('customer_email')->nullable()->change();
            $table->string('pickup_location')->nullable()->after('customer_phone');
            $table->string('pickup_time')->nullable()->after('pickup_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('customer_email')->nullable(false)->change();
            $table->dropColumn(['pickup_location', 'pickup_time']);
        });
    }
};
