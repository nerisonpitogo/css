<?php

namespace App\Models;

use App\Models\LibRegion\LibRegion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeRegion extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'office_id',
        'is_priority',
    ];

    public function region()
    {
        return $this->belongsTo(LibRegion::class, 'region_id', 'id');
    }
}
