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
        <h2 class="page-heading">Csv Translator Languages List</h2>
    </div>
</div>
<div class="row">
@include('partials.flash_messages', ['withWidth' => true])
</div>
<div class="row w-100">
    <div class="col-md-2">
       <label>Language</label> 
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
        </select>
    </div>
    <div class="col-md-2">
    <label>Status</label>
        <select class="form-control" name="status_filter" id="status_filter">
            <option value="">Status</option>
            <option value="checked">checked</option>
            <option value="unchecked">unchecked</option>
            <option value="">others</option>
        </select>
    </div>
    <div class="col-md-2">
    <label>Users</label>
        <select class="form-control" name="users_filter" id="users_filter">
            <option value="">Select</option>
            @foreach ($users as $user)
                <option value="{{$user->id}}">{{$user->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-1 my-5">
    <a href="#" class="filterSearch">
            <i class="fa fa-search"></i>
        </a>
    </div>

</div>

<div class="float-right my-3">
    @if(auth()->user()->hasRole('Lead Translator') || auth()->user()->hasRole('Admin'))
    <button data-toggle="modal" data-target="#csv_import_model" class="btn btn-secondary btn_import">Import CSV</button>
    <a class="btn btn-secondary text-white btn_select_user" data-toggle="modal" data-target="#permissions_model">Permission</a>
    @endif
    <a class="btn btn-secondary btn_export" href="#" target="_self">Export CSV</a>
</div>


<div class="table-responsive mt-3 table-horizontal-scroll" style="margin-top:20px;">
    <table class="table table-bordered text-wrap csvData-table w-100" style="border: 1px solid #ddd;" id="csvData-table">
        <thead>
            <tr>
            @if(auth()->user()->hasRole('Lead Translator') || auth()->user()->hasRole('Admin'))
                <th>Id</th>
                <th>Keyword</th>
                <th>En</th>
                <th>ES</th>
                <th>RU</th>
                <th>KO</th>
                <th>JA</th>
                <th>IT</th>
                <th>DE</th>
                <th>FR</th>
                <th>NL</th>
                <th>ZH</th>
                <th>AR</th>
                <th>UR</th>
                @else
                @php $language = json_decode($lang);@endphp
                @foreach ($language as $columToDraw)
                    <th>{{$columToDraw->data}}</th>
                @endforeach
                @endif
            </tr>
            

            
        </thead>
        <tbody>

        </tbody>
    </table>
    <div class="pagination-custom">

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
                            @foreach ($activeUsers as $activeUser)
                                <option value="{{$activeUser->id}}">{{$activeUser->name}}</option>
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
                <button type="button" class="btn btn-secondary btn-submit-form">Add
                    Permission</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="csv_import_model" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Upload Csv</h4>
            </div>
            <div class="modal-body">
                <form action="#"  class="dropzone" id="my-dropzone">
                    @csrf
                </form>
                <div class="alert alert-success d-none success-alert">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="edit_model" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <form method="post" class="form-update" action="{{route('csvTranslator.update')}}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Update Value</h4>
            </div>
            <div class="modal-body edit_model_body">
                    @csrf
                    <input type="text" name="update_record" class="form-control update_record" />
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

<div class="modal fade" id="history" role="dialog" >
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">History</h4>
            </div>
            <div class="modal-body" id="history-body-content">

            </div>
            <div class="modal-footer">
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

@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
</script>
<script type="text/javascript">
    $(document).on('click', '.btn_import', function() {

        var myDropzone = new Dropzone("form#my-dropzone", {
            url: "{{ route('csvTranslator.uploadFile') }}",
            acceptedFiles: ".xlsx,.csv",
        }).on('complete', function(response) {
            if(response.status === 'error'){
                $(".success-alert").removeClass('d-none');
                $(".success-alert").addClass('alert-danger');
                $(".success-alert").text('oops something went wrong...!!!');
                $(".success-alert").removeClass('alert-success');
            }else{
                $(".success-alert").removeClass('d-none');
                $(".success-alert").addClass('mt-2');
                $(".success-alert").text('Successfully Imported');
                setTimeout(function() {
                    $("#csv_import_model").modal('hide');
                    window.location.reload();
                }, 500);
            }
            
        })
    });
    
    $(document).on('click', ".btn-submit-form", function() {
        var userId = $("#selectUserId").val();
        var langId = $("#selectLangId").val();
        var actionId = $("#actionId").val();
        var typeId = $("#typeId").val();
        $(".alert-class").text('');
        $.ajax({
            url:'/csv-translator/permissions',
            method:'POST',
            data:{'user_id':userId,'lang_id':langId,'action':actionId,'type':typeId,'_token':"{{csrf_token()}}"},
            success:function(response){
                if(response.status === 200){       
                    $(".alert-class").text("Successfully added");
                    $(".alert-class").addClass("alert-success");
                    $(".alert-class").removeClass("alert-danger");
                    $(".alert-class").removeClass("d-none");
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
    var cols;
</script>

@if(auth()->user()->hasRole('Lead Translator') || auth()->user()->hasRole('Admin'))
    <script>   
        cols =  [{ data: 'id' },
            { data: 'key' },
            { data: 'en', render: function(data, type, row, meta) {
                if(row.status_en == 'checked')
                    return '<div class="bg-success text-white show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="en" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
                else
                    return '<div class="show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="en" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
            }},
            { data: 'es',render: function(data, type, row, meta) {
                if(row.status_es == 'checked')
                    return '<div class="bg-success text-white show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="es" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
                else 
                    return '<div class="show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="es" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';

            } },
            { data: 'ru',render: function(data, type, row, meta) {
                if(row.status_ru == 'checked')
                    return '<div class="bg-success text-white show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="ru" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
                else
                    return '<div class="show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="ru" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';

            } },
            { data: 'ko' ,render: function(data, type, row, meta) {
                if(row.status_ko == 'checked')
                    return '<div class="bg-success text-white show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="ko" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
                else
                    return '<div class="show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="ko" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
            }},
            { data: 'ja',render: function(data, type, row, meta) {
                if(row.status_ja == 'checked')
                    return '<div class="bg-success text-white show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="ja" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
                else 
                    return '<div class="show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="ja" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
            } },
            { data: 'it',render: function(data, type, row, meta) {
                if(row.status_it == 'checked')
                    return '<div class="bg-success text-white show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="it" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
                else 
                    return '<div class="show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="it" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
            } },
            { data: 'de',render: function(data, type, row, meta) {
                if(row.status_de == 'checked')
                    return '<div class="bg-success text-white show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="de" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
                else
                    return '<div class="show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="de" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
            } },
            { data: 'fr',render: function(data, type, row, meta) {
                if(row.status_fr == 'checked')
                    return '<div class="bg-success text-white show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="fr" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
                else
                    return '<div class="show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="fr" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
            } },
            { data: 'nl',render: function(data, type, row, meta) {
                if(row.status_nl == 'checked')
                    return '<div class="bg-success text-white show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="nl" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
                else
                    return '<div class="show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="nl" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
            } },
            { data: 'zh',render: function(data, type, row, meta) {
                if(row.status_zh == 'checked')
                    return '<div class="bg-success text-white show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="zh" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
                else
                    return '<div class="show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="zh" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
            } },
            { data: 'ar',render: function(data, type, row, meta) {
                if(row.status_ar == 'checked')
                    return '<div class="bg-success text-white show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="ar" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
                else
                    return '<div class="show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="ar" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
            } },
            { data: 'ur',render: function(data, type, row, meta) {
                if(row.status_ur == 'checked')
                    return '<div class="bg-success text-white show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="ur" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
                else 
                    return '<div class="show_csv_co">'+data+'</div>' +'<a href="#" class="history_model position-absolute float-right text-wrap" data-lang="ur" data-key='+row.key+' data-id=' + row.id +' data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>';
            } }]
    </script>
 @else
    <script>
        cols = <?php echo $lang; ?>;
  </script>
 @endif
    <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>


<script>
    var csvTable;
    $(document).ready(function() {
        csvTable = $('#csvData-table').DataTable({
            ajax: "{{route('csvTranslator.list')}}",
            responsive: true,
            pageLength: 50,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            columns: cols,
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

    $(".show_csv_data").click(function() {
        var res = $('#demo').text();
        alert(res);
    });

    $(".btn_export").on('click',function(){
            $(".csvData-table").table2excel({
            name: "Language File",
            filename: "csv-translator.xls", // do include extension
            preserveColors: false // set to true if you want background colors and font colors preserved
        });
    });

    $(document).on('click', ".editbtn_model", function() {
        setTimeout(function() {
            $("#Show_message_display").modal('hide');
        }, 1);
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

    $(document).on('click','.history_model',function(){
        setTimeout(function() {
            $("#Show_message_display").modal('hide');
        }, 0.1);
        var id = $(this).data('id');
        var key = $(this).data('key');
        var language = $(this).data('lang');

        $('#history-body-content').empty();

        $.ajax({
            url:"{{ route('csvTranslator.history') }}",
            method:'POST',
            data:{'id':id,"key":key,"language":language,'_token':"{{csrf_token()}}"},
            success:function(response){
                $("#Show_message_display").modal('hide');
                $('#history-body-content').empty().html(response.html);
            }
        })
    });

    $(".filterSearch").on('click',function(){
        var langFilter = $("#lang_filter").val();
        var statusFilter =  $("#status_filter").val();
        var usersFilter = $("#users_filter").val();
        csvTable.ajax.url("/csv-filter?"+'user='+usersFilter+'&status='+statusFilter+'&lang='+langFilter).load();
        
    });

    $(document).on("change",'input:radio[name="radio1"]',function(){
            var id,language,status;
            if($(this).val() == 'checked'){
                 id = $(this).data('id');
                 language = $(this).data('lang');
                 status = "checked";
            }else if($(this).val() == 'unchecked'){
                id = $(this).data('id');
                language = $(this).data('lang');
                status = "unchecked";
            }
            $.ajax({
                url:'/csv-translator/approvedByAdmin',
                method:"POST",
                data:{"id":id,"lang":language,"status":status,"_token":"{{csrf_token()}}"},
                success:function(response){
                    csvTable.clear().draw();
                }
            })
        });

</script>
@endsection
