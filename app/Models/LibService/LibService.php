<?php

namespace App\Models\LibService;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibService extends Model
{
    use HasFactory;
    protected $fillable = ['service_name', 'service_description', 'created_by', 'updated_by'];
}
