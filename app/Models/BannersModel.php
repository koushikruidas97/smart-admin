<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannersModel extends Model
{
    use HasFactory;
    protected $table='banners';
    protected $fillable=[
        'menu',
        'title',
        'description',
        'link',
        'image',
    ];
}
