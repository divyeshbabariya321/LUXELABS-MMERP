@extends('layouts.app')

@section('large_content')
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-typeahead.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>

    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

        tr th {
            width: 20px;
        }

        .nav-item a {
            color: #555;
        }

        a.btn-image {
            padding: 2px 2px;
        }

        .text-nowrap {
            white-space: nowrap;
        }

        .search-rows .btn-image img {
            width: 12px !important;
        }

        .search-rows .make-remark {
            border: none;
            background: none
        }

        table.csvData-table tbody tr td div{
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        table.csvData-table tbody tr td .bg-success{
            padding: 5px !important;
        }

        table.csvData-table tbody tr td .bg-custom-grey{
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            max-width: 150px;
        }
        .csvData-table tbody td {
            padding-bottom: 10px !important;
        }

        .viewbtn_model {
            top: 5px !important;
        }

        .history_model {
            top: 10px;
        }

        @media (max-width: 1280px) {
            table.table {
                width: 0px;
                margin: 0 auto;
            }

            /** only for the head of the table. */
            table.table thead th {
                padding: 10px;
            }

            /** only for the body of the table. */
            table.table tbody td {
                padding: 10 px;
            }

            .text-nowrap {
                white-space: normal !important;
            }
        }
    </style>
@endsection

<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>
<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">GoogleFile Translator Datas</h2>

        <?php $type = request()->route('type');
        $id = request()->route('id');
        ?>

        <form action="{{ route('googlefiletranslator.list-page.view', ['id' => $id , 'type' => $type]) }}" method="get" class="search">

            <div class="col-lg-2">
                <input class="form-control" type="text" id="search_keyword" placeholder="Search keywords" name="search_keyword" value="{{ (request('search_keyword') ?? "" )}}">
            </div>
            <div class="col-lg-2">
                <input class="form-control" type="text" id="search_msg" placeholder="Search message" name="search_msg" value="{{ (request('search_msg') ?? "" )}}">
            </div>
            <div class="col-lg-2">
                <input class="form-control" type="text" id="search_stand_value" placeholder="Search Standard value" name="search_stand_value" value="{{ (request('search_stand_value') ?? "" )}}">
            </div>
            <div class="col-lg-2">
                <input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
            </div>
            <div class="col-lg-2">
                <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                   <img src="{{ asset('images/search.png') }}" alt="Search">
               </button>
               <a href="{{ route('googlefiletranslator.list-page.view', ['id' => $id , 'type' => $type]) }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </form>
    </div>
</div>
<div class="row">
@include('partials.flash_messages', ['withWidth' => true])
</div>
<div class="row w-100">
    <div class="col-md-2">
       {{-- <label>Language</label> 
       <select class="form-control" name="lang_filter" id="lang_filter">
            <option value="">Select</option>
            <option value="en">EN</option>
            <option value="es">ES</option>
            <option value="ru">RU</option>
            <option value="ko">KO</option>
            <option value="ja">JA</option>
            <option value="it">IT</option>
            <option value="de">DE</option>
            <option value="fr">FR</option>
            <option value="nl">NL</option>
            <option value="zh">ZH</option>
            <option value="ar">AR</option>
            <option value="ur">UR</option>
        </select> --}}
    </div>
    <div class="col-md-2">
    {{-- <label>Status</label>
        <select class="form-control" name="status_filter" id="status_filter">
            <option value="">Status</option>
            <option value="checked">checked</option>
            <option value="unchecked">unchecked</option>
            <option value="">others</option>
        </select> --}}
    </div>
    <div class="col-md-2">
    {{-- <label>Users</label>
        <select class="form-control" name="users_filter" id="users_filter">
            <option value="">Select</option>
            @php
            use App\User;    
            @endphp
            @foreach (User::all() as $users)
                <option value="{{$users->id}}">{{$users->name}}</option>
            @endforeach
        </select> --}}
    </div>
</div>

<div class="float-right my-3">
    <?php $type = request()->route('type'); ?>
    @if(auth()->user()->hasRole('Lead Translator') || auth()->user()->hasRole('Admin'))
    <a class="btn btn-secondary text-white btn_select_user" data-toggle="modal" data-target="#permissions_model">Permission</a>
    <a class="btn btn-secondary text-white btn_download_approved" data-id ={{$id}} data-type ={{$type}} onclick="return confirm('{{ __('Are you sure you want to Give a download Permission') }}')" >Approve To download</a>
    @endif
</div>


<div class="modal fade" id="permissions_model" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Edit Permission</h4>
            </div>
            <div class="modal-body">
                <form class="permission_form">
                    <div class="form-group">
                        <label>Select User :</label>
                        <select class="form-control" id="selectUserId" name="user">
                            <option>Select</option>
                            @foreach ($activeUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Lanuage</label>
                        <select class="form-control" name="lang" id="selectLangId">
                            <option>Select</option>
                            <option value="en">EN</option>
                            <option value="es">ES</option>
                            <option value="ru">RU</option>
                            <option value="ko">KO</option>
                            <option value="ja">JA</option>
                            <option value="it">IT</option>
                            <option value="de">DE</option>
                            <option value="fr">FR</option>
                            <option value="nl">NL</option>
                            <option value="zh">ZH</option>
                            <option value="ar">AR</option>
                            <option value="ur">UR</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Action</label>
                        <select class="form-control" name="action" id="actionId">
                            <option>Select</option>
                            <option value="view">view</option>
                            <option value="edit">edit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Permission Type</label>
                        <select class="form-control" name="type" id="typeId">
                            <option>Select</option>
                            <option value="basic">Basic</option>
                            <option value="advance">Advance</option>
                        </select>
                    </div>
                    <div class="d-none alert alert-class">

                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-permission-submit-form">Add
                    Permission</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="Show_message_display" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Csv Translator</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered text-wrap w-auto min-w-100">
                    <thead>
                    <tr>
                        <th>Message</th>
                    </tr>
                    </thead>
                    <tbody class="chat_message_history">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="magneto-frontend-historylist" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 95%;width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Google Translate History</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">

                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="25%">Old value</th>
                                <th width="25%">New Value</th>
                                <th width="25%">Updated By</th>                   
                            </tr>
                        </thead>
                        <tbody class="magneto-historylist-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="magneto-frontend-historyliststatus" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 95%;width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Google Translate History</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">

                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="25%">New Status</th>
                                <th width="25%">Updated By</th>                   
                            </tr>
                        </thead>
                        <tbody class="magneto-historyliststatus-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="mt-3 col-md-12">
    <table class="table table-bordered table-striped" id="log-table">
        <thead>
            <tr>
                <th width="3%">ID</th>
                <th width="3%">KeyWords</th>
                <th width="45%">Message</th>
                <th width="45%">Standard Value</th>
                <th width="5%">Translate Score</th>
                <th width="8%">Date</th>
                <th width="5%">Action</th>
             </tr>
            <tbody>
                @foreach ($googleTranslateDatas as $data)
                    <tr>
                        <td>{{$data->id}}</td>
                        <td><div class="show_csv_co">{{$data->key}}</div> 
                        </td>
                        <td>
                            <div class="show_csv_co">
                                {{$data->value}}
                            </div>
                        </td>
                        @if($data->status == 3)
                            <td style="background-color: #dd6255!important;"><div class="show_csv_co">{{$data->standard_value}}</div> 
                        @elseif($data->status == 1)
                            <td style="background-color: #8bd789!important;"><div class="show_csv_co">{{$data->standard_value}}</div> 
                        @else 
                            <td><div class="show_csv_co">{{$data->standard_value}}</div> 
                        @endif
                        
                            @if($data->status == 2 && auth()->user()->hasRole('Admin'))
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="accept" value="accept" data-id="{{$data->id}}">
                                    <label class="form-check-label" for="accept">
                                        Accept
                                    </label>
                                </div>
                                
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="reject" value="reject" data-id="{{$data->id}}">
                                    <label class="form-check-label" for="reject">
                                        Reject
                                    </label>
                                </div>
                            @endif
                        </td>
                        <td>{{ $data->translate_text_score }}</td>
                        <td>{{$data->created_at}}</td>

                        @if($userPermission || auth()->user()->hasRole('Admin'))
                        <td>
                            <button class="insert-code-shortcut" data-target="#edit_model"  data-user ="{{auth()->user()->id }}" data-lang ="{{$data->lang_id}}" data-value="{{$data->standard_value}}" data-id="{{$data->id}}"><i class="fa fa-pencil"></i></button>
                            
                            <button class="view-history" data-id="{{$data->id}}" title="view Test Change History"><i class="fa fa-history"></i></button>

                            <button class="view-status-history" data-id="{{$data->id}}" title="View Status Change History"><i class="fa fa-history"></i></button>

                            <button class="get-translate-score" data-id="{{$data->id}}" title="Get Translared Text Score"><i class="fa fa-dashboard"></i></button>
                        </td>
                        @endif
                    </tr>                        
                @endforeach
            </tbody>
        </thead>
    </table>
    {!! $googleTranslateDatas->appends(Request::except('page'))->links() !!}
</div>

<div class="modal fade" id="edit_model" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="form-update" action="{{ route('googlefiletranslator.update') }}" id="updateForm">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Update Value</h4>
            </div>
            <div class="modal-body edit_model_body">
                    @csrf
                    <input type="text" name="update_record" id="update_record" class="form-control update_record" />
                    <div class="d-none add_hidden_data"></div>
                   
              
            </div>
            <div class="modal-footer">
            <input type="submit" value="update" name="update" class="btn btn-secondary" />
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
50% 50% no-repeat;display:none;">

@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
</script>
<script type="text/javascript">
    
    $(document).on('click', ".btn-permission-submit-form", function(e) {
        e.preventDefault();
        $("#loading-image").show();
        var userId = $("#selectUserId").val();
        var langId = $("#selectLangId").val();
        var actionId = $("#actionId").val();
        var typeId = $("#typeId").val();
        $(".alert-class").text('');
        $.ajax({
            url: "{{route('googlefiletranslator.user-view.permission')}}",
            method:'POST',
            data:{'user_id':userId,'lang_id':langId,'action':actionId,'type':typeId,'_token':"{{csrf_token()}}"},
            success:function(response){
                $("#loading-image").hide();
                if(response.status === 200){       
                    $(".alert-class").text("Successfully added");
                    $(".alert-class").addClass("alert-success");
                    $(".alert-class").removeClass("alert-danger");
                    $(".alert-class").removeClass("d-none");
                    toastr['success'](response.message);
                    window.location.reload();
                }else{
                    $("#selectUserId").val('');
                    $("#selectLangId").val('');
                    $("#actionId").val('');
                    $("#typeId").val('');
                    $(".alert-class").removeClass("alert-success");
                    $(".alert-class").text("Permission already exist");
                    $(".alert-class").removeClass("d-none");
                    $(".alert-class").addClass("alert-danger");
                }
            }
        })
    });

    $(document).on("click", ".btn_download_approved", function (e) {
        e.preventDefault();
        let id = $(this).data("id");
        let type = $(this).data("type");
        $("#loading-image").show();
        $.ajax({
            type: "get",
            url: "{{route('googlefiletranslator.downlaod.permission')}}",
            data: {
                id:id,
                type:type,
            },
            success: function (response) {
                $("#loading-image").hide();
                if(response.status == 200) {
                    toastr['success'](response.message);
                }
            },
            error: function () {
                $("#loading-image").hide();
                toastr['error']("Something went wrong!");
            }
        });
    });
        

    $(document).on("click",".show_csv_co", function(e) {
        e.preventDefault();
        $("#Show_message_display").modal('show');
        var _this = $(this);
        var content = _this.html();
        var aa = $('_this .show_csv_co').html();

        $('.chat_message_history').html('<td>'+content+'</td>')
    });


    $(document).on('click', ".insert-code-shortcut", function() {
        $("#edit_model").modal('show');
        var id = $(this).data('id');
        var formValue = $(this).data('value');
        var userId = $(this).data('user');
        var langId = $(this).data('lang');
        $(".update_record").val(formValue);
        let html = `<input type="hidden" name="update_by_user_id" value='`+userId+`'>
        <input type="hidden" name="lang_id" value='`+langId+`'>
        <input type="hidden" name="record_id" value='`+id+`'>`;
        $(".add_hidden_data").html(html);
        
    });

    $(document).ready(function() {
        $('#updateForm').on('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            var formData = new FormData($(this)[0]); // Create a FormData object with form data

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'), // Get the form's action URL
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function(response) {
                    toastr['success'](response.message);
                    window.location.reload();           
                },
                error: function(xhr, textStatus, errorThrown) {
                    // Handle any errors that occur during the AJAX request
                    console.error(xhr, textStatus, errorThrown);
                }
            });
        });
    });

    $(document).on("click", ".view-history", function (e) {
        e.preventDefault();
        let id = $(this).data("id");
        $.ajax({
            method: "GET",
            url: '{{ route("googlefiletranslator_histories.show", '') }}/' + id,
            dataType: "json",
            data: {
                id:id,
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#magneto-frontend-historylist").modal("show");

                if (response) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${v.old_value} </td>
                                    <td> ${v.new_value} </td>
                                    <td> ${v.user.name} </td>
                                </tr>`;
                    });
                    $("#magneto-frontend-historylist").find(".magneto-historylist-view").html(html);
                    $("#magneto-frontend-historylist").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
                $("#loading-image").hide();
            }
        });
    });

    $('input[type="radio"]').change(function() {
        var selectedValue = $('input[name="status"]:checked').val();
        id = $(this).data('id');
        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('googlefiletranslator_histories.status')}}",
            data: { status: selectedValue , id:id },
            success: function(response) {
                toastr['success']("update successfully");
                window.location.reload();           
            },
            error: function(error) {
                // Handle any errors here
                window.location.reload();          
                console.error('Error storing status value: ' + error);
            }
        });
    });

    $(document).on("click", ".view-status-history", function (e) {
        e.preventDefault();
        let id = $(this).data("id");
        $.ajax({
                method: "GET",
                url: '{{ route("googlefiletranslator_histories_status.show", '') }}/' + id,
                dataType: "json",
                data: {
                    id:id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#magneto-frontend-historyliststatus").modal("show");

                    if (response) {
                        var html = "";
                        $.each(response.data, function(k, v) {

                            if(v.status==1){
                                var status = 'Accept';
                            } else if(v.status==2){
                                var status = 'Unchecked';
                            } else if(v.status==3){
                                var status = 'Reject';
                            }

                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> `+status+` </td>
                                        <td> ${v.user.name} </td>
                                    </tr>`;
                        });
                        $("#magneto-frontend-historyliststatus").find(".magneto-historyliststatus-view").html(html);
                        $("#magneto-frontend-historyliststatus").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
    });

    $(document).on("click", ".get-translate-score", function (e) {
        e.preventDefault();
        let id = $(this).data("id");
        if (id != "") {
            $.ajax({
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route("googlefiletranslator.get-multi-score") }}',
                dataType: "json",
                data: {
                    id:id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        toastr['success'](response.message);
                        window.location.reload(); 
                    } else {
                        toastr["error"](response.message, "Message");
                    }
                }
            });
        }
    });
</script>
@endsection
