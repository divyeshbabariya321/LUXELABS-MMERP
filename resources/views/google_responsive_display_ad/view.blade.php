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
        <h4>Google Responsive Display Ads View<button class="btn-image custom-button float-right" onclick="window.location.href='/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/responsive-display-ad';">Back</button></h4>

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
                    <th>Final URL</th>
                    <td>{{$record->final_url}}</td>
                </tr>
                <tr>
                    <th>Long headline</th>
                    <td>{{$record->long_headline}}</td>
                </tr>
                <tr>
                    <th>Business name</th>
                    <td>{{$record->business_name}}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{$record->status}}</td>
                </tr>
                <tr>
                    <th>Marketing Images</th>
                    <td>
                        @foreach($record->marketing_images as $image)
                            <img src="{{ asset('storage/google_ads/responsive_display_ad/'.$account_id.'/'.$image->name) }}" height="100" width="100">
                        @endforeach
                    </td>
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
