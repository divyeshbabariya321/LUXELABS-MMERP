@extends('layouts.app')

@section('styles')

    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection

@section('content')
    <script src="https://cdn.ckeditor.com/4.18.0/standard/ckeditor.js"></script>
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Mailing list Templates ({{count($mailings)}})</h2>
                    <div class="pull-left">
                        <form action="{{--{{ route('whatsapp.config.queue', $id) }}--}}" method="GET"
                              class="form-inline align-items-start form-filter">
                            <div class="form-group mr-3 mb-3 pl-2">
                                <input name="term" type="text" class="form-control global" id="term"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="name , image count, text count">
                            </div>
                            <div class="form-group ml-3">
                                <div class='input-group date' id='filter-date'>
                                    <input type='text' class="form-control global" name="date"
                                           value="{{ isset($date) ? $date : '' }}" placeholder="Date" id="date"/>

                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
                            </div>
                            <!-- added by pawan for filter the data through above selected id's -->
                            <div class="form-group ml-3">
                                <div class='input-group'>
                                    <select name="filter_mailinglist_category" class="form-control select2" id='filter_mailinglist_category'>
                                        <option value="">Select MailingList-Category</option>
                                        @if(!empty($MailingListCategory))
                                            @foreach($MailingListCategory as $key => $category)
                                                <option value="{{ $key }}">{{ $category }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger">&nbsp;</span>
                                </div>
                            </div>
                            <div class="form-group ml-3">
                                <div class='input-group'>
                                    <select name="filter_store_website" class="form-control select2" id='filter_store_website'>
                                        <option value="">Select Store-Website</option>
                                        @if(!empty($storeWebSites))
                                            @foreach($storeWebSites as $key => $storeWebSite)
                                                <option value="{{ $key }}">{{ $storeWebSite }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger">&nbsp;</span>
                                </div>
                            </div>
                            <!-- end -->
                            <button id="filter" type="submit" class="btn mt-0 btn-image"><img src="/images/filter.png"/>
                            </button>
                        </form>
                    </div>
                    <button type="button" class="btn mr-4 custom-button  float-right create-new-template-btn"
                            data-toggle="modal"
                            data-target="#exampleModalCenter">
                        Create a new email template
                    </button>

                    <button type="button" class="btn custom-button float-right mr-3" data-toggle="modal"
                            data-target="#addMailingListCategoryModal">
                        Add Category
                    </button>

                </div>
            </div>
        </div>

    </div>


    <div class="modal fade edit-template-modal" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create a new email template</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" id="form-store">
                        <input type="hidden" name="id" class="py-3" id="form_id">

                        <div class="form-group col-md-6">
                            <label>Name</label>
                            <input required type="text" name="name" class="form-control" id="form_name"
                                   aria-describedby="NameHelp" placeholder="Enter Name">
                            <span class="text-danger">&nbsp;</span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Subject</label>
                            <input required type="text" name="subject" class="form-control" id="form_subject" placeholder="Enter Subject">
                            <span class="text-danger">&nbsp;</span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>From Email</label>
                            <input required type="text" name="from_email" class="form-control" id="form_from_email" placeholder="Enter From Email">
                            <span class="text-danger">&nbsp;</span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Salutation</label>
                            <input required type="text" name="salutation" class="form-control" id="form_salutation" placeholder="Enter salutation">
                            <span class="text-danger">&nbsp;</span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Introduction</label>
                            <textarea required name="introduction" id="form_introduction" class="form-control" placeholder="Enter Introduction" rows='8' style="height: 34px;"></textarea>
                            <span class="text-danger">&nbsp;</span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Logo</label>
                            <input type="hidden" name="old_logo" class="py-3" id="form_logo">
                            <input required type="file" name="logo" class="py-3" id="logo">
                            <span class="text-danger">&nbsp;</span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Template Example</label>
                            <input required type="file" name="image" class="py-3" id="image">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Static Template</label>
                            <textarea required name="static_template" id="form_static_template" class="form-control" placeholder="Enter Static Template" rows='8' style="height: 34px;"></textarea>
                            <span class="text-danger">&nbsp;</span>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Form Mail Template</label>
                            {{ html()->select("mail_tpl", ["-- None --"] + $rViewMail)->class("form-control select2")->required()->id("form_mail_tpl") }}
                            <span class="text-danger">&nbsp;</span>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Category</label>
                            {{ html()->select("category", ["-- None --"] + $MailingListCategory)->class("form-control select2")->required()->id("template_category") }}
                            <span class="text-danger">&nbsp;</span>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Store Website</label>
                            {{ html()->select("store_website", ["-- None --"] + $storeWebSites)->class("form-control select2")->required()->id("store_website") }}
                            <span class="text-danger">&nbsp;</span>
                        </div>


                        <div class="form-group col-md-6">
                            <label for="mail_tpl">Store Website</label>
                            {{ html()->checkbox("store_website", null)->class(" select2")->attribute('required', true)->id("store_website") }}
                            <span class="text-danger">&nbsp;</span>
                        </div>

                        <!-- <div class="form-group">
                            <label for="exampleInputImageCount">Image Count</label>
                            <input required type="text" name="image_count" class="form-control"
                                   id="exampleInputImageCount"
                                   placeholder="Enter Image Count">
                            <span class="text-danger">&nbsp;</span>
                        </div> -->
                        <!-- <div class="form-group">
                            <label for="exampleInputTextCount">Text Count</label>
                            <input required type="text" name="text_count" class="form-control"
                                   id="exampleInputTextCount"
                                   placeholder="Enter Text Count">
                            <span class="text-danger">&nbsp;</span>
                        </div> -->
                        

                        <div class="form-group col-md-12">
                            <label for="image">Template HTML</label><br/>
                            <textarea cols="80" id="form_html_text" name="html_text" rows="10"></textarea>
                        </div>
                        <br/>
                        <br/>
                        <br/>
                        <!-- <div class="form-group d-flex flex-column">
                            <label for="image">File</label>
                            <input required type="file" name="file" class="py-3" id="image">
                            <span class="text-danger">&nbsp;</span>
                        </div> -->
                        <button id="store" type="submit" class="btn custom-button">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Model Add Mailinglist category START -->
    <div id="addMailingListCategoryModal" class="modal fade in" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" style="padding: 0 10px 10px">
                <div class="modal-header">
                    <h3>Add new category</h3>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form name="add-mailing-list-category" style="padding:10px;"
                      action="{{ route('mailingList.category.store') }}" method="POST">
                    @csrf
                    <div class="form-group mt-3">
                        <input type="text" class="form-control" name="name" placeholder="Name" value="" required="">
                    </div>
                    <button type="submit" class="btn btn-secondary">Add Category</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Model Add Mailinglist category END -->






    <div class="modal fade template-modal" id="addcontent" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle12" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle1">Add Content</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body content_body">

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade template-modal" id="addimage" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle13" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle1">Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{url('/marketing/mailinglist-templates/saveimagesfile')}}" enctype="multipart/form-data">
                        @csrf
                        <input required type="file" name="image" class="py-3" id="image">
                        <input type="hidden" name="id" id="i_id" value='0'>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>


                    <div id="image_body">

                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
    @endif
    <div class="col-md-12">
        <div class="table-responsive mt-3">
            <table class="table table-bordered" id="passwords-table" style="table-layout: fixed;">
                <thead>
                <tr>
                    <!-- <th style="">ID</th> -->
                    <th width="10%">Name</th>
                    <th width="10%">Mail Tpl</th>
                    <th width="10%">Subject</th>
                    <th width="9%">Static Tem</th>
                    <th width="6%">Category</th>
                    <th width="7%">Store Web</th>
                    <!-- <th style="">Image Count</th>
                    <th style="">Text Count</th> -->
                    <th width="7%">Template Ex</th>
                    <th width="6%">Salutation</th>
                    <th width="7%">Introduction</th>
                    <th width="7%">Logo</th>
                    <th width="3%">Action</th>
                    
                </tr>
                </thead>
                <tbody>
                @foreach($mailings as $value)
                    <tr>

                        <td class="expand-row table-hover-cell" style="word-break: break-all;">
                             <span class="td-mini-container">
                                {{ strlen($value["name"]) > 10 ? substr($value["name"], 0, 10).'...' : $value["name"] }}
                            </span>
                            <span class="td-full-container hidden">
                                {{$value["name"]}}
                            </span>
                        </td>
                        <td class="expand-row table-hover-cell" style="word-break: break-all;">
                            <span class="td-mini-container">
                                     {{ strlen($value["mail_tpl"]) > 10 ? substr($value["mail_tpl"], 0, 10).'...' : $value["mail_tpl"] }}
                                    </span>
                            <span class="td-full-container hidden">
                                    {{$value["mail_tpl"]}}
                                </span>
                        </td>
                        <td class="expand-row table-hover-cell" style="word-break: break-all;">
                                <span class="td-mini-container">
                                {{ strlen($value["subject"]) > 10 ? substr($value["subject"], 0, 10).'...' : $value["subject"] }}
                                </span>
                            <span class="td-full-container hidden">
                                    {{$value["subject"]}}
                                </span>
                        </td>
                        <td class="expand-row table-hover-cell" style="word-break: break-all;">
                             <span class="td-mini-container">
                                {{ strlen($value["static_template"]) > 10 ? substr($value["static_template"], 0, 10).'...' : $value["static_template"] }}
                             </span>
                            <span class="td-full-container hidden">
                                    {{$value["static_template"]}}
                            </span>
                        </td>

                        <td class="expand-row table-hover-cell" style="word-break: break-all;">
                             <span class="td-mini-container">
                               {{$value->category !== null ? strlen($value->category->title) > 10 ? substr($value->category->title, 0, 10).'...' : $value->category->title  : '-' }}
                             </span>
                            <span class="td-full-container hidden">
                                   {{$value->category !== null ? $value->category->title : '-'}}
                            </span>
                        </td>

                        <td class="expand-row table-hover-cell" style="word-break: break-all;">
                             <span class="td-mini-container">
                               {{$value->storeWebsite !== null ? strlen($value->storeWebsite->title) > 10 ? substr($value->storeWebsite->title, 0, 10).'...' : $value->storeWebsite->title  : '-' }}
                             </span>
                            <span class="td-full-container hidden">
                                   {{$value->storeWebsite !== null ? $value->storeWebsite->title : '-'}}
                            </span>
                        </td>
                    <!-- <td>{{$value["image_count"]}}</td> -->
                    <!-- <td>{{$value["text_count"]}}</td> -->
                        <td>
                            @if($value['example_image'])
                                <img style="width: 100px" src="{{ asset($value['example_image']) }}">
                            @endif
                        </td>
                        <td>{{$value["salutation"]}}</td>
                        <td class="expand-row table-hover-cell" style="word-break: break-all;">
                              <span class="td-mini-container">
                                     {{ strlen($value["introduction"]) > 10 ? substr($value["introduction"], 0, 10).'...' : $value["introduction"] }}
                                    </span>
                            <span class="td-full-container hidden">
                                    {{$value["introduction"]}}
                                </span>

                        </td>
                        <td>
                            @if($value['logo'])
                                <img style="width: 100px" src="{{ asset($value['logo']) }}">
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn({{$value["id"]}})"><i class="fa fa-arrow-down"></i></button>
                        </td>
                    </tr>
                    <tr class="action-btn-tr-{{$value["id"]}} d-none">
                        <td>Action</td>
                        <td colspan="10">
                            <a data-id="{{ $value['id'] }}" class="delete-template-act pr-1" href="javascript:;" style="color: gray;">
                                <i class="fa fa-trash"></i>
                            </a>
                            <a data-id="{{ $value['id'] }}" data-storage='{{ $value }}' class="edit-template-act pr-1"
                               href="javascript:;" style="color: gray;">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a data-id="{{ $value['id'] }}" class="add-content pr-1"
                               href="javascript:;" style="color: gray;">
                                <i class="fa fa-send"></i>
                            </a>
                            <a data-id="{{ $value['id'] }}" class="add-image pr-1"
                               href="javascript:;" style="color: gray;">
                                <i class="fa fa-list"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if(isset($mailings))
                {{$mailings->appends($_GET)->links()}}
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        CKEDITOR.replace('form_html_text');

        $(document).ready(function () {
            $(".select-multiple").multiselect();
            $(".select-multiple2").select2();
        });

        $('#filter-date').datetimepicker({
            format: 'YYYY-MM-DD',
        });

        $('#filter-whats-date').datetimepicker({
            format: 'YYYY-MM-DD',
        });

        $('#store').on('click', function (e) {
            e.preventDefault();

            var id = $("#form-store").find("#form_id").val();
            var name = $("#form-store").find("#form_name").val();
            var subject = $("#form-store").find('#form_subject').val();
            var from_email = $("#form-store").find("#form_from_email").val();
            var salutation = $("#form-store").find("#form_salutation").val();
            var introduction = $("#form-store").find("#form_introduction").val();
            var static_template = $("#form-store").find("#form_static_template").val();
            var mail_tpl = $("#form-store").find("#form_mail_tpl").val();
            var category = $("#form-store").find("#template_category").val();
            var store_website = $("#form-store").find("#store_website").val();
            var logo = $("#form-store").find("#logo")[0].files[0];
            var image = $("#form-store").find("#image")[0].files[0];
            var html_text = CKEDITOR.instances['form_html_text'].getData();

            var productForm = new FormData();
            productForm.append("id", id);
            productForm.append("name", name);
            productForm.append("subject", subject);
            productForm.append("from_email", from_email);
            productForm.append("salutation", salutation);
            productForm.append("introduction", introduction);
            productForm.append("static_template", static_template);
            productForm.append("mail_tpl", mail_tpl);
            productForm.append("category", category);
            productForm.append("store_website", store_website);
            productForm.append("logo", logo);
            productForm.append("image", image);
            productForm.append("html_text", html_text);

            var form = $('#form-store')[0];
            var formData = new FormData(form);
            $.ajax({
                url: "/marketing/mailinglist-templates/store",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                contentType: false,
                processData: false,
                type: 'POST',
                data: productForm
            }).done(function (response) {
                if (response.errors) {
                    var obj = response.errors;
                    for (var prop in obj) {
                        $('input[name="' + prop + '"]').next().html(obj[prop]);
                    }
                } else {
                    location.reload()
                }
            }).fail(function (errObj) {
                console.log(errObj);
            });
        });

        // pawan added for calling the function on change for maillistcategory & StoreWebsite filter
        $('#filter_mailinglist_category').on('change',function (e){
            e.preventDefault();
            var term = $('#term').val();
            var date = $('#date').val();
            var StoreWebsite = $('#filter_store_website').val();
            var MailingListCategory = $('#filter_mailinglist_category').val();
            // alert(MailingListCategory);
            // alert(StoreWebsite);

            $.ajax({
                url: "/marketing/mailinglist-ajax",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                data: {
                    term: term,
                    date: date,
                    MailingListCategory: MailingListCategory,
                    StoreWebsite: StoreWebsite
                }
            }).done(function (response) {
                $('tbody').html('');
                $('tbody').html(response.mailings);
            }).fail(function (errObj) {
                console.log(errObj);
            });
            // alert(MailingListCategory);
        });
        $('#filter_store_website').on('change',function (e){
            e.preventDefault();
            var term = $('#term').val();
            var date = $('#date').val();
            var StoreWebsite = $('#filter_store_website').val();
            var MailingListCategory = $('#filter_mailinglist_category').val();
            // alert(MailingListCategory);
            // alert(StoreWebsite);

            $.ajax({
                url: "/marketing/mailinglist-ajax",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                data: {
                    term: term,
                    date: date,
                    MailingListCategory: MailingListCategory,
                    StoreWebsite: StoreWebsite
                }
            }).done(function (response) {
                $('tbody').html('');
                $('tbody').html(response.mailings);
            }).fail(function (errObj) {
                console.log(errObj);
            });
            // alert(MailingListCategory);
        });
        // end

        $('#filter').on('click', function (e) {
            e.preventDefault();
            var term = $('#term').val();
            var date = $('#date').val();
            var StoreWebsite = $('#filter_store_website').val();
            var MailingListCategory = $('#filter_mailinglist_category').val();
            $.ajax({
                url: "/marketing/mailinglist-ajax",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                data: {
                    term: term,
                    date: date,
                    MailingListCategory: MailingListCategory,
                    StoreWebsite: StoreWebsite
                }
            }).done(function (response) {
                $('tbody').html('');
                $('tbody').html(response.mailings);
            }).fail(function (errObj) {
                console.log(errObj);
            });
        })


        var deleteAct = function (ele) {
            var id = ele.data("id");
            $.ajax({
                type: 'GET',
                url: "/marketing/mailinglist-templates/" + id + '/delete'
            }).done(function (response) {
                if (response.code == 200) {
                    ele.closest("tr").remove();
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
            }).fail(function (response) {
                console.log(response);
            });
        };

        $(document).on("click", ".delete-template-act", function (e) {
            e.preventDefault();
            var c = confirm("Are you sure you want to delete this?");
            if (c === true) {
                deleteAct($(this));
            }
        });

        $(document).on("click", ".edit-template-act", function (e) {
            e.preventDefault();
            let storage = $(this).data("storage");
            var findForm = $("#form-store");
            $.each(storage, function (k, v) {
                var formField = findForm.find("#form_" + k);
                if (formField.length > 0) {
                    var tagName = formField.prop("tagName").toLowerCase();
                    if(tagName == "input" || tagName == "hidden" || tagName == "textarea") {
                        if(k === 'html_text') {
                            CKEDITOR.instances['form_html_text'].setData(v);
                        } else {
                            formField.val(v);
                        }
                    } else if (tagName == "select") {
                        var options = formField.find("option");
                        if (options.length > 0) {
                            $.each(options, function (k, r) {
                                if ($(r).val() == v) {
                                    $(r).prop("selected", true);
                                }
                            });
                        }
                    }
                }
            });

            /**
             * Make Category and storeWebsite Selected
             * */

            findForm.find('select[name=category]').find('option[value="' + storage.category_id + '"]').val(storage.category_id).prop('selected', true)
            findForm.find('select[name=store_website]').find('option[value="' + storage.store_website_id + '"]').val(storage.store_website_id).prop('selected', true)


            $(".edit-template-modal").modal("show");
        });

        $(document).on("click", ".create-new-template-btn", function () {
            document.getElementById("form-store").reset();
            // $(".template-modal").modal("show");
        });


        /**
         * Create mailingList Template Category and error notification.
         */

        function error_notification(form, path) {

            if (form !== null) {

                if (form.find('label.error').length > 0) {
                    form.find('label.error').remove();
                }

                $.each(path, function (index, element) {

                    const html = '<label class="error" for="' + index + '">' + element[0] + '</label>';
                    $(html).insertAfter(form.find('[name="' + index + '"]'));
                })
            }
        }

        $('form[name="add-mailing-list-category"]').submit(function (e) {

            e.preventDefault();
            let form = $(this);

            $(this).find('[type="submit"]').html('Please wait...').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: '{{ route('mailingList.category.store') }}',
                data: new FormData($(this)[0]),
                contentType: false,
                processData: false,
            }).done(function (response) {

                if (response.status) {
                    form[0].reset();
                    toastr['success'](' Mailinglist category has been successfully created.', 'success');

                    form.parents('#addMailingListCategoryModal').modal('hide');

                }

                form.find('[type="submit"]').html('Add Category').prop('disabled', false);

            }).fail(function (response) {

                if (response.responseJSON.errors) {
                    error_notification(form, response.responseJSON.errors)
                }

                form.find('[type="submit"]').html('Add Category').prop('disabled', false);
            });

            return false;

        });


        $('.add-content').on('click', function () {
            let id = $(this).attr('data-id');

            $.ajax({
                url: "{{ route('displayContentModal') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                },
                success: function (response) {
                    $('.content_body').html('');
                    $('.content_body').append(response.html);
                    $('#addcontent').modal('show');
                },
                error: function (response) {

                }
            });
        });

        $('.add-image').on('click', function () {
            let id = $(this).attr('data-id');

            $.ajax({
                url: "{{ url('marketing/mailinglist-templates/images_file') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                },
                success: function (response) {

                    $('#image_body').html('');
                    $('#i_id').val(id);
                    $('#image_body').html(response);
                    $('#addimage').modal('show');

                },
                error: function (response) {

                }
            });
        });

        function Showactionbtn(id) {
            $(".action-btn-tr-" + id).toggleClass('d-none')
        }

    </script>
@endsection