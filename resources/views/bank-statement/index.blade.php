@extends('layouts.app')
@section('favicon' , 'user-management.png')

@section('title', 'Bank statement')

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
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Bank statements >> List (<span id="user_count">{{ $data->total() }}</span>)</h2>
        </div>
        <div class="col-lg-12 margin-tb ml-2 mb-2">
            <a href="{{ route('bank-statement.import') }}" class="btn btn-default">
                {{__('Import Excel File')}}
            </a>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                <th rowspan="2">Sr. No</th>
                <th rowspan="2" class="text-center">Name</th>
                <th rowspan="2" class="text-center">File</th>
                <th rowspan="2" class="text-center">Status</th>
                <th rowspan="2" class="text-center">Created By</th>
                <th rowspan="2" class="text-center">Created At</th>
                <th rowspan="2" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $detail)
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td class="text-center">{{$detail->name}}</td>
                        <td class="text-center">{{$detail->filename}}</td>
                        <td class="text-center">{{$detail->status}}</td>
                        <td class="text-center">{{$detail->user->name}}</td>
                        <td class="text-center">{{$detail->created_at}}</td>
                        <td class="text-center">
                            @if($detail->status != 'mapped')
                            <a href="{{ route('bank-statement.import.map', ['id' => $detail->id]) }}" class="btn btn-default">
                                {{__('Map & Import')}}
                            </a>
                            @else
                            <a href="{{ route('bank-statement.import.mapped.data', ['id' => $detail->id]) }}" class="btn btn-default">
                                {{__('Mapped Data')}}
                            </a>
                            @endif

                            <a href="javascript:void(0)" data-id="{{ $detail->id }}" class="preview_excel_file btn btn-default">
                                {{__('Preview File')}}
                            </a>
                        </td>  
                    </tr>    
                @endforeach
            </tbody>
        </table>
        <div class="text-center">
            <div class="text-center">
                {!! $data->links() !!}
            </div>
        </div>
    </div> 
 <!-- user-search Modal-->
 <div id="bank-statements-model" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl"  role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Preview File</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table table-bordered mt-3" style="overflow: auto; max-height:500px;">
                            <table class="table table-bordered page-notes" id="bank-statements-tbody">
                                
                            </table>
                        </div>
                    </div>
                </div>
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
    
    $(document).ready(function() {
        $(".preview_excel_file").click(function() {
            $("#bank-statements-model").modal("show");
            $.ajax({
                url: 'preview-file/'+$(this).data('id'),
                dataType: "json",
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(function (data) {
                $("#loading-image").hide();
                var dataHtml = "";
                $.each(data, function(index, element) {
                    var dataTds = '';
                    $.each(element, function(index1, element1) {
                        dataTds += '<td>'+element1+'</td>';
                    });
                    dataHtml += '<tr>'+dataTds+'</tr>';
                });
                $("#bank-statements-tbody").html(dataHtml);
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        });
    });
</script>

@endsection
