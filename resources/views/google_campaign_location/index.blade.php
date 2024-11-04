@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <style>
        .select2-container.select2-container--default.select2-container--open {
            width: 100% !important;
        }

        .select2.select2-container.select2-container--default {
            width: 100% !important;
        }  

        div.pac-container {
            z-index: 99999999999 !important;
        }
    </style>
    <div class="col-md-12">
    <h4 class="page-heading">Google Locations (<span id="adsgroup_count">{{$totalNumEntries}}</span>) for {{@$campaign_name}} campaign <button class="btn-image" onclick="window.location.href='/google-campaigns?account_id={{$account_id}}'">Back to campaign</button></h4>
    <div class="pull-left">
        <div class="form-group">
            <div class="row">
                
                <div class="col-md-6">
                    <input name="address" type="text" class="form-control" value="{{ isset($address) ? $address : '' }}" placeholder="Address" id="address">
                </div>


                <div class="col-md-1">
                    <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png" /></button>
                </div>

                <div class="col-md-1">
                    <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png" /></button>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal" data-target="#new_location">New Location</button>

    <table class="table table-bordered" id="adsgroup-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Google Location Id</th>
                <th>Type</th>
                <th>Address</th>
                <th>Distance</th>
                <th>Radius Units</th>
                <th>Is Target?</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($locations as $location)
                <tr>
                    <td>{{$location->id}}</td>
                    <td>{{$location->google_location_id}}</td>
                    <td>{{$location->type}}</td>
                    <td>{{$location->address}}</td>
                    <td>{{$location->distance}}</td>
                    <td>{{$location->radius_units}}</td>
                    <td>{{$location->is_target ? "Yes" : "Exclude"}}</td>
                    <td>{{$location->created_at}}</td>
                    <td>
                        <div class="d-flex justify-content-between">
                            {{ html()->form('DELETE', route('google-campaign-location.deleteLocation', [$campaignId, $location['google_location_id']]))->style('display:inline')->open() }}
                            <button type="submit" class="btn-image"><img src="/images/delete.png"></button>
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    {{ $locations->links() }}
    </div>

    <div class="modal fade" id="new_location" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="container">
                    <div class="page-header">
                        <h2>Create Location</h2>
                    </div>
                    <form method="POST" action="/google-campaigns/{{$campaignId}}/google-campaign-location/create" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="campaignId" id="campaignId" value="{{$campaignId}}">
                        
                        <div class="form-group row m-0">
                            <div class="col-md-6 pl-0 pr-3">
                                <div class="form-group m-0 mb-3">
                                    <label for="start-date" class="col-form-label">Location</label>
                                    <div>
                                        <input type="radio" class="" name="target_location" value="all" checked> All countries and territories
                                        <input type="radio" class="" name="target_location" value="other"> Enter another location
                                        @if ($errors->has('target_location'))
                                            <span class="text-danger">{{$errors->first('target_location')}}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 pr-0 pl-0 other_location_div" style="display: none;">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-0 mb-5">
                                            <div>
                                                <input type="radio" class="" name="target_location_type" value="location" checked> Location
                                                <input type="radio" class="" name="target_location_type" value="radius"> Radius
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="advance_type_location_div" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group m-0 mb-5">
                                                <label for="country_id" class="col-form-label">Country</label>
                                                <select class="form-control" id="" name="country_id" style="height: auto">
                                                    
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group m-0 mb-5">
                                                <label for="state_id" class="col-form-label">State</label>
                                                <select class="form-control" id="" name="state_id" style="height: auto">
                                                    
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group m-0 mb-5">
                                                <label for="city_id" class="col-form-label">City</label>
                                                <select class="form-control" id="" name="city_id" style="height: auto">
                                                    
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group m-0 mb-5">
                                                <div>
                                                    <input type="radio" class="" name="is_target" value="1" checked> Target
                                                    <input type="radio" class="" name="is_target" value="0"> Exclude
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="advance_type_radius_div" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group m-0 mb-5">
                                                <label for="target_location_address" class="col-form-label">Address</label>

                                                <input type="text" class="form-control" id="target_location_address" name="target_location_address" placeholder="Enter a place name, address or coordinates">
                                                {{-- <select class="form-control" id="" name="target_location_address" style="height: auto">
                                                    
                                                </select> --}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group m-0 mb-5">
                                                <label for="target_location_distance" class="col-form-label">Distance</label>
                                                <input type="number" name="target_location_distance" class="form-control" placeholder="Distance" min="1" max="500">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group m-0 mb-5">
                                                <label for="target_location_radius_units" class="col-form-label">Radius Units</label>
                                                <select class="form-control" id="" name="target_location_radius_units" style="height: auto">
                                                    <option value="mi">mi</option>
                                                    <option value="km">km</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row m-0">
                            <div class="col-md-12 pl-0 pr-3">
                                <div class="form-group m-0 mb-3">
                                    <span for="start-date" class="col-form-label">Note: If you select "All countries and territories" then all the location details will be removed and the location value will be set to all countries.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <button type="button" class="float-right ml-2" data-dismiss="modal" aria-label="Close">Close</button>
                                <button type="submit" class="mb-2 float-right">Create</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">
    $('.select-multiple').select2({width: '100%'});

    function submitSearch(){
        src = '/google-campaigns/{{ $campaignId }}/google-campaign-location';
        address = $('#address').val();

        $.ajax({
            url: src,
            dataType: "json",
            data: {
                address : address,
            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#adsgroup-table tbody").empty().html(data.tbody);
            $("#adsgroup_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });   
    }

    function resetSearch(){
        src = '/google-campaigns/{{ $campaignId }}/google-campaign-location';
        blank = ''
        $.ajax({
            url: src,
            dataType: "json",
            data: {
               
               blank : blank, 

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $('#address').val('');

            $("#adsgroup-table tbody").empty().html(data.tbody);
            $("#adsgroup_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }
</script>
@endsection


{{-- Start Target Locations --}}
@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{@$google_map_api_key}}&libraries=places"></script>

<script>

    function gm_authFailure() {
        toastr["error"]('Google maps failed to load!');
    }

    function initialize() {
      var input = document.getElementById('target_location_address');
      new google.maps.places.Autocomplete(input);
    }

    google.maps.event.addDomListener(window, 'load', initialize);

    $(document).ready(function() {
        $(document).on('change', '[name="target_location"]', function(event) {
            event.preventDefault();

            if($('[name="target_location"]:checked').val() == "other"){
                $('.other_location_div').show();
                $('.advance_type_location_div').show();
            }else{
                $('.other_location_div').hide();
                $('.advance_type_location_div').hide();
            }
        });

        $(document).on('change', '[name="target_location_type"]', function(event) {
            event.preventDefault();

            if($('[name="target_location_type"]:checked').val() == "location"){
                $('.advance_type_location_div').show();
                $('.advance_type_radius_div').hide();
            }else{
                $('.advance_type_location_div').hide();
                $('.advance_type_radius_div').show();
            }
        });

        $('[name="country_id"]').select2({
            ajax: {
                url: '{{ route('google-campaign-location.countries') }}',
                dataType: 'json',
                delay: 250, // wait 250 milliseconds before triggering the request
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1
                    }
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data.result,
                        pagination: data.pagination
                    };
                },
            },
            cache: true,
            allowClear: true,
            placeholder: 'Select a country',
        });

        $('[name="state_id"]').select2({
            ajax: {
                url: '{{ route('google-campaign-location.states') }}',
                dataType: 'json',
                delay: 250, // wait 250 milliseconds before triggering the request
                data: function (params) {
                    var query = {
                        search: params.term,
                        country_id: $('[name="country_id"]').select2().find(":selected").val(),
                        page: params.page || 1
                    }
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data.result,
                        pagination: data.pagination
                    };
                },
            },
            cache: true,
            placeholder: 'Select a state',
        });

        $('[name="city_id"]').select2({
            ajax: {
                url: '{{ route('google-campaign-location.cities') }}',
                dataType: 'json',
                delay: 250, // wait 250 milliseconds before triggering the request
                data: function (params) {
                    var query = {
                        search: params.term,
                        state_id: $('[name="state_id"]').select2().find(":selected").val(),
                        page: params.page || 1
                    }
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data.result,
                        pagination: data.pagination
                    };
                },
            },
            cache: true,
            placeholder: 'Select a city',
        });

        /*$('[name="target_location_address"]').select2({
            // dropdownParent: $("#create-compaign"),
            ajax: {
                url: '{{ route('google-campaign-location.address') }}',
                dataType: 'json',
                delay: 250, // wait 250 milliseconds before triggering the request
                data: function (params) {
                    var query = {
                        search: params.term,
                        account_id: {{ $account_id }},
                    }
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                },
            },
            cache: true,
            minimumInputLength: 3,
            placeholder: 'Enter a place name, address or coordinates',
        });*/
    });
</script>
@endpush 
{{-- End Target Locations --}}
