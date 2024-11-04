<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorQuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'vendor_id', 'answer'];
}
