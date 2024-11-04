@extends('layouts.app')

@section('title', 'Google Search')


@section('styles')
    <style type="text/css">
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        .filter-icon {
            font-size: 16px;
        }

        .btn-trash {
            font-size: 16px;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(16px);
            -ms-transform: translateX(16px);
            transform: translateX(16px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }

        .select-box .select2-container .selection .select2-selection--multiple .select2-selection__rendered {
         display: block;
        }

        .select-box .select2-container .selection .select2-selection--multiple .select2-selection__rendered + .loading-icon {
             position: absolute;
            top: 7px;
        }
    </style>
@endsection
@section('content')
    @include('partials.flash_messages')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="container-fluid">
        @if(Session::has('message'))
            <script>
                alert("{{Session::get('message')}}")
            </script>
        @endif
        <div class="row">
            <div class="col-md-12 px-0">
                <h2 class="page-heading">Google Search Keywords (<span>{{ $keywords_total }}</span>) </h2>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                               <form  >
                                    <div class="">
                                        <div class="row align-items-center">
                                            <div class="col-md-5">
                                                <input name="term" type="search" class="form-control"
                                                       value="{{ isset($term) ? $term : '' }}"
                                                       placeholder="Search Keyword" id="search-keyword-text">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-check d-flex align-items-center pl-0">
                                                        <input class="form-check-input mt-0" type="checkbox"
                                                               name="priority"
                                                               id="priority_filter">
                                                        <label class="form-check-label pl-4 ml-2 font-weight-normal"
                                                               for="priority_filter">
                                                            Priority
                                                        </label>
                                                    </div>
                                                    <div>
                                                        <button type="button" onclick="filterData()" class="btn btn-secondary ml-4"><i
                                                                    class="fa fa-filter filter-icon"
                                                                    aria-hidden="true"></i> Apply Filter
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                            <div class="col-md-6 flex flex-end">
                                <!-- Button Add New Keyword -->
                                <button type="button" class="btn btn-secondary mr-2" onclick="showForm('add-keyword-block')">
                                    Add new Keyword
                                </button>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-secondary mr-2" data-toggle="modal"
                                        data-target="#addVariantModal">
                                    Add Postfix
                                </button>
                                <!-- Button Generate Keyword Strings -->
                                <button type="button" class="btn btn-secondary"
                                        onclick="showForm('generate-string-block')">
                                    Generate Keywords
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3" id="form-div" style="display: none">
                    <!-- Generate Keywords modal -->
                    <div class="p-3">
                        <div id="generate-string-block" class="generate-string-block form-blocks" style="display: none">
                            <h4>Generate Keywords</h4>
                            <form method="post" id="generate-keyword-string-form">

                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group select-box">
                                            <label for="brand-list">Brand</label>
                                            {{ html()->select("brand", [])->class("form-control brand-list")->id('brand-list') }}
                                            <span class="product-title-show"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group select-box">
                                            <label for="name">Select Category</label>
                                            {!! $new_category_selection !!}
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group select-box">
                                            <label for="variants-list">Select Postfix</label>
                                            <select id="variants-list" class="form-control w-100 select-multiple"
                                                    name="variants" data-placeholder=" Select Postfix" multiple>
                                                @foreach($variants as $key => $variant)
                                                    <option value="{{ $variant }}">{{ $variant }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-ms-2">
                                        <div class="form-group">
                                            <label style="visibility: hidden" class="w-100"> Action </label>
                                            <button class="btn btn-secondary" type="button" onclick="generateString(this)">
                                                Generate Keywords
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-ms-1">
                                        <div class="form-group">
                                            <label style="visibility: hidden" class="w-100"> Action </label>
                                            <button class="btn btn-secondary" type="button" onclick="closeFormDiv()">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="add-keyword-block" class="add-keyword-block form-blocks" style="display: none">
                            <h4>Add New Keyword</h4>
                            <form method="post"
                                  action="{{ action([\App\Http\Controllers\GoogleSearchController::class, 'store']) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Keyword</label>
                                            <input type="text" name="name" id="name" placeholder="sololuxuryindia"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Add?</label>
                                            <button class="btn-block btn btn-secondary">Add Keyword</button>
                                        </div>
                                    </div>
                                    <div class="col-ms-1">
                                        <div class="form-group">
                                            <label style="visibility: hidden" class="w-100"> Action </label>
                                            <button class="btn btn-secondary" type="button" onclick="closeFormDiv()">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="platform_id" value="2">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-5 card-body">
                <table class="table-striped table-bordered table table-sm" id="keyword-list-table">
                    <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Keyword</th>
                        <th>Priority</th>
                        <th>Run Scraper</th>
                        <th>Created By</th>
                        <th>Created On</th>
                        <th width="50px">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="5">Processing...</td>
                    </tr>
                    </tbody>
                   
                </table>
               
            </div>
        </div>
    </div>
    <!-- Add variants modal -->
    <div class="modal fade " id="addVariantModal" tabindex="-1" role="dialog" aria-labelledby="addVariantModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Keyword Postfix List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Postfix</label>
                                    <input type="text" name="variant_name" id="variant_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Add?</label>
                                    <button type="button" class="btn-block btn btn-secondary" id="add_keyword_variant">
                                        Add Postfix
                                    </button>
                                </div>
                            </div>
                        </div>

                        <table id="variant_list_table" class="table table-striped table-bordered w-100">
                            <thead>
                            <tr>
                                <th width="16%">ID</th>
                                <th width="16%">Postfix</th>
                                <th width="16%">Date</th>
                                <th width="16%">Action</th>
                            </tr>
                            </thead>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/media-card.css') }} ">
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        // loading icon html
        const loadingIcon = '<i class="fa fa-spinner fa-spin loading-icon"></i>';

        function callScraper(id) {
            var buttonCaption = $('#runScrapper_' + id).html();
            $('#runScrapper_' + id).html('Initiating...').prop('disabled', true);
            //ajax call coming here...
            $.ajax({
                url: "{{ route('google.search.keyword.scrap') }}",
                type: 'GET',
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function (data) {
                    if (data.error) {
                        toastr['error'](data.error);
                    } else {
                        toastr['success']('Scrapper initiated Successfully');
                    }
                    $('#runScrapper_' + id).prop('disabled', false).html(buttonCaption);
                },
                error: function (err) {
                    toastr['error'](err.error);
                    $('#runScrapper_' + id).prop('disabled', false).html(buttonCaption);
                }
            });
        }

        function showForm(elementId) {
            $('#form-div').show()
            $('.form-blocks').hide();
            $('#' + elementId).show();

        }

        function updatePriority(e) {
            let id = e.value;

            $.ajax({
                type: 'GET',
                url: '{{ route('google.search.keyword.priority') }}',
                data: {
                    id: id,
                    type: e.checked ? 1 : 0
                }, success: function (data) {
                    console.log(data);
                    if (data.status === 'error') {
                        toastr['error'](data.error);
                        // alert('Priority Limit Exceded');
                        e.val(!e.value);
                    } else {
                        toastr['success']('Keyword Priority ' + (e.checked ? 'Added' : 'Removed'));
                    }
                },
                error: function (data) {
                    toastr['error']('Priority Limit Exceded');
                }
            });
        }

        function deleteRow(id) {
            $.ajax({
                url: "{{ url('variant') }}/" + id,
                type: 'DELETE',
                headers: {
                    "X-CSRF-TOKEN": "{{csrf_token()}}"
                },
                beforeSend: function () {
                    // Show the loading icon when the AJAX request starts
                    $('#delete-variant-' + id).append(loadingIcon);
                },
                success: function (data) {
                    toastr['success']('Deleted Succesfully.');
                    $('#variant_list_table').DataTable().ajax.reload();
                }
            });
        }

        function deleteKeyword(id) {
            $.ajax({
                type: "POST",
                url: "{{ url('/google/search/delete') }}/" + id,
                data: {
                    _token: '{{csrf_token()}}',
                },
                beforeSend: function () {
                    // Show the loading icon when the AJAX request starts
                    $('#delete-keyword-' + id).append(loadingIcon);
                },
                success: function (msg) {
                    toastr['success']('Deleted Succesfully.');
                    $('#keyword-list-table').DataTable().ajax.reload();
                }
            });
        }

        $(document).ready(function () {
            $('.select-multiple').select2({width: '100%'});

            /*POSTFIX JAVASCRIPT DATATABLE*/
            $('#variant_list_table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                ajax: "{{ route('list.keyword.variant') }}",
                columns: [
                    {
                        data: null,
                        width: '70%'
                    },
                    {data: "keyword"},
                    {
                        data: "created_at",
                        render: function (data, type, row) {
                            return moment(data).format('DD-MM-YYYY');
                        }
                    },
                    {
                        data: null,
                        width: '100px'
                    },
                ],
                columnDefs: [
                    {
                        targets: 3,
                        data: null,
                        render: function (data, type, row, meta) {
                            return `<a href="javascript:void(0)" class="text-secondary" id="delete-variant-${data.id}" onclick="deleteRow(${data.id})"><i class="fa fa-trash"></i></a>`;
                        }
                    }
                ],
                createdRow: function (row, data, index) {
                    $('td', row).eq(0).html(index + 1);
                }
            });

            $('#add_keyword_variant').on('click', function (e) {

                e.preventDefault();
                if (!$('#variant_name').val()){
                    toastr['error']('postfix field if required');
                }
                var name = $('#variant_name').val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('add.keyword.variant') }}",
                    data: {keyword: name, _token: "{{ csrf_token() }}"},
                    success: function (msg) {
                        $('#variant_list_table').DataTable().ajax.reload();
                        $('#variant_name').val('');
                    }
                });
            });
            /*POSTFIX JAVASCRIPT END*/

            /* KEYWORD TABLE DATATABLE SCRIPT START */
            const keywordListTable = $('#keyword-list-table').DataTable({
                language: {
                    searchPlaceholder: "Search Keyword"
                },
                pageLength: 50,
                processing: true,
                serverSide: true,
                ordering: true,
                searching: false,
                ajax: {
                    url: "{{ route('google.search-keyword.list') }}",
                    data: function (d) {
                        d.priority = ($('#priority_filter').is(":checked")) ? 'on' : undefined
                        d.search = $('#search-keyword-text').val()
                    }
                },
                columns: [
                    {data: null},
                    {data: "hashtag"},
                    {data: "priority"},
                    {data: "hashtag"},
                    {data: "creator",
                            "render": function (data, type, row) {
                                return data?.name ?? '-';
                            }},
                    {data: "created_at",
                           "render": function (data, type, row) {
                                return moment(data).format('DD-MM-YYYY');
                            }},
                    {data: null},
                ],
                columnDefs: [
                    {
                        targets: 2,
                        data: null,
                        render: function (data, type, row, meta) {
                            return `<label class="switch mb-0"><input type="checkbox" onclick="updatePriority(this)" ${row.priority === 1 ? 'checked' : ''} class="checkbox" value=${row.id}"><span class="slider round"></span></label>`;
                        }
                    },
                    {
                        targets: 3,
                        data: null,
                        render: function (data, type, row, meta) {
                            return `<button class="btn py-0 btn-default " id="runScrapper_${row.id}" onclick="callScraper('${row.id}')">Run Scraper For ${row.hashtag}</button>`;
                        }
                    },
                    {
                        targets: 6,
                        data: null,
                        render: function (data, type, row, meta) {
                            return `<button class="btn btn-default btn-trash btn-image border-0 btn-sm" id="delete-keyword-${data.id}" onclick="deleteKeyword(${data.id})"><i class="fa fa-trash"></i></button>`;
                        }
                    }
                ],
                createdRow: function (row, data, index, meta) {
                    var pageInfo = keywordListTable.page.info();
                    $('td', row).eq(0).html((pageInfo.length * pageInfo.page) + index + 1);
                }
            });

            /*$('#search-keyword-text, #priority_filter').change(function(){
                $('#keyword-list-table').DataTable().ajax.reload();
            });*/
        });

        function filterData() {
            $('#keyword-list-table').DataTable().ajax.reload();
        }

        /* KEYWORD TABLE DATATABLE SCRIPT END */

        function generateString(e) {
            const formData = $('#generate-keyword-string-form').serializeArray();
            const groupedData = [];

            for (let i = 0; i < formData.length; i++) {
                if (formData[i].name === '_token') {
                    continue;
                }
                if (!groupedData[formData[i].name]) {
                    groupedData[formData[i].name] = [];
                }
                groupedData[formData[i].name].push(formData[i].value);
            }
            console.log(formData, groupedData);

            $.ajax({
                type: "POST",
                url: "{{ route('keyword.generate') }}",
                data: {
                    _token: '{{csrf_token()}}',
                    data: {
                        brand: groupedData.brand,
                        category: groupedData.category,
                        variants: groupedData.variants,

                    }
                },
                beforeSend: function () {
                    toastr['warning']('Please wait while keywords are generating.');
                },
                success: function (msg) {
                    $('#keyword-list-table').DataTable().ajax.reload();
                    toastr['success']('Keywords generated Successfully.');
                }
            });
        }

        // Create a variable to keep track of the current AJAX request
        var brandSearch = null;

        // Initialize Select2 with AJAX data source
        $('#brand-list').select2({
            multiple: true,
            width: '100%',
            ajax: {
                url: '{{ route("brand.list") }}',
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                dataType: 'json',
                beforeSend: function () {
                    if (brandSearch != null) {
                        brandSearch.abort();
                    }
                    brandSearch = this.xhr;
                    // Show the loading icon when the AJAX request starts
                    if (!$('.loading-icon').length) {
                        $('#brand-list').parent().find('.select2-selection').append(loadingIcon);
                    }
                },
                complete: function () {
                    brandSearch = null;
                    // Hide the loading icon when the AJAX request completes
                    $('#brand-list').parent().find('.fa-spinner').remove();
                },
                processResults: function (response) {
                    var results = [];
                    $.each(response.data, function (index, item) {
                        results.push({
                            // id: item.id,
                            id: item.name, // so we dont g=have to fetch name again in backend
                            text: item.name
                        });
                    });
                    return {
                        results: results
                    };
                }
            }
        });

        $('#product-category').select2({multiple: true, width: '100%'})

        function closeFormDiv() {
            $('#form-div').hide();
        }
    </script>
@endsection