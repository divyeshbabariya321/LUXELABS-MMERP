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
        <?php $id = request()->route('id'); ?>
        <h2 class="page-heading">GoogleFile Translator Languages List</h2>
        <form action="{{ route('store-website.push.csv', ['id' => $id]) }}" method="get" class="search">

        <div class="col-lg-2">
            <input class="form-control" type="text" id="search_filename" placeholder="Search FileName" name="search_filename" value="{{ (request('search_filename') ?? "" )}}">
        </div>
        <div class="col-lg-2">
            <input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
        </div>
        <div class="col-lg-2">
            <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
               <img src="{{ asset('images/search.png') }}" alt="Search">
           </button>
           <a href="{{ route('store-website.push.csv', ['id' => $id]) }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
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
    <div class="col-md-1 my-5">
    {{-- <a href="#" class="filterSearch">
            <i class="fa fa-search"></i>
        </a> --}}
    </div>

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
                            @foreach (App\User::where('is_active', '1')->get() as $user)
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



<div class="mt-3 col-md-12">
    <table class="table table-bordered table-striped" id="log-table">
        <thead>
            <tr>
                <th width="3%">ID</th>
                <th width="30%">Filepath</th>
                <th width="10%">Date</th>
                <th width="10%">Action</th>
             </tr>
            <tbody>
                <?php $id = request()->route('id'); ?>
                @foreach ($filenames as $key => $file)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td><div class="show_csv_co">{{$file->filename}}</div> 
                        </td>
                        <td><div class="show_csv_co">{{$file->created_at}}</td>  
                        <td>
                            <button type="button" id="ip_log" class="btn btn-image process-magento-push-btn" title="pushCsvDownlaod" data-filename="{{$file->filename}}" data-id="{{$id}}"  data-csvfileId="{{$file->id}}"> 
                            <i class="fa fa-upload  upload_faq" aria-hidden="true"></i></button>
                            <button type="button" class="btn btn-image load-pull-logs" data-id="{{$file->id}}" title="View push Logs" style="cursor: default;"> <i class="fa fa-info-circle"> </i></button>
                            <?php $fileName = basename($file->filename);
                            ?>
                            <a class="btn btn-image" href="{{ route('googlefiletranslator.list-page.view', ['id' => $id, 'type' => 'storewebsite-' . $fileName]) }}" target="_blank" title="Csv data to View"> <img src="/images/view.png" style="cursor: default;"></a>
                            @if($file->download_status== 1)
                            <a class="btn btn-image" href="{{ route('store-website.download.csv', ['id' => $id, 'type' =>  'storewebsite-' . $fileName]) }}" target="_blank"><i class="fa fa-download"></i></a>
                            @else 
                            <button type="button" class="btn btn-image" onclick="showPermissionAlert()"><i class="fa fa-download"></i></button>
                            @endif
                        </td>
                    </tr>                        
                @endforeach
            </tbody>
        </thead>
    </table>
    {{ $filenames->appends(request()->except('page'))->links() }}
</div>


<div id="store-log-push-listing" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Push Logs</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">request</th>
                                <th width="25%">message</th>
                                <th width="25%">Updated by</th>
                                <th width="25%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="store-log-push-listing-view">
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
    
    function showPermissionAlert() {
        alert('Permission required to download this file.');
    }

    $(document).on("click", ".process-magento-push-btn", function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            var filename = $(this).data("filename");
            var csvfileId = $(this).data("csvfileid");

            $.ajax({
                url: '{{route('store-website.single.push.command.run')}}',
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, 
                data: {
                    id: id,
                    filename:filename,
                    csvfileId:csvfileId,
                },
                beforeSend: function() {
                    $('#loading-image').show();
                },
            }).done(function(response) {
                if (response.code == '200') {
                    toastr['success']('Command Run successfully!!!', 'success');
                } else if(response.code == '500') {
                    toastr['error'](response.message, 'error');
                }
                else {
                    toastr['error'](response.message, 'error');
                }
                $('#loading-image').hide();
            }).fail(function(errObj) {
                $('#loading-image').hide();
                    toastr['error']("Invalid JSON response", 'error');

            });
         });

         $(document).on('click', '.load-pull-logs', function() {
            var id = $(this).attr('data-id');
            var action = "push";

            $.ajax({
                url: '{{route('pull-request.log.show')}}',
                dataType: "json",
                data: {
                    id:id,
                    action:action,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.command ? v.command : ''} </td>
                                        <td> ${v.message ? v.message : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                    </tr>`;
                        });
                        $("#store-log-push-listing").find(".store-log-push-listing-view").html(html);
                        $("#store-log-push-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });


</script>
@endsection
