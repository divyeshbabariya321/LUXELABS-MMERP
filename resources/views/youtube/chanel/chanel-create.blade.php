@extends('layouts.app')
@section('title', 'Channel Create')
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

    legend {
        display: block;
        width: auto;
        max-width: 100%;
        padding: 0;
        margin-bottom: 0;
        font-size: 1.5rem;
        line-height: inherit;
        color: inherit;
        white-space: normal;
        border-bottom:none !important;
    }
    fieldset {
        padding: 10px 10px;
        margin: 20px 2px;
        border: 1px solid #c0c0c07a;
        border-radius: 4px;
    }
</style>
@endsection
@section('content')
<div class="container " style="max-width: 100%;width: 100%;">
    <div class="row">
    <div class="col-md-12 p-0">
    <h4 class="page-heading">Youtube Channel Count (<span id="ads_account_count">{{ $totalentries }}</span>)</h4>
    </div>
    </div>
    <div class="pull-left">
        <div class="form-group">
            <div class="row"> 
                <div class="col-md-5">
                    <select class="form-control select-multiple" id="website-select">
                        <option value="">Select Store Website</option>
                        @foreach($store_website as $key => $sw)
                        <option value="{{ $sw->website }}">{{ $sw->website }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <input name="accountname" type="text" class="form-control" value="{{ isset($accountname) ? $accountname : '' }}" placeholder="Channel Name" id="accountname">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn mt-0 btn-image" onclick="submitSearch()"><img src="/images/filter.png" /></button>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn mt-0 btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png" /></button>
                </div>
            </div>
        </div>
    </div>


    <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal" data-target="#newaccountmodal">New Channel</button>

    <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal" data-target="#refreshTokenModal">Generate Access/Refresh Token</button>

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="adsaccount-table">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Channel Name</th>
                    <th>Store Website</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($googleadsaccount as $googleadsac)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$googleadsac->chanel_name}}</td>
                    <td>{{$googleadsac->store_websites}}</td>
                    <td>{{$googleadsac->status}}</td>
                    <td>{{$googleadsac->created_at}}</td>
                    <td>
                        <button type="button" onclick="editaccount('{{$googleadsac->id}}')" class="btn-image" data-toggle="modal" data-target="#EditModal"><img src="{{asset('/images/edit.png')}}"></button>
                        <button type="button" class="btn-image btn-primary"><a style="color:#fff" href="video-upload/{{$googleadsac->id}}">PostVideo</a></button>
                        <button type="button" class="btn-image btn-primary"><a style="color:#fff" href="list-video/{{$googleadsac->id}}">ListVideo</a></button>

                        @if(Auth::user()->hasRole('Admin'))
                        {{ html()->form('DELETE', route('googleadsaccount.deleteGoogleAdsAccount', [$googleadsac->id]))->style('display:inline')->open() }}
                        {{ html()->form()->close() }}
                        @endif
                        
                        
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $googleadsaccount->links() }}
</div>

<div class="modal fade" id="refreshTokenModal" role="dialog" style="z-index: 3000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('youtubeaccount.refresh_token') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Generate Access/Refresh Token</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-4 mb-4">
                        <label>Client ID</label>
                    </div>
                    <div class="col-md-8 mb-4">
                        <input type="input" class="form-control" name="client_id" required />
                    </div>
                    <br />
                    <div class="col-md-4 mb-4">
                        <label>Client Secret</label>
                    </div>
                    <div class="col-md-8 mb-4">
                        <input type="input" class="form-control" name="client_secret" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="newaccountmodal" role="dialog" style="z-index: 3000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="container">
                <div class="page-header" style="width: 69%">
                    <h4>Create Channel</h4>
                </div>
                <form action="{{ route('youtubeaccount.createChanel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                   

                    <div class="form-group row">
                        <label for="store_websites" class="col-sm-2 col-form-label">Store Websites</label>
                        <div class="col-sm-6">
                            <select class="browser-default custom-select" id="store_websites" name="store_websites" required="required" style="height: auto">
                                <option value="" selected>---Selecty store websites---</option>
                                @foreach($store_website as $sw)
                                    <option value="{{$sw->website}}">{{$sw->website}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('store_websites'))
                                <span class="text-danger">{{$errors->first('store_websites')}}</span>
                            @endif
                        </div>
                    </div>

                 

                    <fieldset>
                        <legend class="lagend">Youtube Channel</legend>
                       

                        <div class="form-group row">
                            <label for="email" class="col-sm-2 col-form-label">Email Address</label>
                            <div class="col-sm-6">
                                <input type="text" required="required" class="form-control" id="email" name="email" placeholder="Email Address">
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{$errors->first('email')}}</span>
                                @endif
                            </div>
                        </div>
                         
                       
                    </fieldset>

                    <fieldset>
                        <legend class="lagend">OAuth2</legend>
                        <div class="form-group row">
                            <label for="oauth2_client_id" class="col-sm-2 col-form-label">Client Id</label>
                            <div class="col-sm-6">
                                <input type="text" required="required" class="form-control" id="oauth2_client_id" name="oauth2_client_id" placeholder="Client Id">
                                @if ($errors->has('oauth2_client_id'))
                                    <span class="text-danger">{{$errors->first('oauth2_client_id')}}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="oauth2_client_secret" class="col-sm-2 col-form-label">Client Secret</label>
                            <div class="col-sm-6">
                                <input type="text" required="required" class="form-control" id="oauth2_client_secret" name="oauth2_client_secret" placeholder="Client Secret">
                                @if ($errors->has('oauth2_client_secret'))
                                    <span class="text-danger">{{$errors->first('oauth2_client_secret')}}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="oauth2_refresh_token" class="col-sm-2 col-form-label">Refresh Token</label>
                            <div class="col-sm-6">
                                <input type="text" required="required" class="form-control" id="oauth2_refresh_token" name="oauth2_refresh_token" placeholder="Refresh Token">
                                @if ($errors->has('oauth2_refresh_token'))
                                    <span class="text-danger">{{$errors->first('oauth2_refresh_token')}}</span>
                                @endif
                            </div>
                        </div>
                    </fieldset>

                    <div class="form-group row">
                        <label for="status" class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-6">
                            <select required="required" class="browser-default custom-select" id="status" name="status" style="height: auto">
                                <option value="ENABLED" selected>ENABLED</option>
                                <option value="DISABLED">DISABLED</option>
                            </select>
                            @if ($errors->has('status'))
                                <span class="text-danger">{{$errors->first('status')}}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-8">
                            <button type="button" class="float-right ml-2" data-dismiss="modal" aria-label="Close">Close</button>
                            <button type="submit" class="mb-2 float-right">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="EditModal"  role="dialog" style="z-index: 3000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="EditModalLabel">Edit Channel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="/youtube/channel/update" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="account_id" name="account_id" placeholder="Account Id" value="">
                  

                    <div class="form-group row">
                        <label for="store_websites" class="col-sm-2 col-form-label">Store Website</label>
                        <div class="col-sm-10">
                            <select class="browser-default custom-select" id="edit_store_websites" required="required" name="store_websites" style="height: auto">
                                <option value="" selected>---Selecty store websites---</option>
                                @foreach($store_website as $sw)
                                    <option value="{{$sw->website}}" >{{$sw->website}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('store_websites'))
                                <span class="text-danger">{{$errors->first('store_websites')}}</span>
                            @endif
                        </div>
                    </div>

                    <fieldset>
                         <legend class="lagend">Youtube Channel</legend>
                        

                        <div class="form-group row">
                            <label for="edit_google_adwords_client_account_email" class="col-sm-2 col-form-label">Email Address</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" required="required" id="email" name="email" placeholder="Email Address">
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{$errors->first('email')}}</span>
                                @endif
                            </div>
                        </div>

                        

                    </fieldset>


                    <fieldset>
                        <legend class="lagend">OAuth2</legend>
                        <div class="form-group row">
                            <label for="edit_oauth2_client_id" class="col-sm-2 col-form-label">Client Id</label>
                            <div class="col-sm-10">
                                <input type="text" required="required" class="form-control" id="edit_oauth2_client_id" name="oauth2_client_id" placeholder="Client Id">
                                @if ($errors->has('oauth2_client_id'))
                                    <span class="text-danger">{{$errors->first('oauth2_client_id')}}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="edit_oauth2_client_secret" class="col-sm-2 col-form-label">Client Secret</label>
                            <div class="col-sm-10">
                                <input type="text" required="required" class="form-control" id="edit_oauth2_client_secret" name="oauth2_client_secret" placeholder="Client Secret">
                                @if ($errors->has('oauth2_client_secret'))
                                    <span class="text-danger">{{$errors->first('oauth2_client_secret')}}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="edit_oauth2_refresh_token" class="col-sm-2 col-form-label">Refresh Token</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" required="required" id="edit_oauth2_refresh_token" name="oauth2_refresh_token" placeholder="Refresh Token">
                                @if ($errors->has('oauth2_refresh_token'))
                                    <span class="text-danger">{{$errors->first('oauth2_refresh_token')}}</span>
                                @endif
                            </div>
                        </div>
                    </fieldset>

                    <div class="form-group row">
                        <label for="status" class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <select class="browser-default custom-select" required="required" id="edit_status" name="status" style="height: auto">
                                <option value="ENABLED">ENABLED</option>
                                <option value="DISABLED">DISABLED</option>
                            </select>
                            @if ($errors->has('status'))
                                <span class="text-danger">{{$errors->first('status')}}</span>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="float-right ml-2" data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="submit" class="mb-2 float-right">Update</button>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="PostVideoModal"  role="dialog" style="z-index: 3000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="EditModalLabel">Post Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    
                    <input type="hidden" name="channelId" id="channelId" val="">

                    <fieldset>
                         <legend class="lagend">Post video</legend>
                        

                        <div class="form-group row">
                            <label for="title" class="col-sm-2 col-form-label">Title</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" required="required" id="title_youtube" name="title" placeholder="title">
                                @if ($errors->has('title'))
                                    <span class="text-danger">{{$errors->first('title')}}</span>
                                @endif
                            </div>
                        </div>

                         <div class="form-group row">
                            <label for="youtubeVideo" class="col-sm-2 col-form-label">Upload Video</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" required="required" id="uploadVideo" name="uploadVideo" placeholder="uploadVideo">
                                @if ($errors->has('uploadVideo'))
                                    <span class="text-danger">{{$errors->first('title')}}</span>
                                @endif
                            </div>
                        </div>

                    </fieldset>



                    <div class="form-group row">
                        <label for="status" class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <select class="browser-default custom-select" required="required" id="edit_status" name="status" style="height: auto">
                                <option value="ENABLED">ENABLED</option>
                                <option value="DISABLED">DISABLED</option>
                            </select>
                            @if ($errors->has('status'))
                                <span class="text-danger">{{$errors->first('status')}}</span>
                            @endif
                        </div>
                    </div>
                    
                    <button type="button" class="float-right ml-2" data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="submit" class="mb-2 float-right">Update</button>
                </form>
            </div>
            <div class="modal-footer">
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
        src = '/youtube/add-chanel'
        accountname = $('#accountname').val();
        website = $('#website-select').val();
        $.ajax({
            url: src,
            dataType: "json",
            data: {
                accountname : accountname,
                website : website,

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#adsaccount-table tbody").empty().html(data.tbody);
            $("#ads_account_count").text(data.count);
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
        src = '/youtube/add-chanel'
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
            $('#accountname').val('');
            $('#website-select').val('');
            $("#adsaccount-table tbody").empty().html(data.tbody);
            $("#ads_account_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }

    function renderChanelId(chanelId)
    {
       $('#PostVideoModal #channelId').val(chanelId);
    }

     
    

    function editaccount(id) {
        $('#EditModal').hide();
        var url = "{{ route('youtubeaccount.editChannel', ":id") }}";
        url = url.replace(':id', id);
        $.ajax({
            method: "GET",
            url: url,
            dataType: "json",
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function (data) {
                $("#account_id").val(data.id);
                $("#edit_account_name").val(data.account_name);
                $('#edit_google_customer_id').val(data.google_customer_id);
                $('#edit_store_websites').val(data.store_websites);
                $('#edit_notes').val(data.notes);
                $('#edit_config_file_path').val(data.config_file_path);
                $('#edit_status').val(data.status);

                $('#EditModal #email').val(data.email);
                $('#EditModal #chanel_name').val(data.chanel_name);
                $('#edit_google_adwords_client_account_password').val(data.google_adwords_client_account_password);
                $('#edit_google_adwords_manager_account_customer_id').val(data.google_adwords_manager_account_customer_id);
                $('#edit_google_adwords_manager_account_developer_token').val(data.google_adwords_manager_account_developer_token);
                $('#edit_google_adwords_manager_account_email').val(data.google_adwords_manager_account_email);
                $('#edit_google_adwords_manager_account_password').val(data.google_adwords_manager_account_password);
                $('#edit_oauth2_client_id').val(data.oauth2_client_id);
                $('#edit_oauth2_client_secret').val(data.oauth2_client_secret);
                $('#edit_oauth2_refresh_token').val(data.oauth2_refresh_token);

                $('#EditModal').show();
            }
        });
    }
</script>

@endsection
