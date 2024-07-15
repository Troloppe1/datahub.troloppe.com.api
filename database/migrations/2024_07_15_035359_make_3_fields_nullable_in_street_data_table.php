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
            $table->string('contact_name')->nullable()->change();
            $table->string('contact_numbers')->nullable()->change();
            $table->string('contact_email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('street_data', function (Blueprint $table) {
            $table->string('contact_name')->nullable(false)->change();
            $table->string('contact_numbers')->nullable(false)->change();
            $table->string('contact_email')->nullable(false)->change();
        });
    }
};
