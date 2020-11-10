<?php

namespace App\Models\Authentication;

use App\Models\Ignug\Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shortcut extends Model
{
    use HasFactory;

    const TYPE = 'SHORTCUTS';

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
