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
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('office_level')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('offices');
            $table->timestamps();
        });


        // insert data
        DB::table('offices')->insert([

            ['name' => 'Caraga Region', 'short_name' => 'ORD', 'office_level' => 'RO', 'parent_id' => null], //1
            ['name' => 'Regional Office', 'short_name' => 'ORD', 'office_level' => 'RO', 'parent_id' => 1], //2
            ['name' => 'Office of the Regional Director', 'short_name' => 'ORD', 'office_level' => 'RO FD', 'parent_id' => 2], //3
            ['name' => 'Office of the Assistant Regional Director', 'short_name' => 'OARD', 'office_level' => 'RO FD', 'parent_id' => 2], //4
            ['name' => 'Administrative Services Division', 'short_name' => 'ASD', 'office_level' => 'RO FD', 'parent_id' => 2], //5
            ['name' => 'Curriculum and Learning Management Division', 'short_name' => 'CLMD', 'office_level' => 'RO FD', 'parent_id' => 2], //6
            ['name' => 'Education Support Services Division', 'short_name' => 'ESSD', 'office_level' => 'RO FD', 'parent_id' => 2], //7
            ['name' => 'Finance Division', 'short_name' => 'FD', 'office_level' => 'RO FD', 'parent_id' => 2], //8
            ['name' => 'Field Technical Assistance Division', 'short_name' => 'FTAD', 'office_level' => 'RO FD', 'parent_id' => 2], //9
            ['name' => 'Human Resource Development Division', 'short_name' => 'HRDD', 'office_level' => 'RO FD', 'parent_id' => 2], //10

            ['name' => 'Information and Communications Technology Unit', 'short_name' => 'ICTU', 'office_level' => 'RO UNIT', 'parent_id' => 3], //11
            ['name' => 'Legal Unit', 'short_name' => 'Legal', 'office_level' => 'RO UNIT', 'parent_id' => 3], //12
            ['name' => 'Public Affairs Unit', 'short_name' => 'PAU', 'office_level' => 'RO UNIT', 'parent_id' => 3], //13

            ['name' => 'Accounting Unit', 'short_name' => 'Accounting', 'office_level' => 'RO UNIT', 'parent_id' => 7], //14
            ['name' => 'Budget Unit', 'short_name' => 'Budget', 'office_level' => 'RO UNIT', 'parent_id' => 7], //15


            ['name' => 'Asset Management Unit', 'short_name' => 'Asset', 'office_level' => 'RO UNIT', 'parent_id' => 5], //16
            ['name' => 'Cash Unit', 'short_name' => 'Cash', 'office_level' => 'RO UNIT', 'parent_id' => 5], //17
            ['name' => 'General Services Unit', 'short_name' => 'GSU', 'office_level' => 'RO UNIT', 'parent_id' => 5], //18
            ['name' => 'Personnel Unit', 'short_name' => 'Personnel', 'office_level' => 'RO UNIT', 'parent_id' => 5], //19
            ['name' => 'Procurement Unit', 'short_name' => 'Procurement', 'office_level' => 'RO UNIT', 'parent_id' => 5], //20
            ['name' => 'Records Unit', 'short_name' => 'Records', 'office_level' => 'RO UNIT', 'parent_id' => 5], //21
            ['name' => 'Regional Payroll Services Unit', 'short_name' => 'RPSU', 'office_level' => 'RO UNIT', 'parent_id' => 5], //22


            // ['name' => 'Agusan del Norte', 'short_name' => 'ADN', 'office_level' => 'SDO', 'parent_id' => 1], //23
            // ['name' => 'School Governance and Operations Division', 'short_name' => 'SGOD', 'office_level' => 'SDO FD', 'parent_id' => 23], //24
            // ['name' => 'Curriculum and Instruction Division', 'short_name' => 'CID', 'office_level' => 'SDO FD', 'parent_id' => 23], //25

            // // Agusan del Sur and the SGOD and CID
            // ['name' => 'Agusan del Sur', 'short_name' => 'ADS', 'office_level' => 'SDO', 'parent_id' => 1], //26
            // ['name' => 'School Governance and Operations Division', 'short_name' => 'SGOD', 'office_level' => 'SDO FD', 'parent_id' => 26], //27
            // ['name' => 'Curriculum and Instruction Division', 'short_name' => 'CID', 'office_level' => 'SDO FD', 'parent_id' => 26], //28

            // // Bayugan City
            // ['name' => 'Bayugan City', 'short_name' => 'BC', 'office_level' => 'SDO', 'parent_id' => 1], //29
            // ['name' => 'School Governance and Operations Division', 'short_name' => 'SGOD', 'office_level' => 'SDO FD', 'parent_id' => 29], //30
            // ['name' => 'Curriculum and Instruction Division', 'short_name' => 'CID', 'office_level' => 'SDO FD', 'parent_id' => 29], //31

            // // Bislig City
            // ['name' => 'Bislig City', 'short_name' => 'BC', 'office_level' => 'SDO', 'parent_id' => 1], //32
            // ['name' => 'School Governance and Operations Division', 'short_name' => 'SGOD', 'office_level' => 'SDO FD', 'parent_id' => 32], //33
            // ['name' => 'Curriculum and Instruction Division', 'short_name' => 'CID', 'office_level' => 'SDO FD', 'parent_id' => 32], //34

            // // Butuan City
            // ['name' => 'Butuan City', 'short_name' => 'BC', 'office_level' => 'SDO', 'parent_id' => 1], //35
            // ['name' => 'School Governance and Operations Division', 'short_name' => 'SGOD', 'office_level' => 'SDO FD', 'parent_id' => 35], //36
            // ['name' => 'Curriculum and Instruction Division', 'short_name' => 'CID', 'office_level' => 'SDO FD', 'parent_id' => 35], //37

            // // Cabadbaran City
            // ['name' => 'Cabadbaran City', 'short_name' => 'CC', 'office_level' => 'SDO', 'parent_id' => 1], //38
            // ['name' => 'School Governance and Operations Division', 'short_name' => 'SGOD', 'office_level' => 'SDO FD', 'parent_id' => 38], //39
            // ['name' => 'Curriculum and Instruction Division', 'short_name' => 'CID', 'office_level' => 'SDO FD', 'parent_id' => 38], //40

            // // Dinagat Islands
            // ['name' => 'Dinagat Islands', 'short_name' => 'DI', 'office_level' => 'SDO', 'parent_id' => 1], //41
            // ['name' => 'School Governance and Operations Division', 'short_name' => 'SGOD', 'office_level' => 'SDO FD', 'parent_id' => 41], //42
            // ['name' => 'Curriculum and Instruction Division', 'short_name' => 'CID', 'office_level' => 'SDO FD', 'parent_id' => 41], //43

            // // Siargao Island
            // ['name' => 'Siargao Island', 'short_name' => 'SI', 'office_level' => 'SDO', 'parent_id' => 1], //44
            // ['name' => 'School Governance and Operations Division', 'short_name' => 'SGOD', 'office_level' => 'SDO FD', 'parent_id' => 44], //45
            // ['name' => 'Curriculum and Instruction Division', 'short_name' => 'CID', 'office_level' => 'SDO FD', 'parent_id' => 44], //46

            // // Surigao City
            // ['name' => 'Surigao City', 'short_name' => 'SC', 'office_level' => 'SDO', 'parent_id' => 1], //47
            // ['name' => 'School Governance and Operations Division', 'short_name' => 'SGOD', 'office_level' => 'SDO FD', 'parent_id' => 47], //48
            // ['name' => 'Curriculum and Instruction Division', 'short_name' => 'CID', 'office_level' => 'SDO FD', 'parent_id' => 47], //49

            // // Surigao del Norte
            // ['name' => 'Surigao del Norte', 'short_name' => 'SDN', 'office_level' => 'SDO', 'parent_id' => 1], //50
            // ['name' => 'School Governance and Operations Division', 'short_name' => 'SGOD', 'office_level' => 'SDO FD', 'parent_id' => 50], //51
            // ['name' => 'Curriculum and Instruction Division', 'short_name' => 'CID', 'office_level' => 'SDO FD', 'parent_id' => 50], //52

            // // Surigao del Sur
            // ['name' => 'Surigao del Sur', 'short_name' => 'SDS', 'office_level' => 'SDO', 'parent_id' => 1], //53
            // ['name' => 'School Governance and Operations Division', 'short_name' => 'SGOD', 'office_level' => 'SDO FD', 'parent_id' => 53], //54
            // ['name' => 'Curriculum and Instruction Division', 'short_name' => 'CID', 'office_level' => 'SDO FD', 'parent_id' => 53], //55

            // // Tandag City
            // ['name' => 'Tandag City', 'short_name' => 'TC', 'office_level' => 'SDO', 'parent_id' => 1], //56
            // ['name' => 'School Governance and Operations Division', 'short_name' => 'SGOD', 'office_level' => 'SDO FD', 'parent_id' => 56], //57
            // ['name' => 'Curriculum and Instruction Division', 'short_name' => 'CID', 'office_level' => 'SDO FD', 'parent_id' => 56], //59
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
