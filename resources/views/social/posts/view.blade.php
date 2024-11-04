@extends('layouts.app')

@section('title', __('Posts'))

@section('content')

<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">View Post<span class="count-text"></span></h2>

            <div class="modal-body">
                <div class="form-group">
                    <label>Images</label>
                    {{-- Need to implement after Image upload fix --}}
                </div>
                <div class="form-group">
                    <label>Video</label>
                    {{-- Need to implement feature --}}
                </div>

                <div class="form-group">
                    <label for="">Message(Caption)</label>
                    <div>
                        {{ $post->caption }}
                    </div>
                </div>

                <div class="form-group">
                    <label for="">Hashtags
                    </label>
                    <div>
                        {{ $post->hashtag }}
                    </div>
                </div>

                <div class="form-group">
                    <label for="">Post Date
                    </label>
                    <div>
                        {{ $post->posted_on }}
                    </div>
                </div>

            </div>
    </div>
</div>

@endsection