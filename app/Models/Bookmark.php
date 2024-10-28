<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $fillable = ['user_id', 'name', 'url', 'category'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
