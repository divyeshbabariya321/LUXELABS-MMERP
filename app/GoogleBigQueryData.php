<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleBigQueryData extends Model
{

    protected function casts(): array
    {
        return [
            'device'            => 'array',
            'memory'            => 'array',
            'storage'           => 'array',
            'operating_system'  => 'array',
            'application'       => 'array',
            'custom_keys'       => 'array',
            'installation_uuid' => 'array',
            'logs'              => 'array',
            'breadcrumbs'       => 'array',
            'blame_frame'       => 'array',
            'exceptions'        => 'array',
            'errors'            => 'array',
            'threads'           => 'array',
        ];
    }
}
