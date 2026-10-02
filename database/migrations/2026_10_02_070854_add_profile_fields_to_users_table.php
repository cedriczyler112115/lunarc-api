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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('guest')->after('id');
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('middle_name')->nullable()->after('last_name');
            $table->string('extension_name')->nullable()->after('middle_name');
            $table->date('birthday')->nullable()->after('extension_name');
            $table->text('address')->nullable()->after('birthday');
            $table->string('contact_number')->nullable()->after('address');
            $table->string('avatar_path')->nullable()->after('contact_number');
            $table->text('owner_description')->nullable()->after('avatar_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'first_name',
                'last_name',
                'middle_name',
                'extension_name',
                'birthday',
                'address',
                'contact_number',
                'avatar_path',
                'owner_description',
            ]);
        });
    }
};
