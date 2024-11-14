<?php

namespace App\Models;

use App\Models\LibRegion\LibRegion;
use App\Models\OfficeService\OfficeService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    // table name
    protected $table = 'feedbacks';

    // guarded none
    protected $guarded = [];

    public function officeService()
    {
        return $this->belongsTo(OfficeService::class);
    }

    public function region()
    {
        return $this->belongsTo(LibRegion::class);
    }
}
