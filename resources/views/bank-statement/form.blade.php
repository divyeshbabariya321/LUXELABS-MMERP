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
            <h2 class="page-heading">Bank statements >> Import</h2>
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
                <form action="{{ route('bank-statement.import.submit') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter file name" style="width: 400px;" required />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">File</label>
                            <input type="file" name="excel_file" accept=".xls, .xlsx" required />
                        </div>
                        <div class="col-md-12 mb-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
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
</script>
@endsection