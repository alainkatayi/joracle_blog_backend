<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title', 'content', 'slug','user_id','created_at'
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}
}
