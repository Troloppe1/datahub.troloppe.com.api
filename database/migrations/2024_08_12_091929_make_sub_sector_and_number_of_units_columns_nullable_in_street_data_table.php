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
        Schema::table('street_data', function (Blueprint $table) {
            $table->smallInteger('number_of_units')->nullable()->change();
            $table->foreignId('sub_sector_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('street_data', function (Blueprint $table) {
            $table->smallInteger('number_of_units')->nullable(false)->change();
            $table->foreignId('sub_sector_id')->nullable(false)->change();
        });
    }
};
