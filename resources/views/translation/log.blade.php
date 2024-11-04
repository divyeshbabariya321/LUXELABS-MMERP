@extends('layouts.app')
@section('favicon' , 'translation.png')

@section('title', 'Translation Listing')

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
            <h2 class="page-heading">Translation Error Logs (<span id="translation_count">{{  $data->total() }}</span>)</h2>
            <div class="pull-left">
                <div class="form-group">
                        <div class="row">
                            <div class="col-md-8">
                                <input name="search" type="text" class="form-control"
                                       value="{{ isset($search) ? $search : '' }}"
                                       placeholder="Search Translation Error Logs" id="searchTranslationLog">
                            </div>
                            <div class="col-md-2">
                               <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png"/></button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png"/></button>    
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    
    @include('partials.flash_messages')
     <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered" id="translation-table">
                  <thead>
                <tr>
                    <th>ID</th>
                    <th>Google Traslation Settings Id</th>
                    <th>Messages</th>
                    <th>Error Code</th>
                    <th>Domain</th>
                    <th>Reason</th>
                    <th>Updated At</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    @include('translation.partials.list-translation-logs')
                </tbody>
            </table>
        </div>
   </div>

    {!! $data->render() !!}


@endsection


@section('scripts')
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">
    $('.select-multiple').select2({width: '100%'});
    
    $(document).on("click",".btn-delete-TranslationLog",function(event){
        
        if(!confirm("Are you sure you want to delete record?")) {
            event.preventDefault();
        }
    });
    $(document).on("click",".page-link",function(event){
        event.preventDefault();
        src = $(this).attr("href")
        search = $('#searchTranslationLog').val()
        console.log(search);
        $.ajax({
            url: src,
            dataType: "json",
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#translation-table tbody").empty().html(data.tbody);
            $("#translation_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    });
    function submitSearch(){
        src = "{{route('translation.log')}}"
        search = $('#searchTranslationLog').val()
        console.log(search);
        $.ajax({
            url: src,
            dataType: "json",
            data: {
                search : search,
            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#translation-table tbody").empty().html(data.tbody);
            $("#translation_count").text(data.count);
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
        src = "{{route('translation.log')}}"
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
            $('#searchTranslationLog').val('')
            $('#translation-select').val('')
            $("#translation-table tbody").empty().html(data.tbody);
            $("#translation_count").text(data.count);
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

