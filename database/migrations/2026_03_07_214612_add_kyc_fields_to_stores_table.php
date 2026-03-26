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
        Schema::table('stores', function (Blueprint $table) {
            $table->string('store_activity')->nullable()->after('store_type');
            $table->decimal('latitude', 10, 8)->nullable()->after('contact_info');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->text('location_description')->nullable()->after('longitude');
            $table->string('identity_front')->nullable()->after('location_description');
            $table->string('identity_back')->nullable()->after('identity_front');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['store_activity', 'latitude', 'longitude', 'location_description', 'identity_front', 'identity_back']);
        });
    }
};
