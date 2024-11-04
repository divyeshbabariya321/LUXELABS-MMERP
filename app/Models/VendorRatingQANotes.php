<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorRatingQANotes extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'question_id', 'notes', 'user_id'];
}
