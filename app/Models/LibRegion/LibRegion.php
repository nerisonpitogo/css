<?php

namespace App\Models\LibRegion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibRegion extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'created_by', 'updated_by'];
}
