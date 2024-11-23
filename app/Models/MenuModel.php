<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuModel extends Model
{
    use HasFactory;
    protected $table='menu';
    protected $fillable=[
        'parent_menu',
        'menu',
        'link',
        'position'
    ];
    public function children()
    {
        return $this->hasMany(MenuModel::class, 'parent_menu')->orderBy('position');
    }

    public function parent()
    {
        return $this->belongsTo(MenuModel::class, 'parent_menu');
    }
}
