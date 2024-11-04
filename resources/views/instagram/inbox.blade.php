@extends('layouts.app')
@section('favicon', 'task.png')

@section('title', 'Message List | Inbox')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/dialog-node-editor.css">
    <link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style>
        .panel-img-shorts .remove-img {
            display: block;
            float: right;
            width: 15px;
            height: 15px;
        }

        form.chatbot .col {
            flex-grow: unset !important;
        }

        .cls_remove_rightpadding {
            padding-right: 0px !important;
        }

        .cls_remove_allpadding {
            padding-left: 0px !important;
            padding-right: 0px !important;
        }

        #chat-list-history tr {
            word-break: break-word;
        }

        .reviewed_msg {
            word-break: break-word;
        }

        .chatbot .communication {
        }

        .background-grey {
            color: grey;
        }

        @media (max-width: 1400px) {
            .btns {
                padding: 3px 2px;
            }
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ddd !important;
        }

        .d-inline.form-inline .select2-container {
            max-width: 100% !important;
            /*width: unset !important;*/
        }

        .actions {
            display: flex !important;
            align-items: center;
        }

        .actions a {
            padding: 0 3px !important;
            display: flex !important;
            align-items: center;
        }

        .actions .btn-image img {
            width: 13px !important;
        }

        .read-message {
            float: right;
        }

    </style>
@endsection

@section('content')
    <div class="row m-0">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">Message List | Inbox</h2>
        </div>
    </div>
    <div class="row m-4">
        <div class="col-lg-12 margin-tb p-0">
            <form action="" method="GET" class="form-inline align-items-start">
                <div class="row mr-3 mb-3">
                    <div class="form-group ml-4">
                        <label>Select Platform</label>  
                        <select id="social_config" class="form-control social_config" name="social_config[]" multiple>
                            @foreach ($socialconfigs->unique('platform') as $socialconfig)
                                <option value="{{ $socialconfig->platform }}" {{ in_array($socialconfig->platform,$_GET['social_config']?? []) ? 'selected' : '' }}>{{ $socialconfig->platform }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group ml-4">
                    <label>Select Website</label>
                    <select id="store_website_id" class="form-control store_website_id" name="store_website_id[]" multiple>
                        @foreach ($websites as $id => $website)
                            <option value="{{ $website->id }}" {{ in_array($website->id,$_GET['store_website_id']?? []) ? 'selected' : '' }}>{{ $website->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group ml-4">
                    <label>Select language</label>
                    <select id="page_language" class="form-control page_language" name="page_language[]" multiple>
                        @foreach ($socialconfigs->unique('page_language') as $socialconfig)
                            <option value="{{ $socialconfig->page_language }}" {{ in_array($socialconfig->page_language,$_GET['page_language']?? []) ? 'selected' : '' }}>{{ $socialconfig->page_language }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group ml-4">
                    <label>Select Name</label>
                    <select id="name" class="form-control name" name="name[]" multiple>
                        @foreach ($socialconfigs->unique('name') as $id => $website)
                            <option value="{{ $website->name }}" {{ in_array($website->name,$_GET['name']?? []) ? 'selected' : '' }}>{{ $website->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group ml-4">
                    <label>From Date</label>
                    <input type="date" value="{{ isset($_GET['from_date']) ? $_GET['from_date'] : '' }}" name="from_date">
                </div>
                <div class="form-group ml-4">
                    <label>To Date</label>
                    <input type="date" value="{{ isset($_GET['to_date']) ? $_GET['to_date'] : '' }}" name="to_date">
                </div>
                <button type="submit" class="btn btn-image"><img src="{{asset('images/filter.png')}}"/></button>
    
            </form>
        </div>
    </div>
    <div class="row m-0">
        <div class="col-md-12 pl-3 pr-3">
            <div class="table-responsive-lg" id="page-view-result">
                @include("instagram.partials.message",[
                    'socialContact' => $socialContact
                ])
            </div>
        </div>
    </div>

    <div id="contact-chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" />
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loading-image" style="position: fixed;left: 0;top: 0;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
      50% 50% no-repeat;display:none;">
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-toggle.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/jsrender.min.js') }} "></script>
    <script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>


    <script>
        $('#social_config').select2({
            placeholder: 'Select Platform',
        });
        $('#store_website_id').select2({
            placeholder: 'Select Website',
        });
        $('#page_language').select2({
            placeholder: 'Select Language',
        });
        $('#name').select2({
            placeholder: 'Select Name',
        });
      $(document).on("click", ".load-contact-communication-modal", function() {
        $("#loading-image").show();
        const data = $(this).data("id");
        $.ajax({
          url: "{{ route('social.message.list') }}",
          method: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            id: data
          },
          success: function(response) {
            const res = response.messages;
            $("#loading-image").hide();
            $("#contact-chat-list-history .modal-body").empty();
            if (res.length > 0) {
              $(res).each(function(key, value) {
                let from = value.sender_id == value.social_contact.account_id ? value.social_contact.name : value.social_contact.social_config.name;
                let to = value.recipient_id == value.social_contact.account_id ? value.social_contact.name : value.social_contact.social_config.name;
                const sentBy =
                  `From ${from} To ${to} On ${new Date(value.created_at)}`;

                $("#contact-chat-list-history .modal-body").append(`
                            <table class="table table-bordered">
                                <tr>
                                    <td style="width:50%">${value.text}</td>
                                    <td style="width:50%">${sentBy}</td>
                                </tr>
                            </table>
                            `);
              });
            } else {
              $("#contact-chat-list-history .modal-body").append(`
                            <table class="table table-bordered">
                                <tr>
                                    <td colspan="2">No conversations found</td>
                                </tr>
                            </table>
                            `);
            }
            $("#contact-chat-list-history").modal("show");
          },
          error: function(error) {
            alert("Counldn't load messages");
            $("#loading-image").hide();
          }
        });
      });
    </script>
@endsection
