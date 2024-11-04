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
            <h2 class="page-heading">Bank statements >> Import Mapping</h2>
        </div>
        <div class="col-lg-12 margin-tb ml-2 mb-2">
            <a href="{{ route('bank-statement.index') }}" class="btn btn-default">
                {{__('Imported file listing')}}
            </a>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="row">
        
        <div class="col-md-12 p-4">    
        <div class="card ml-3">
            <div class="card-body">
                <h2>{{$bankStatement->filename}} (Status: {{$bankStatement->status}})</h2> <br/>   
                
                <a href="javascript:void(0);" id="select-header">
                    Please choose the row you want to select as a header.
                </a>

                <form action="{{ route('bank-statement.import.map.number.check', ['id'=> $id, 'heading_row_number' => $heading_row_number]) }}" method="post" id="map-number-check">
                    @csrf
                    <div class="row">
                        <div class="col-md-2 p-4 d-none">
                            <label>Please enter the row you want to choose as a header.</label>
                        </div>
                        <div class="col-md-2 p-4">
                            <input type="hidden" name="id" value="{{ $id }}" />
                            <input type="hidden" id="heading_row_number" name="heading_row_number" value="{{ $heading_row_number }}" class="form-control" />
                        </div>
                        <div class="col-md-8 p-4 d-none">
                            <input type="submit" name="heading_row_number_submit" id="heading_row_number_submit" />
                        </div>
                    </div>
                </form>

                <form action="{{ route('bank-statement.import.map.submit', ['id'=>$id]) }}" method="post">
                    @csrf
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" width="30%">{{__('Database Field')}}</th>
                                <th scope="col" width="70%">{{__('Excel Field')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dbFields as $key=>$field)
                            <tr>
                                <td><label for="{{ $key }}" class="form-label">{{ $field }}:</label></td>
                                <td>
                                    <select name="{{ trim($key) }}" class="form-control">
                                        <option value="">-- {{__('Select Excel Field')}} --</option>
                                        @foreach($excelHeaders as $header)
                                            <option value="{{ $header }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                   
                    <button type="submit" class="btn btn-primary mt-2">Import</button>
                </form>
                
            </div>
        </div>        
        </div>    
    </div>

<!-- select header Modal-->
 <div id="select-header-model" class="modal fade in" role="dialog">
    <div class="modal-dialog modal-xl"  role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Select Header</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table table-bordered mt-3" style="overflow: auto; max-height:700px;">
                            <table class="table table-bordered page-notes" id="bank-statements-tbody">
                                @foreach($first10Rows as $k=>$v)
                                @if(($heading_row_number-1) == $k)
                                    <tr style="background-color: green;">
                                @else
                                    <tr>   
                                @endif
                                    <td>
                                        <button type="button" class="btn btn-default choose-header" data-id="{{$k+1}}">
                                            Choose
                                        </button>
                                    </td>
                                    @foreach($v as $kk=>$vv)
                                    <td>{{ $vv }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
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
        $("#select-header").click(function() {
            $("#select-header-model").modal("show");
        });

        $(".choose-header").click(function() {
            // alert($(this).data('id'));
            $("#heading_row_number").val($(this).data('id'));
            $("#map-number-check").submit();
        });
    });
</script>

@endsection
