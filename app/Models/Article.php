<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table='articles';
    protected $primaryKey='article_id';

    public function comments(){
        return $this->hasMany('App\Models\Comment','article_id');
    }
    public function tags(){
        return $this->hasMany('App\Models\Tag','article_id');
    }
}
