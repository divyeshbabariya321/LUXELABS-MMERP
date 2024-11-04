@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', "Api Response Messages Translation List")

@section('content')
    <style type="text/css">
        .preview-category input.form-control {
            width: auto;
        }

        .push-brand {
            height: 14px;
        }

        .icon-log-history {
            margin-top: -7px !important;
            display: flex;
            /*display: table-caption;*/
        }

        #page-view-result table tr th:last-child,
        #page-view-result table tr th:nth-last-child(2) {
            width: 50px !important;
            min-width: 50px !important;
            max-width: 50px !important;
        }

    </style>
    <style>
        .loader-small {
            border: 2px solid #b9b7b7;
            border-radius: 50%;
            border-top: 4px dotted #4e4949;
            width: 21px;
            height: 21px;
            -webkit-animation: spin 2s linear infinite; /* Safari */
            animation: spin 2s linear infinite;
            float: left;
            margin: 8px;
            display: none;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Api Response Messages Translation List</h2>
        </div>
        <br>
        @include('partials.flash_messages', ['extraDiv' => true])
        <div class="col-lg-12 margin-tb">
            <div class="col-md-12 margin-tb" id="page-view-result">
                <div class="row table-horizontal-scroll">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th width="10%">Store Website</th>
                            <th width="10%">Key</th>
                            @foreach($languages as $language)
                                {
                                <th data-language-locale="{{$language->name}}" width="10%">
                                    {{ $language->name }}
                                </th>
                                }
                            @endforeach
                        </tr>
                        </thead>
                        <tbody id="translation_data">
                        @if ($apiResponseMessagesTranslationsRows)
                            {
                            @foreach ($apiResponseMessagesTranslationsRows as $apiResponseMessagesTranslationsRow)
                                {
                                <tr>
                                    <td width="10%" class="expand-row">
            <span class="td-mini-container">
                {{ strlen($apiResponseMessagesTranslationsRow->storeWebsite->website) > 15 ? substr($apiResponseMessagesTranslationsRow->storeWebsite->website, 0, 15).'...' :  $apiResponseMessagesTranslationsRow->storeWebsite->website }}
            </span>
                                        <span class="td-full-container hidden">
                {{$apiResponseMessagesTranslationsRow->storeWebsite->website}}
            </span>
                                    </td>
                                    <td width="10%" class="expand-row">
            <span class="td-mini-container">
                {{ strlen($apiResponseMessagesTranslationsRow->key) > 15 ? substr($apiResponseMessagesTranslationsRow->key, 0, 15).'...' :  $apiResponseMessagesTranslationsRow->key }}
            </span>
                                        <span class="td-full-container hidden">
                {{$apiResponseMessagesTranslationsRow->key}}
            </span>
                                    </td>
                                    @foreach ($languages as $language)
                                        {
                                        @php
                                            $rowValue = $rowValues[$apiResponseMessagesTranslationsRow->store_website_id][$apiResponseMessagesTranslationsRow->key][$language->name]['value'] ?? '';
                                            $rowValueApproved = $rowValues[$apiResponseMessagesTranslationsRow->store_website_id][$apiResponseMessagesTranslationsRow->key][$language->name]['approved_by_user_id'] ?? '';
                                        @endphp
                                        <td width="10%" class="expand-row">
                <span class="td-mini-container">
                    {{ strlen($rowValue) > 12 ? substr($rowValue, 0, 12).'...' :  $rowValue }}
                </span>
                                            <span class="td-full-container hidden">
                    {{$rowValue}}
                </span>
                                            @if ($rowValueApproved)
                                                <a href="javascript:;" class="btn p-0 pull-right">
                                                    <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <a href="javascript:;"
                                                   class="update-translation-icon btn p-0 pull-right"
                                                   data-store-website-id="{{$apiResponseMessagesTranslationsRow->store_website_id}}"
                                                   data-store-website-title="{{$apiResponseMessagesTranslationsRow->storeWebsite->title}}"
                                                   data-key="{{$apiResponseMessagesTranslationsRow->key}}"
                                                   data-language-name="{{$language->name}}" data-value="{{$rowValue}}">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        </td>
                                        }
                                    @endforeach
                                </tr>
                                }
                            @endforeach
                            }
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
50% 50% no-repeat;display:none;">
    </div>
    <div class="common-modal modal" role="dialog">
        <div class="modal-dialog" role="document">
        </div>
    </div>

    <!-- Modal -->
    <div id="update-translation-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Translation</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="update-translation-submit-form"
                          action="{!! route('api-response-message.messageTranslateApprove') !!}"
                          method="post">
                        @csrf
                        <div class="form-group">
                            <label for="store-website-title">Website Title:</label>
                            <span id="store-website-title"></span>
                            <input id="store-website-id" name="store_website_id" class="form-control" type="hidden">
                        </div>
                        <div class="form-group">
                            <label for="key">Key:</label>
                            <span id="key-label"></span>
                            <input id="key" name="key" class="form-control" type="hidden">
                        </div>
                        <div class="form-group">
                            <label for="language-name">Language Name:</label>
                            <span id="language-name-label"></span>
                            <input id="language-name" name="lang_name" class="form-control" type="hidden">
                        </div>
                        <div class="form-group">
                            <label for="value">Value</label>
                            <input id="value" name="value" class="form-control" type="text">
                        </div>
                        <div class="form-group pull-right">
                            <input id="translation-update-form-submit" class="btn btn-secondary" type="submit"
                                   value="Approve">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('/js/jquery-ui.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/store-website-brand.js') }} "></script>

    <script type="text/javascript">
      $(document).on("click", ".expand-row", function() {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
          $(this).find(".td-mini-container").toggleClass("hidden");
          $(this).find(".td-full-container").toggleClass("hidden");
        }
      });

      $(document).on("click", ".update-translation-icon", function() {
        var storeWebsiteId = $(this).data("store-website-id");
        var storeWebsiteTitle = $(this).data("store-website-title");
        var key = $(this).data("key");
        var languageName = $(this).data("language-name");
        var value = $(this).attr("data-value");

        $("#store-website-id").val(storeWebsiteId);
        $("#store-website-title").text(storeWebsiteTitle);
        $("#key").val(key);
        $("#key-label").text(key);
        $("#language-name").val(languageName);
        $("#language-name-label").text(languageName);
        $("#value").val(value);

        $("#update-translation-modal").modal("show");
      });

      $(document).on("submit", "#update-translation-submit-form", function(e) {
        e.preventDefault();
        var $form = $(this).closest("form");
        $.ajax({
          type: "POST",
          url: $form.attr("action"),
          data: $form.serialize(),
          dataType: "json",
          success: function(data) {
            if (data.code == 200) {
              $form[0].reset();
              $("#update-translation-modal").modal("hide");
              toastr["success"](data.message, "Message");
              $("a[data-store-website-id='" + data.store_website_id + "'][data-key='" + data.key + "'][data-language-name='" + data.lang_name + "']").find("i").removeClass("fa-pencil").addClass("fa-check-circle-o text-success");
            } else {
              toastr["error"](data.message, "Message");
            }
          },
          error: function(xhr, status, error) {
            var errors = xhr.responseJSON;
          }
        });
      });
    </script>

@endsection

