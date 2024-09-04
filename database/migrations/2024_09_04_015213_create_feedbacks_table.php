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
            $table->string('sex');
            $table->integer('age');
            $table->string('region');
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
            $table->string('suggestions')->nullable();
            // email
            $table->string('email')->nullable();


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
