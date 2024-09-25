<?php

namespace App\Models;

use App\Models\OfficeService\OfficeService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Office extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'office_level',
        'parent_id',
        'header_image',
        'report_header_image',
        'report_footer_image',
        'prepared_by_name',
        'prepared_by_position',
        'attested_by_name',
        'attested_by_position',
    ];

    public function parent()
    {
        return $this->belongsTo(Office::class, 'parent_id');
    }

    // public function children()
    // {
    //     return $this->hasMany(Office::class, 'parent_id')->orderBy('name')->with('children');
    // }
    public function children()
    {
        return $this->hasMany(Office::class, 'parent_id')->orderBy('name');
    }

    public function services()
    {
        return $this->hasMany(OfficeService::class);
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function getHierarchy()
    {
        $hierarchy = [];
        $currentOffice = $this;

        while ($currentOffice) {
            array_unshift($hierarchy, $currentOffice);
            $currentOffice = $currentOffice->parent;
        }

        return $hierarchy;
    }

    public function getHierarchyString()
    {
        $hierarchy = $this->getHierarchy();
        $hierarchyString = '';

        foreach ($hierarchy as $office) {
            $hierarchyString .= $office->name . ' > ';
        }

        return rtrim($hierarchyString, ' > ');
    }

    public function isDescendantOf($ancestorId)
    {
        $currentOffice = $this;

        while ($currentOffice) {
            if ($currentOffice->id == $ancestorId) {
                return true;
            }
            $currentOffice = $currentOffice->parent;
        }

        return false;
    }

    public function hasExternalServices()
    {
        return $this->services->where('is_external', true)->count() > 0;
    }

    public function hasInternalServices()
    {
        return $this->services->where('is_internal', true)->count() > 0;
    }
}
