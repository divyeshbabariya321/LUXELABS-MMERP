<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostSocialRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
'message'  => [
                'required',
            ],
'source.*' => [
                'mimes:jpeg,bmp,png,gif,tiff',
            ],
'video'    => [
                'mimes:3g2,3gp,3gpp,asf,avi,dat,divx,dv,f4v,flv,gif,m2ts,m4v,mkv,mod,mov,mp4,mpe, mpeg,mpeg4,mpg,mts,nsv,ogm,ogv,qt,tod,tsvob,wmv',
            ],
];
    }
}
