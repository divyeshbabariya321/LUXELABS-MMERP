@extends('layouts.app')
@section('favicon' , 'referral-management.png')

@section('title', 'Referral Info')

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
            <h2 class="page-heading">Referral Programs Listing (<span id="Referral_count">{{ $data->total() }}</span>)</h2>
            <div class="pull-left">
                <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <input name="term" type="text" class="form-control filter-apply"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="Search Program Name" id="term_1" data-id="1">
                            </div>
                            <div class="col-md-4">
                                <select data-id="2" name="term" id="term_2" class="form-control select2" data-placeholder="Select Website">
                                    <option value="">Select Website</option>
                                    @if(count($storeWebsite) != 0)
                                        @foreach($storeWebsite as $website)
                                            <option value="{{$website->website}}">{{$website->website}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                
                                
                            </div>
                            <div class="col-md-4">
                                <input name="term" type="text" class="form-control filter-apply"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="Search Program Credit" id="term_3" data-id="3">
                            </div>
                            <div class="col-md-4">
                                <input name="term" type="text" class="form-control filter-apply"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="Search Program Currency" id="term_4" data-id="4">
                            </div>
                            <div class="col-md-4">
                                <input name="term" type="text" class="form-control filter-apply"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="Search Lifetime Minutes" id="term_5" data-id="5">
                            </div>
                            <!-- <div class="col-md-2">
                               <button type="button" class="btn btn-image " onclick="submitSearch()"><img src="/images/filter.png"/></button>
                            </div> -->
                            <div class="col-md-2">
                                <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png"/></button>    
                            </div>
                            <!-- <div class="col-md-2">
                                <button id="filter" class="btn mt-0 btn-image">Filter
                                </button>
                            </div> -->
                        </div>
                    </div>
            </div>
            <div class="pull-right pr-4">
                <a class="btn btn-secondary" href="{{ route('referralprograms.add') }}">+</a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')
<div class="p-4">
    <div class="table-responsive">
        <table class="table table-bordered" id="Referrals-table">
              <thead>
            <tr>
                <th>No</th>
                <th>Program Name</th>
                <th>program Uri</th>
                <th>program Credit</th>
                <th>program Currency</th>
                <th>program Lifetime Minutes</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
                @include('referralprogram.partials.list-programs')
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
    $('#term_2').select2({placeholder: "Select Website",allowClear: true});

    
    function submitSearch(){
        src = "{{route('referralprograms.list')}}"
        term = $('#term_1').val();
        $.ajax({
            url: src,
            dataType: "json",
            data: {
                term : term,
            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#Referrals-table tbody").empty().html(data.tbody);
            $("#Referral_count").text(data.count);
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
        src = "{{route('referralprograms.list')}}"
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
            $('#term_1').val('');
            $('#term_2').select2("val", "");
            $('#term_3').val('');
            $('#term_4').val('');
            $('#term_5').val('');
            $('#Referral-select').val('')
            $("#Referrals-table tbody").empty().html(data.tbody);
            $("#Referral_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }

    // pawan added for calling the function on change for filter & ajax call
    function onInput(value,applyId){
        url = "{{route('referralprograms.ajax')}}"
        $.ajax({
            url: url,
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            data: {
                term : value,
                apply_id: applyId
            },
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done(function (response) {
            $("#loading-image").hide();
            if(applyId == 1){
                // $('#term_1').val('');
                $('#term_2').val('');
                $('#term_3').val('');
                $('#term_4').val('');
                $('#term_5').val('');
            } else if(applyId == 2){
                $('#term_1').val('');
                // $('#term_2').val('');
                $('#term_3').val('');
                $('#term_4').val('');
                $('#term_5').val('');
            } else if(applyId == 3){
                $('#term_1').val('');
                $('#term_2').val('');
                // $('#term_3').val('');
                $('#term_4').val('');
                $('#term_5').val('');
            } else if(applyId == 4){
                $('#term_1').val('');
                $('#term_2').val('');
                $('#term_3').val('');
                // $('#term_4').val('');
                $('#term_5').val('');
            } else if(applyId == 5){
                $('#term_1').val('');
                $('#term_2').val('');
                $('#term_3').val('');
                $('#term_4').val('');
                // $('#term_5').val('');
            } else{
                $('#term_1').val('');
                $('#term_2').val('');
                $('#term_3').val('');
                $('#term_4').val('');
                $('#term_5').val('');
            }
            
            $('#Referral-select').val('');
            $('tbody').html('');
            $('tbody').html(response.referralprogram);
            $("#Referral_count").text(response.count);
        
            if (response.links.length > 10) {
                $('ul.pagination').replaceWith(response.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }
        }).fail(function (errObj) {
            alert('No response from server');
            console.log(errObj);
        });
    }
    $('#term_2').on('change', function(e){
        e.preventDefault();
        id = $(this).data('id');
        value = $('#term_'+id).val();
        onInput(value,id);
    });
    $(document).on("input", ".filter-apply", function (e) {
        e.preventDefault();
        id = $(this).data('id');
        value = $('#term_'+id).val();
        // alert(value); //filter-apply
        onInput(value,id);
        // url = "{{route('referralprograms.ajax')}}"
        // ProgramCurrency = 1;
        // $.ajax({
        //     url: url,
        //     headers: {
        //         'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        //     },
        //     type: 'GET',
        //     data: {
        //         term : term,
        //     }
        // }).done(function (response) {
        //     $('tbody').html('');
        //     $('tbody').html(response.referralprogram);
        // }).fail(function (errObj) {
        //     console.log(errObj);
        // });
    });
    
</script>

@endsection
