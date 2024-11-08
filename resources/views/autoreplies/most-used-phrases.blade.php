@extends('layouts.app')

@section('title', 'Auto Replies - ERP Sololuxury')

@section('styles')
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/bootstrap-datetimepicker.min.css') }} ">
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/bootstrap-toggle.min.css') }} ">
    <link rel="stylesheet" type="text/css" href="/css/dialog-node-editor.css">
    <style type="text/css">
        .dis-none {
            display: none;
        }
        .fixed_header{
            table-layout: fixed;
            border-collapse: collapse;
        }

        .fixed_header tbody{
          display:block;
          width: 100%;
          overflow: auto;
          height: 250px;
        }

        .fixed_header thead tr {
           display: block;
        }

        .fixed_header thead {
          background: black;
          color:#fff;
        }

        .fixed_header th, .fixed_header td {
          padding: 5px;
          text-align: left;
        }

        .selecte-choice-reply {
            cursor: pointer;
        }
        .select2-container {
            width:100% !important;
        }
    </style>
@endsection

@section('content')
    <div class="row margin-tb">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Most Used Phrases | Auto Replies</h2>
            <div class="pull-left">
                <form>
                    <div class="form-group">
                        <input type="text" class="form-control" value="{{ request('keyword') }}" name="keyword" id="search-by-word" aria-describedby="search-by-word" placeholder="Enter Keyword">
                    </div>
                </form>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary ml-3" onclick="addGroupPhrase()">Phrase Group</a>
            </div>
            <div class="pull-left">
                <a class="btn intent-edit" data-toggle="modal" data-target="#intent-reply-popup">
                    <span>Add Popup</span>
                </a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')
    <div class="col-md-12 margin-tb" style="margin-top:10px;">
        <ul class="pagination" role="navigation">
            @for($i = 1; $i <= $recordsNeedToBeShown; $i++) {
                 <li  class="page-item @if($activeNo == $i) active  @endif" style="display:inline-block;margin-right: 2px;">
                    <a class="page-link" href="{{ route('chatbot.mostUsedPhrases', request()->except('page') + ['page' => ($i * $multiple)]) }}" rel="prev" aria-label="From {{ $i }}">From {{ $i * $multiple }} >></a>
                </li>
             }@endfor
        </ul>
    </div>    
    <div class="col-md-12 margin-tb" style="margin-top:10px;">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th width="25%">Words</th>
                <th width="25%">Total</th>
                <th width="25%">Action</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($mostUsedPhrases as $key => $phrases)
                    <tr>
                        <td><input type="checkbox" data-message-id="{{ $phrases->chat_id }}" name="phrase" data-keyword="{{ $phrases->id }}" value="{{ $phrases->id }}">  {{ $phrases->phrase }}</td>
                        <td>{{ $phrases->total_count }}</td>
                        <td>
                            <button data-id="{{ $phrases->chat_id }}" class="btn btn-image get-chat-details"><img src="/images/chat.png"></button>
                            <button data-id="{{ $phrases->id }}" class="btn btn-image delete-row-btn"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-md-12 margin-tb">
        {{ $mostUsedPhrases->appends(request()->except('page'))->links() }}
    </div>    
    @include('partials.chat-history')
    @include('autoreplies.partials.group')
    <div class="modal fade" id="leaf-editor-model" role="dialog" style="z-index: 3000;">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Editor</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary save-dialog-btn">Save changes</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="phrase-editor-model" role="dialog" style="z-index: 1041;">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="allPhrases">All Phrases</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            
          </div>
        </div>
      </div>
    </div>
    @php include_once(app_path()."/../Modules/ChatBot/Resources/views/dialog/includes/template.php"); @endphp
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-datetimepicker.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-toggle.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/jsrender.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/dialog-build.js') }} "></script>
    <script type="text/javascript">
        window.buildDialog = {};
        window.pageLocation = "autoreply";

        $('.modal').on("hidden.bs.modal", function (e) { 
            if ($('.modal:visible').length) { 
                $('body').addClass('modal-open');
            }
        });

        $(document).on("focusout","#search-by-phrases",function() {
            var dataId = $(this).data("id");
            $.ajax({
                type: 'GET',
                url: "/autoreply/get-phrases",
                data: {
                    id: dataId,
                    keyword : $(this).val()
                }
            }).done(function (response) {
                if(response.code == 200) {
                    $("#phrase-editor-model").find(".modal-body").html(response.html);
                    //$("#phrase-editor-model").modal("show");
                }
            }).fail(function (response) {
            });
        });

        $(document).on("click",".delete-row-btn",function() {
            var $this = $(this);
            var dataId = $(this).data("id");
            if(confirm("Are you sure you want to do this operation ?")) {
                $.ajax({
                    type: 'POST',
                    url: "autoreply/delete-most-used-phrases",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: dataId,
                    }
                }).done(function () {
                    $this.closest("tr").remove();
                }).fail(function (response) {
                });
            }
        });

        var callingMoreChat = function(chatId, pageId) {
            $.ajax({
                type: 'GET',
                url: "autoreply/replied-chat/"+chatId,
                data : {
                    page : pageId
                },
                dataType : "json"
            }).done(function (response) {
                var html = "";
                $.each(response.data,function(k,v) {
                    html += '<div class="bubble">';
                    html += '<div class="txt">';
                    html += '<p class="name"></p>';
                    html += '<p class="message" data-message="'+v.message+'">'+v.message+'</p><br>';
                    html += '<span class="timestamp">'+v.created_at+'</span><span>';
                    html += '<a href="javascript:;" class="btn btn-xs btn-default ml-1 set-autoreply" data-q="'+response.question+'" data-a="'+v.message+'">+ Auto Reply</a></span>';
                    html += '</div>';
                    html += '</div>';
                });

                if(html != "") {
                    html += '<div class="row"><a onclick="callingMoreChat('+chatId+','+response.page+')" href="javascript:;" class="load-more-chat">Load More</a></div>';
                }
                $(".load-more-chat").parent().remove();
                $("#chat-list-history").find(".modal-body").find(".speech-wrapper").append(html);

            }).fail(function (response) {
            });
        }

        $(document).on("click",".get-chat-details",function() {
            var chatId = $(this).data("id");
            $.ajax({
                type: 'GET',
                url: "autoreply/replied-chat/"+chatId,
                dataType : "json"
            }).done(function (response) {
                var html = "";
                $.each(response.data,function(k,v) {
                    html += '<div class="bubble">';
                    html += '<div class="txt">';
                    html += '<p class="name"></p>';
                    html += '<p class="message" data-message="'+v.message+'">'+v.message+'</p><br>';
                    html += '<span class="timestamp">'+v.created_at+'</span><span>';
                    html += '<a href="javascript:;" class="btn btn-xs btn-default ml-1 set-autoreply" data-q="'+response.question+'" data-a="'+v.message+'">+ Auto Reply</a></span>';
                    html += '</div>';
                    html += '</div>';
                });

                if(html != "") {
                    html += '<div class="row"><a href="javascript:;" onclick="callingMoreChat('+chatId+','+response.page+')" class="load-more-chat">Load More</a></div>';
                }

                $("#chat-list-history").find(".modal-body").find(".speech-wrapper").html(html);
                $("#chat-list-history").find(".modal-title").html(response.question);
                $("#chat-list-history").modal("show");
                

            }).fail(function (response) {
            });
        });

        $(document).on("click",".set-autoreply",function() {

            $("#leaf-editor-model").modal("show");
            
            var myTmpl = $.templates("#add-dialog-form");
            var assistantReport = [];
                assistantReport.push({"response" : $(this).data("a") , "condition_sign" : "" , "condition_value" : "" , "condition" : "","id" : 0});
            var json = {
                "create_type": "intents_create",
                "intent"  : {
                    "question" : $(this).data("q"),
                },
                "assistant_report" : assistantReport,
                "response" :  $(this).data("a"),
                "allSuggestedOptions" : JSON.parse('{{ json_encode($allSuggestedOptions) }}')
            };
            var html = myTmpl.render({
                "data": json
            });

            window.buildDialog = json;
            
            $("#leaf-editor-model").find(".modal-body").html(html);
            $("[data-toggle='toggle']").bootstrapToggle('destroy')
            $("[data-toggle='toggle']").bootstrapToggle();
            $(".search-alias").select2({width : "100%"});
            
            var eleLeaf = $("#leaf-editor-model");
            searchForIntent(eleLeaf);
            searchForCategory(eleLeaf);
            searchForDialog(eleLeaf);
            previousDialog(eleLeaf);
            parentDialog(eleLeaf);

            /*$.ajax({
                type: 'POST',
                url: "autoreply/save-by-question",
                data: {
                    _token: "{{ csrf_token() }}",
                    q: $(this).data("q"),
                    a: $(this).data("a")
                }
            }).done(function () {
                toastr['success']('Auto Reply added successfully', 'success');
            }).fail(function (response) {
            });*/
        });

        function addGroupPhrase(){
            var phraseId = [];
            var messageId = [];
            $.each($("input[name='phrase']:checked"), function(){
                phraseId.push($(this).val());
                messageId.push($(this).data("message-id"));
            });

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('autoreply.group.phrases.reply') }}',
                data: {
                    message_ids: messageId
                },
                dataType : "json"
            }).done(response => {
                if(response.code == 200) {
                    var html = "<ul class='list-group'>";
                    $.each(response.data,function(e,r){
                        html += "<li class='selecte-choice-reply list-group-item'>"+r+"</li>";
                    });
                    html += "</ul>";
                    $("#groupPhraseCreateModal").find(".list-of-reply").html(html);
                }
            }).fail(function (response) {
                alert('Not found any response');
            });


            if(phraseId.length == 0){
                alert('Please Select Phrase From Keywords');
            }else{
                $('#groupPhraseCreateModal').modal('show');
            }
        }

        $(document).on("click",".selecte-choice-reply",function() {
           $("#autochat-reply").val($(this).html());  
        });

        $(document).on("change","#phraseGroup",function(){
           $("#autochat-reply").val($(this).find(':selected').data("suggested-reply"));
        });
        $(document).ready(function () {  
            $('.existing-intent.select2').select2({tags: true});
        });

        function createGroupPhrase() {
            var phraseId = [];
            phrase_group = $('#phraseGroup').val();
            reply = $('#autochat-reply').val();
            erp_or_watson = $('#erp_or_watson').val();
            auto_approve = $('#auto_approve').val();
            category_id = $('#category_id').val();
            
            $.each($("input[name='phrase']:checked"), function(){
                phraseId.push($(this).val());
                keyword = $(this).attr("data-keyword")
            });
            
            if(phraseId.length == 0){
                alert('Please Select Phrase From Keywords');
            }else{
                $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('autoreply.save.group.phrases') }}',
                data: {
                    phraseId: phraseId,
                    keyword : keyword,
                    phrase_group : phrase_group,
                    reply : reply,
                    erp_or_watson : erp_or_watson,
                    auto_approve : auto_approve,
                    category_id : category_id
                },
                }).done(response => {
                    if(response.code == 200) {
                        toastr['success']('Intent created successfully successfully', 'success');
                        location.reload();
                    }
                    else {
                        toastr['error'](response.message, 'error'); 
                    }
                    
                }).fail(function (response) {
                    alert('Could not add Phrase group!');
                });
            }
        }
    </script>
@endsection
