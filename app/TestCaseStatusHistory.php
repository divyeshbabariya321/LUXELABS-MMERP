<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestCaseStatusHistory extends Model
{
    protected $table = 'test_case_status_history';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];
}
