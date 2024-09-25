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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            // client_Type
            $table->string('client_type');
            // is_external
            $table->boolean('is_external');
            $table->string('sex');
            $table->integer('age');
            // $table->string('region');
            // region_id from the lib_regions table
            $table->foreignId('region_id')->constrained('lib_regions');

            $table->unsignedBigInteger('office_service_id');
            $table->foreign('office_service_id')->references('id')->on('office_services');
            // cc1 int
            $table->integer('cc1');
            $table->integer('cc2')->nullable();
            $table->integer('cc3')->nullable();

            // sqd0 integer
            $table->integer('sqd0');
            $table->integer('sqd1');
            $table->integer('sqd2');
            $table->integer('sqd3');
            $table->integer('sqd4');
            $table->integer('sqd5');
            $table->integer('sqd6');
            $table->integer('sqd7');
            $table->integer('sqd8');

            // suggestion
            $table->longText('suggestions')->nullable();
            // email
            $table->string('email')->nullable();

            // is_reported
            $table->boolean('is_reported')->default(true);
            // type 1 for highlighted, 2 for lowlighted, 3 for normal
            $table->integer('type')->default(3);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
