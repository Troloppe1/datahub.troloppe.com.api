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
        Schema::create('street_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('users');
            $table->string('unique_code', 50);
            $table->string('street_address', 250);
            $table->mediumText('description');
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->smallInteger('number_of_units');
            $table->string('contact_name', 250);
            $table->string('contact_numbers', 250);
            $table->string('contact_email', 250);
            $table->string('construction_status', 50);
            $table->boolean('is_verified')->default(false);
            $table->string('image_path', 255);
            $table->string('geolocation', 150)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('street_data');
    }
};
