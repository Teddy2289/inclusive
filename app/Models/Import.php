<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $fillable = [
        'filename',
        'original_filename',
        'rows_imported',
        'rows_skipped',
        'status',
        'error_message',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
