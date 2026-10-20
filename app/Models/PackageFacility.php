<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageFacility extends Model
{
    protected $fillable = ['package_id', 'item_name', 'qty'];

    public function package()
    {
        return $this->belongsTo(ServicePackage::class, 'package_id');
    }
}