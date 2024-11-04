<?php

namespace App\Http\Controllers;

use App\Services\Instagram\Instagram;
use Illuminate\Http\Request;

//Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class InstagramProfileController extends Controller
{
    // this methos is used in blade file layouts\app.blade.php
    public function index() {}

    // this methos is used in blade file instagram\comp\followers.blade.php, instagram\comp\grid.blade.php, instagram\profile\hashtags.blade.php, instagram\profile\list.blade.php
    public function show($id, Request $request) {}

    // this methos is used in blade file instagram\comp\followers.blade.php, instagram\comp\grid.blade.php, instagram\profile\list.blade.php
    public function add(Request $request) {}

    // this methos is used in blade file layouts\app.blade.php
    public function edit($d) {}
}
