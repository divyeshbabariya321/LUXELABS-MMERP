<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetManagerLinkUser extends Model
{
    use HasFactory;

    protected $table = 'assets_manager_link_user';

    protected $fillable = ['user_id', 'asset_manager_id', 'created_at'];
}
