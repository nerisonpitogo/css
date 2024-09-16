<?php

namespace App\Models\OfficeService;

use App\Models\LibService\LibService;
use App\Models\Office;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeService extends Model
{
    use HasFactory;
    protected $fillable = [
        'office_id',
        'service_id',
        'created_by',
        'updated_by',
        'is_simple',
        'has_sqd0',
        'has_sqd1',
        'has_sqd2',
        'has_sqd3',
        'has_sqd4',
        'has_sqd5',
        'has_sqd6',
        'has_sqd7',
        'has_sqd8',
        'allow_na',
        'is_external',
        'is_internal',

    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function service()
    {
        return $this->belongsTo(LibService::class);
    }
}
