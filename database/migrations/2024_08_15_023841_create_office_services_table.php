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
        Schema::create('office_services', function (Blueprint $table) {
            $table->id();
            // office_id
            $table->foreignId('office_id')->constrained('offices');
            // service_id
            $table->foreignId('service_id')->constrained('lib_services');
            // has_cc
            $table->boolean('has_cc')->default(false);
            // created_by

            $table->foreignId('created_by')->constrained('users');
            // updated_by
            $table->foreignId('updated_by')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_services');
    }
};
