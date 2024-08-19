<?php

namespace App\Models\Sqd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sqd extends Model
{
    use HasFactory;
    protected $fillable = ['office_id', 'language', 'is_onsite', 'header', 'client_type', 'citizen', 'business', 'government', 'date', 'sex', 'male', 'female', 'age', 'region', 'sqd0', 'sqd1', 'sqd2', 'sqd3', 'sqd4', 'sqd5', 'sqd6', 'sqd7', 'sqd8', 'cc1', 'cc1_1', 'cc1_2', 'cc1_3', 'cc1_4', 'cc2', 'cc2_1', 'cc2_2', 'cc2_3', 'cc2_4', 'cc2_5', 'cc3', 'cc3_1', 'cc3_2', 'cc3_3', 'cc3_4', 'suggestion', 'email_address', 'created_by', 'updated_by'];
}
