<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lib_regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // insert default regions Region I – Ilocos Region
        // Region II – Cagayan Valley
        // Region III – Central Luzon
        // Region IV‑A – CALABARZON
        // MIMAROPA Region
        // Region V – Bicol Region
        // Region VI – Western Visayas
        // Region VII – Central Visayas
        // Region VIII – Eastern Visayas
        // Region IX – Zamboanga Peninsula
        // Region X – Northern Mindanao
        // Region XI – Davao Region
        // Region XII – SOCCSKSARGEN
        // Region XIII – Caraga
        // NCR – National Capital Region
        // CAR – Cordillera Administrative Region
        // BARMM – Bangsamoro Autonomous Region in Muslim Mindanao

        $regions = [
            'Region I – Ilocos Region',
            'Region II – Cagayan Valley',
            'Region III – Central Luzon',
            'Region IV‑A – CALABARZON',
            'Region IV‑B MIMAROPA Region',
            'Region V – Bicol Region',
            'Region VI – Western Visayas',
            'Region VII – Central Visayas',
            'Region VIII – Eastern Visayas',
            'Region IX – Zamboanga Peninsula',
            'Region X – Northern Mindanao',
            'Region XI – Davao Region',
            'Region XII – SOCCSKSARGEN',
            'Region XIII – Caraga',
            'NCR – National Capital Region',
            'CAR – Cordillera Administrative Region',
            'BARMM – Bangsamoro Autonomous Region in Muslim Mindanao',
        ];

        foreach ($regions as $region) {
            DB::table('lib_regions')->insert([
                'name' => $region,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lib_regions');
    }
};
