<?php $base_url = URL::to('/'); ?>
<div class="panel-body">
    <table class="table table-bordered table-striped">
        <tr>
            <th>Page URI</th>
            <th>Page Title</th>
            <th>Page Description</th>
            <th>Status</th>
            @if(auth()->user()->isAdmin())
                <th>Email Alerts</th>
            @endif
            <th>Action</th>
        </tr>
        @foreach ($routesData as $data )
            <tr>
                <td width="40%"><a href="{{$base_url.'/'.$data->url}}"
                    target="_blank" >{{$base_url.'/'.$data->url}}</a></td>
                <td width="20%">{{$data->page_title}}</td>
                <td width="20%">{{$data->page_description}}</td>
                <td width="10%">
                    <select class="form-control status-change" name="status" data-url="{{ route('routes.update',$data->id) }}">
                        <option value="">Select Status</option>
                        <option value="active" {{ 'active' == $data->status ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ 'inactive' == $data->status ? 'selected' : '' }}>InActive</option>
                    </select>
                </td>
                @if(auth()->user()->isAdmin())
                    <td width="5%">
                        <label class="switchAN" title="Email Alerts Enable">
                            <input data-id={{$data->id}} class="switchEnableEmailAlertRoute" type="checkbox" @if($data->email_alert) {{'checked'}} @endif>
                            <span class="slider round"></span>
                            <span class="switchEnableEmailAlertRouteText text @if($data->email_alert) {{'textLeft'}} @else {{'textRight'}} @endif">
                                @if($data->email_alert) {{'On'}} @else {{'Off'}} @endif
                            </span>
                        </label>
                    </td>
                @endif
                <td width="5%"><a class="btn btn-default"
                    href="{{ route('routes.update',$data->id) }}">Edit</a></td>
            </tr>
        @endforeach
    </table>

    {{ $routesData->links() }}
</div>
