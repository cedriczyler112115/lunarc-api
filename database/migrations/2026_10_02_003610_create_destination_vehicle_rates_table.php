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
        Schema::create('destination_vehicle_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->foreignUuid('vehicle_type_id')->constrained('vehicle_types')->cascadeOnDelete();
            $table->decimal('destination_rate', 10, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['destination_id', 'vehicle_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destination_vehicle_rates');
    }
};
