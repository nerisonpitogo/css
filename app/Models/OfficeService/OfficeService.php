<?php

namespace App\Models\OfficeService;

use App\Models\LibService\LibService;
use App\Models\Office;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeService extends Model
{
    use HasFactory;
    protected $fillable = ['office_id', 'service_id', 'has_cc', 'created_by', 'updated_by'];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function service()
    {
        return $this->belongsTo(LibService::class);
    }
}
