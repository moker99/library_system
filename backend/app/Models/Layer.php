<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layer extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function shelf() {
        return $this->belongsTo(Shelf::class);
    }
    public function books() {
        return $this->hasMany(Book::class);
    }
}
