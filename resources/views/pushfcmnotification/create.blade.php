@extends('layouts.app')


@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Create Notification</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-secondary" href="{{ route('pushfcmnotification.list') }}"> Back</a>
        </div>
    </div>
</div>


@if (count($errors) > 0)
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif



{{ html()->form('POST', route('pushfcmnotification.store'))->open() }}
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Notification Title:</strong>
            {{ html()->text('title')->placeholder('Name')->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Notification Website:</strong>
            <select name="url" class="form-control">
                <option value="">Select Website</option>
                @foreach($StoreWebsite as $website)
                <option value="{{$website->website}}" {{ ($website->website == old('url'))?'selected':''}}>{{$website->website}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Notification body:</strong>
            {{ html()->textarea('body')->placeholder('body')->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Sent At:</strong>
            {{ html()->text('sent_at')->placeholder('time to send notification at')->class('form-control')->id('sent_at_fcm_create') }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
        <button type="submit" class="btn btn-secondary">+</button>
    </div>
    
</div>
{{ html()->form()->close() }}


@endsection
@section('scripts')
<script type="text/javascript">
    $(function () {
        $('#sent_at_fcm_create').datetimepicker({
            format: 'Y-MM-DD HH:mm',
            stepping: 5
        });
    });
</script>
@endsection
