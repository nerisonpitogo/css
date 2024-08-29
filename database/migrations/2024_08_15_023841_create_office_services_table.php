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
            $table->boolean('is_simple')->default(false);

            // has_sqd0 to has_sqd8
            $table->boolean('has_sqd0')->default(true);
            $table->boolean('has_sqd1')->default(true);
            $table->boolean('has_sqd2')->default(true);
            $table->boolean('has_sqd3')->default(true);
            $table->boolean('has_sqd4')->default(true);
            $table->boolean('has_sqd5')->default(true);
            $table->boolean('has_sqd6')->default(true);
            $table->boolean('has_sqd7')->default(true);
            $table->boolean('has_sqd8')->default(true);
            $table->boolean('allow_na')->default(true);

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
