<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Feedback;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {



        // create permissions
        $permissions_to_add = ['Manage Users', 'Manage Settings'];
        foreach ($permissions_to_add as $permission) {
            Permission::create([
                'name' => $permission,
                'description' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // create roles
        $roles_to_add = ['Admin'];
        foreach ($roles_to_add as $role) {
            Role::create([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        // assign permissions to roles
        $role = Role::where('name', 'Admin')->first();
        $permissions = Permission::all();

        $role->permissions()->sync($permissions->pluck('id'));

        // assign user id 1 to admin
        $user = User::find(1);
        $user->assignRole($role);


        // THE FOLOWING IS A DEPED MIGRATION DATA IF YOU ARE NOT FROM DEPED, COMMENT OUT THE FOLLOWING CODE
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

            ['name' => 'Accounting Unit', 'short_name' => 'Accounting', 'office_level' => 'RO UNIT', 'parent_id' => 8], //14
            ['name' => 'Budget Unit', 'short_name' => 'Budget', 'office_level' => 'RO UNIT', 'parent_id' => 8], //15


            ['name' => 'Asset Management Unit', 'short_name' => 'Asset', 'office_level' => 'RO UNIT', 'parent_id' => 5], //16
            ['name' => 'Cash Unit', 'short_name' => 'Cash', 'office_level' => 'RO UNIT', 'parent_id' => 5], //17
            ['name' => 'General Services Unit', 'short_name' => 'GSU', 'office_level' => 'RO UNIT', 'parent_id' => 5], //18
            ['name' => 'Personnel Unit', 'short_name' => 'Personnel', 'office_level' => 'RO UNIT', 'parent_id' => 5], //19
            ['name' => 'Procurement Unit', 'short_name' => 'Procurement', 'office_level' => 'RO UNIT', 'parent_id' => 5], //20
            ['name' => 'Records Unit', 'short_name' => 'Records', 'office_level' => 'RO UNIT', 'parent_id' => 5], //21
            ['name' => 'Regional Payroll Services Unit', 'short_name' => 'RPSU', 'office_level' => 'RO UNIT', 'parent_id' => 5], //22


            ['name' => 'Policy Planning and Research Division', 'short_name' => 'PPRD', 'office_level' => 'RO FD', 'parent_id' => 2], //23
            ['name' => 'Quality Assurance Division', 'short_name' => 'QAD', 'office_level' => 'RO FD', 'parent_id' => 2], //24

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

        // insert services CASH Section
        DB::table('lib_services')->insert([
            ['service_name' => 'Cash/Check Collection', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //1
            ['service_name' => 'Payment of Obligation', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //2
            ['service_name' => 'Handling of Cash Advances', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //3
        ]);

        // insert services
        DB::table('office_services')->insert([
            ['office_id' => 17, 'service_id' => 1, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 17, 'service_id' => 2, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 17, 'service_id' => 3, 'created_by' => 1, 'updated_by' => 1,],
        ]);

        // REcords Unit
        DB::table('lib_services')->insert([
            ['service_name' => 'Receiving Incoming Communication', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //4
            ['service_name' => 'Releasing Communication/Documents', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //5
            ['service_name' => 'Certification, Authentication, and Verification', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //6
            ['service_name' => 'Issuance of Requested Documents (Non-CTC)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //7
            ['service_name' => 'Issuance of Requested Documents (CTC And Photocopy of Documents)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //8
        ]);

        DB::table('office_services')->insert([
            ['office_id' => 21, 'service_id' => 4, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 21, 'service_id' => 5, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 21, 'service_id' => 6, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 21, 'service_id' => 7, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 21, 'service_id' => 8, 'created_by' => 1, 'updated_by' => 1,],
        ]);

        // Personnel Unit
        DB::table('lib_services')->insert([
            ['service_name' => 'Foreign Travel Authority Request on Official Time Or Official Business', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //9
            // Foreign Travel Authority Request 
            ['service_name' => 'Foreign Travel Authority Request on Official Time Or Official Business (For Personal Reason)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //10
            // Issuance of Certificate of Employment and/or Service Record
            ['service_name' => 'Issuance of Certificate of Employment and/or Service Record', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //11
            // Request for Transfer from Another Region
            ['service_name' => 'Request for Transfer from Another Region', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //12
            // Application for Leave
            ['service_name' => 'Application for Leave', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //13
            // Application for Retirement
            ['service_name' => 'Application for Retirement', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //14
            // Processing of Terminal Leave Benefits
            ['service_name' => 'Processing of Terminal Leave Benefits', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //15
            // Submission of Employment Application
            ['service_name' => 'Submission of Employment Application', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //16
            // Processing of Equivalent Record Form (ERF)
            ['service_name' => 'Processing of Equivalent Record Form (ERF)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //17
            // Processing of Study Leave	
            ['service_name' => 'Processing of Study Leave', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //18
        ]);

        DB::table('office_services')->insert([
            //    insert office_id 19
            ['office_id' => 19, 'service_id' => 9, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 19, 'service_id' => 10, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 19, 'service_id' => 11, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 19, 'service_id' => 12, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 19, 'service_id' => 13, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 19, 'service_id' => 14, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 19, 'service_id' => 15, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 19, 'service_id' => 16, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 19, 'service_id' => 17, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 19, 'service_id' => 18, 'created_by' => 1, 'updated_by' => 1,],
        ]);

        // RPSU
        DB::table('lib_services')->insert([
            ['service_name' => 'Request for RPSU Clearance/Last Payment Cert.', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //19
            // Request for Inclusion of New Deduction/s
            ['service_name' => 'Request for Inclusion of New Deduction/s', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //20
            // Request for Change/Correction of Names due to Marriage and Incorrect Spelling
            ['service_name' => 'Request for Change/Correction of Names due to Marriage and Incorrect Spelling', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //21
            // Effecting Notice of Step Increment
            ['service_name' => 'Effecting Notice of Step Increment', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //22
            // Request for Adjustment due to Promotion
            ['service_name' => 'Request for Adjustment due to Promotion', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //23
            // Request for Inclusion in the Payroll System Rooster
            ['service_name' => 'Request for Inclusion in the Payroll System Rooster', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //24
            // Request for Station Transfer in the Payroll System
            ['service_name' => 'Request for Station Transfer in the Payroll System', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //25
            // Request for Deletion of Deduction/s
            ['service_name' => 'Request for Deletion of Deduction/s', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //26
            // Loan Verification
            ['service_name' => 'Loan Verification', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //27
            // Billing
            ['service_name' => 'Billing', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //28
        ]);

        DB::table('office_services')->insert([
            //    insert office_id 22
            ['office_id' => 22, 'service_id' => 19, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 22, 'service_id' => 20, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 22, 'service_id' => 21, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 22, 'service_id' => 22, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 22, 'service_id' => 23, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 22, 'service_id' => 24, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 22, 'service_id' => 25, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 22, 'service_id' => 26, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 22, 'service_id' => 27, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 22, 'service_id' => 28, 'created_by' => 1, 'updated_by' => 1,],
        ]);

        // FTAD
        DB::table('lib_services')->insert([
            ['service_name' => 'Provision of Technical Assistance to Schools Division Offices/Schools (Normal Condition)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //29
            // Provision of Technical Assistance to Schools Division   Offices/Schools (emergency Condition)
            ['service_name' => 'Provision of Technical Assistance to Schools Division Offices/Schools (emergency Condition)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //30
            // Provision of Technical Assistance per Invitation(Normal Condition)	
            ['service_name' => 'Provision of Technical Assistance per Invitation (Normal Condition)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //31
            // Provision of Technical Assistance per Invitation (Emergency Condition)		
            ['service_name' => 'Provision of Technical Assistance per Invitation (Emergency Condition)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //32
            // Validation of SBM Level of Practice of Schools Under Normal Condition	
            ['service_name' => 'Validation of SBM Level of Practice of Schools Under Normal Condition', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //33
        ]);

        DB::table('office_services')->insert([
            //    insert office_id 22
            ['office_id' => 9, 'service_id' => 29, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 9, 'service_id' => 30, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 9, 'service_id' => 31, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 9, 'service_id' => 32, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 9, 'service_id' => 33, 'created_by' => 1, 'updated_by' => 1,],
        ]);

        // ACCOUNTING UNIT
        DB::table('lib_services')->insert([
            ['service_name' => 'Request for Issuance of Agency Code', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //34
            ['service_name' => 'Budget Mobilization and utilization (Disbursement)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //35
            ['service_name' => 'Budget Mobilization and Utilization (Disbursement) (Catering Services/Meals/Room Accommodation-SVP)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //36
            ['service_name' => 'Budget Mobilization and Utilization (Disbursement) (Procurement of Goods/Supplies/Materials-Bidding)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //37
            ['service_name' => 'Budget Mobilization and Utilization (Disbursement) (Purchase of Supplies/Materials – Shopping)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //38
            ['service_name' => 'Budget Mobilization and Utilization (Disbursement) (Reimbursement of Local Travel Expenses)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //39
            ['service_name' => 'Budget Execution-Issuance of Sub-ARO', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //40
            ['service_name' => 'Budget Execution-Utilization & Obligation', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //41
            ['service_name' => 'Certification as to Availability of Funds', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //42
            ['service_name' => 'Endorsing Request for Cash Allocation from SDOs', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //43
        ]);

        DB::table('office_services')->insert([
            //    insert office_id 14
            ['office_id' => 14, 'service_id' => 34, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 14, 'service_id' => 35, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 14, 'service_id' => 36, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 14, 'service_id' => 37, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 14, 'service_id' => 38, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 14, 'service_id' => 39, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 14, 'service_id' => 40, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 14, 'service_id' => 41, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 14, 'service_id' => 42, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 14, 'service_id' => 43, 'created_by' => 1, 'updated_by' => 1,],
        ]);


        DB::table('lib_services')->insert([
            ['service_name' => 'Obligation of Expenditures (Incurrence of Obligation of expenditure charged to Approved Budget Allocation Per GAA and other Budget Laws/Authority)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //44
            ['service_name' => 'Disbursement Updating', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //45
            ['service_name' => 'Downloading/Fund Transfer of SAROs Received form Central Office to SDOs and Implementing Units', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //46
            ['service_name' => 'Letter of Acceptance for Downloaded Funds', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //47
            ['service_name' => 'Processing of Budget Utilization Request and Status (BURS)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //48
        ]);

        DB::table('office_services')->insert([
            //    insert office_id 15
            ['office_id' => 15, 'service_id' => 44, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 15, 'service_id' => 45, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 15, 'service_id' => 46, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 15, 'service_id' => 47, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 15, 'service_id' => 48, 'created_by' => 1, 'updated_by' => 1,],
        ]);

        // HRDD
        DB::table('lib_services')->insert([
            ['service_name' => 'Rewards and Recognition', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //49
            ['service_name' => 'Application for Scholarships', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //50
            ['service_name' => 'Recognition of Professional Development at the NEAP Regional Office', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //51
        ]);

        DB::table('office_services')->insert([
            //    insert office_id 10
            ['office_id' => 10, 'service_id' => 49, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 10, 'service_id' => 50, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 10, 'service_id' => 51, 'created_by' => 1, 'updated_by' => 1,],
        ]);

        // PAU
        DB::table('lib_services')->insert([
            ['service_name' => 'Customer Handling (Offline/Online)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //52
            ['service_name' => 'Media Interview Request Process', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //53
        ]);

        DB::table('office_services')->insert([
            //    insert office_id 13
            ['office_id' => 13, 'service_id' => 52, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 13, 'service_id' => 53, 'created_by' => 1, 'updated_by' => 1,],
        ]);

        // CLMD
        DB::table('lib_services')->insert([
            ['service_name' => 'Access to LRMDS Portal', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //54
            ['service_name' => 'Procedure for the Use of LRMDS Computers', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //55
        ]);
        DB::table('office_services')->insert([
            //    insert office_id 6
            ['office_id' => 6, 'service_id' => 54, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 6, 'service_id' => 55, 'created_by' => 1, 'updated_by' => 1,],
        ]);

        // LEGAL UNIT
        //         1.		Filling of Complaint						       97
        //         2.		Request for Correction of Entries in School Record	       99
        //         3. 		Legal Assistance to Walk-In Clients			     101
        //         4. 		Communication Received through 
        //   Public Assistance Action Center (PAAC)			     103
        //       5.		Request for Certificates as to the Pendency or 
        //               Non-Pendency of an Administrative Case		     105

        DB::table('lib_services')->insert([
            ['service_name' => 'Filling of Complaint', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //56
            ['service_name' => 'Request for Correction of Entries in School Record', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //57
            ['service_name' => 'Legal Assistance to Walk-In Clients', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //58
            ['service_name' => 'Communication Received through Public Assistance Action Center (PAAC)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //59
            ['service_name' => 'Request for Certificates as to the Pendency or Non-Pendency of an Administrative Case', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //60
        ]);

        DB::table('office_services')->insert([
            //    insert office_id 12
            ['office_id' => 12, 'service_id' => 56, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 12, 'service_id' => 57, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 12, 'service_id' => 58, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 12, 'service_id' => 59, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 12, 'service_id' => 60, 'created_by' => 1, 'updated_by' => 1,],
        ]);

        // PPRD
        DB::table('lib_services')->insert([
            ['service_name' => 'Generation of School IDs for New Schools and / or Adding Updating of SHS Program Offering (Public, Private & SUC/LUC)', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //61
            ['service_name' => 'Request for Basic Education Information and Data', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //62
            ['service_name' => 'Request for Reversion', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //63
        ]);

        DB::table('office_services')->insert([
            //    insert office_id 23
            ['office_id' => 23, 'service_id' => 61, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 23, 'service_id' => 62, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 23, 'service_id' => 63, 'created_by' => 1, 'updated_by' => 1,],
        ]);

        // QAD
        DB::table('lib_services')->insert([
            ['service_name' => 'Application for Establishment, Merging, Conversion, and Naming/Renaming of Public Schools and Separation of Public Schools', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //64
            ['service_name' => 'Application for Special Orders (SO) of Private Schools/Technical Vocational Institutions', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //65
            ['service_name' => 'Application for Tuition and Other School Fees (TOSF), No Increase and Proposed New Fees of Private Schools', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //66
            ['service_name' => 'Application for the Opening/Additional Offering of SHS Program for Private Schools', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //67
            ['service_name' => 'Issuance of Certification as Principal Test’s Passer', 'service_description' => '', 'created_by' => 1, 'updated_by' => 1,], //68
        ]);

        DB::table('office_services')->insert([
            //    insert office_id 24
            ['office_id' => 24, 'service_id' => 64, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 24, 'service_id' => 65, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 24, 'service_id' => 66, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 24, 'service_id' => 67, 'created_by' => 1, 'updated_by' => 1,],
            ['office_id' => 24, 'service_id' => 68, 'created_by' => 1, 'updated_by' => 1,],
        ]);

        // delete all uploaded photos in the storage
        $files = Storage::disk('public')->allFiles();
        Storage::disk('public')->delete($files);



        // run the FeedbackFactory
        // Feedback::factory()->count(100)->create();
    }
}
