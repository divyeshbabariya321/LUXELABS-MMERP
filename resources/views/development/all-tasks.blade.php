@extends('layouts.app')

@section('title', 'View All Tasks')

@section('styles')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style type="text/css">

    </style>
@endsection

@section('content')
  <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">All Tasks</h2>
        </div>
    </div>

    <div class="row">
        <div class="col d-flex justify-content-end">
            <a class="btn btn-success mr-3 excel-export-a-tag" target="_blank" href="/development/task/export-excel">Export Excel</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div type="text/x-jsrender" class="p-5 all-tasks-table-container">
                @include('development.ajax.all-tasks-ajax')
            </div>
        </div>
    </div>
    @include('partials.loader')
@endsection

@section('scripts')
<script>
$('#loading-image').show();

$(document).ready(function(){
    $('#loading-image').hide();

    $(document).on('click', '.pagination a', function(event){
        event.preventDefault(); 
        var page = $(this).attr('href').split('page=')[1];
        fetchData(page);
    });

    function fetchData(page)
    {
        let url = new URL(window.location.href);
        let params = new URLSearchParams(window.location.search);
        params.append('page', page);
        url.search = params.toString();
        
        $.ajax({
            url: url,
            success:function(data)
            {
                $('.all-tasks-table-container').html(data);
            }
        });
    }
    
    });

    let url = new URL("{{ route('development.task.exportExcel') }}");
    let params = new URLSearchParams(window.location.search);
    url.search = params.toString();

    $('.excel-export-a-tag').attr('href', url);
</script>
@endsection
