@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="col-md-12">
    <h4 class="page-heading">Google Keywords (<span id="adsgroup_count">{{$totalNumEntries}}</span>) for {{@$ad_group_name}} ad group<button class="btn-image" onclick="window.location.href='/google-campaigns/{{$campaignId}}/adgroups'">Back to ad groups</button></h4>
    <div class="pull-left">
        <div class="form-group">
            <div class="row">
                
                <div class="col-md-4">
                    <input name="keyword" type="text" class="form-control" value="{{ isset($keyword) ? $keyword : '' }}" placeholder="Keyword" id="keyword">
                </div>


                <div class="col-md-1">
                    <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png" /></button>
                </div>

                <div class="col-md-1">
                    <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png" /></button>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="float-right custom-button btn mb-3 mr-3" data-toggle="modal" data-target="#new_keyword">New Keyword</button>

    <table class="table table-bordered" id="adsgroup-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Keyword</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($keywords as $keyword)
                <tr>
                    <td>{{$keyword->id}}</td>
                    <td>{{$keyword->keyword}}</td>
                    <td>{{$keyword->created_at}}</td>
                    <td>
                    <div class="d-flex justify-content-between">
                        {{ html()->form('DELETE', route('ad-group-keyword.deleteKeyword', [$campaignId, $keyword['google_adgroup_id'], $keyword['google_keyword_id']]))->style('display:inline')->open() }}
                        <button type="submit" class="btn-image"><img src="/images/delete.png"></button>
                        {{ html()->form()->close() }}
                    </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    {{ $keywords->links() }}
    </div>

    <div class="modal fade" id="new_keyword" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="container">
                    <div class="page-header">
                        <h2>Create Keyword Assign</h2>
                    </div>
                    <form method="POST" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/ad-group-keyword/create" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="campaignId" id="campaignId" value="{{$campaignId}}">
                        <div class="form-group row">
                            <label for="scanurl" class="col-sm-2 col-form-label">Url</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control google_ads_keywords" id="scanurl" name="scanurl" placeholder="Enter a URL to scan for keywords">
                                <span id="scanurl-error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="scan_keywords" class="col-sm-2 col-form-label">Keyword</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control google_ads_keywords" id="scan_keywords" name="scan_keywords" placeholder="Enter products or services to advertise Ex : test,test2">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">&nbsp;</label>
                            <div class="col-sm-10">
                                <button type="button" class="btn btn-default" id="btnGetKeywords">Get keyword suggestions</button>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="suggested_keywords" class="col-sm-2 col-form-label">Suggested Keywords</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="suggested_keywords" name="suggested_keywords" rows="10" placeholder="Enter or paste keywords. You can separate each keyword by commas."></textarea>

                                <span class="text-muted">Note: You can add up to 80 keyword and each keyword character must be less than 80 character.</span><br>

                                @if ($errors->has('suggested_keywords'))
                                    <span class="text-danger">{{$errors->first('suggested_keywords')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <button type="button" class="float-right ml-2" data-dismiss="modal" aria-label="Close">Close</button>
                                <button type="submit" class="mb-2 float-right">Create</button>
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

    function submitSearch(){
        src = '/google-campaigns/{{ $campaignId }}/adgroups/{{$adGroupId}}/ad-group-keyword';
        keyword = $('#keyword').val();

        $.ajax({
            url: src,
            dataType: "json",
            data: {
                keyword : keyword,
            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#adsgroup-table tbody").empty().html(data.tbody);
            $("#adsgroup_count").text(data.count);
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
        src = '/google-campaigns/{{ $campaignId }}/adgroups/{{$adGroupId}}/ad-group-keyword';
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
            $('#keyword').val('');

            $("#adsgroup-table tbody").empty().html(data.tbody);
            $("#adsgroup_count").text(data.count);
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


{{-- Get Keyword Suggestions --}}
@push('scripts')
    <link rel="stylesheet" type="text/css" href="{{ url('tagify/tagify.css') }}">
    {{-- <script src="https://code.jquery.com/jquery.min.js"></script> --}}
    <script type="text/javascript" src="{{ mix('webpack-dist/tagify/jQuery.tagify.min.js') }} "></script>

    <script>
        $(document).ready(function() {
            $('[name=scan_keywords]').tagify();

            $("#btnGetKeywords").prop('disabled',true);
            $('.google_ads_keywords').on('input', function(e) {
                attrId = e.delegateTarget.id;
                $("#"+attrId+"-error").html("");
                $("#btnGetKeywords").prop('disabled',true);
                
                if(attrId == 'scanurl') {
                    attrVal = $(this).val();
                    var res = attrVal.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
                    if(res == null && attrVal != "") {
                        $("#"+attrId+"-error").html("Enter valid URL");
                    } else {
                      $("#btnGetKeywords").prop('disabled',false);
                    }
                } else {
                    newVal = $.trim($(this)[0].innerText);
                    // console.log(newVal)
                    if(newVal!='') {
                        $("#btnGetKeywords").prop('disabled',false);
                    }
                }
            });

            $('#btnGetKeywords').on('click', function(e) {
            kw = $("#scan_keywords")[0].value;
            // console.log({"scanurl":$("#scanurl").val(),"scan_keywords":kw,"campaignId":$("#campaignId").val()});
            key_words='';
            $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "/google-campaigns/"+$("#campaignId").val()+"/adgroups/generate-keywords",
            data: {"scanurl":$("#scanurl").val(),"scan_keywords":kw,"campaignId":$("#campaignId").val()},
            dataType : "json",
            // beforeSend: function () {
            //   $(this).attr('disabled', true);
            //   $(this).text('Adding...');
            // }
            }).done(function(res) {
              // console.log(res);
              if(res['count'] > 0) {
                key_words = res['result'].join(",");
                // console.log(key_words);
                  // $.each(res['result'], function(k,v) {
                  //     key_words += v + ",";
                  // });
              }
              $("#suggested_keywords").html(key_words);
            }).fail(function(response) {
              // console.log(response);
            });
            });
        }); 
    </script>
@endpush 
