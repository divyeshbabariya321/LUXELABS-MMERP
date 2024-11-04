@extends('layouts.app')
@section('favicon' , 'task.png')
@section('styles')
<style type="text/css">
    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
</style>
@endsection
@section('content')
    <div class="container" style="margin-top: 10px">
        <h4>Google App Ads View<button class="btn-image custom-button float-right" onclick="window.location.href='/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/app-ad';">Back</button></h4>

        <table class="table table-bordered" id="ads-table" style="margin-top: 40px">
            <tbody>
                <tr>
                    <th>#ID</th>
                    <td>{{$record->id}}</td>
                </tr>
                <tr>
                    <th>Headline 1</th>
                    <td>{{$record->headline1}}</td>
                </tr>
                <tr>
                    <th>Headline 2</th>
                    <td>{{$record->headline2}}</td>
                </tr>
                <tr>
                    <th>Headline 3</th>
                    <td>{{$record->headline3}}</td>
                </tr>
                <tr>
                    <th>Description 1</th>
                    <td>{{$record->description1}}</td>
                </tr>
                <tr>
                    <th>Description 2</th>
                    <td>{{$record->description2}}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{$record->status}}</td>
                </tr>
                <tr>
                    <th>Images</th>
                    <td>
                        @foreach($record->images as $image)
                            <img src="{{ asset('storage/google_ads/app_ad/'.$account_id.'/'.$image->name) }}" height="100" width="100">
                        @endforeach
                        @if(!count($record->images))
                        N/A
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Youtube Video Ids</th>
                    <td>{{$record->youtube_video_ids}}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">

</script>

@endsection
