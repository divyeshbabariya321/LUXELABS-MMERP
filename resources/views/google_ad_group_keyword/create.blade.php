@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <h2>Create Keyword for {{$ad_group_name}} ad group</h2>
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
                <input type="text" class="form-control google_ads_keywords" id="scan_keywords" name="scan_keywords" placeholder="Enter products or services to advertise">
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

        <button type="submit" class="mb-2 float-right">Create</button>
    </form>

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
                if(attrId == 'scanurl')
                {
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
@endsection