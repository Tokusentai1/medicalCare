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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name');
            $table->string('composition');
            $table->string('dosage');
            $table->string('dosage_form');
            $table->string('image');
            $table->integer('price');
            $table->date('manufacture_date');
            $table->date('expire_date');
            $table->string('manufacturer');
            $table->boolean('rocheta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
