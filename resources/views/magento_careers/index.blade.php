@extends('layouts.app')

@section('content')
    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Careers <span class="count-text"></span></h2>
        </div>
        <br>
        <div class="col-lg-12 margin-tb" id="page-view-result">
            <div class="col-lg-12 pl-5 pr-5">
                <div style="display: flex !important; float: right !important;">
                    <div>
                        <a href="#" class="btn btn-xs btn-secondary create-new-career">Create</a>
                    </div>
                    &nbsp;&nbsp;
                    <input type="text" class="location-search" name="location" placeholder="Location">
                    &nbsp;&nbsp;
                    <input type="text" class="type-search" name="type" placeholder="Type">
                    &nbsp;&nbsp;
                    <input type="text" class="description-search" name="description" placeholder="Description">
                    <div class="form-check">
                        <input class="form-check-input is-active-search" type="checkbox" id="is-active-search"
                            name="is_active" checked>
                        <label class="form-check-label" for="is-active-search">
                            Is active
                        </label>
                    </div>
                    &nbsp;
                    <select class="form-control select2" name="store_ids[]" id="store_ids" multiple="multiple"
                        style="width: 200px;">
                        @foreach ($storeWebsites as $storeWebsite)
                            <option value="{{ $storeWebsite->id }}">{{ $storeWebsite->title }}</option>
                        @endforeach
                    </select>
                    <button style="display: inline-block; width: 10%" class="btn btn-sm btn-image btn-secondary btn-search">
                        <img src="/images/search.png" style="cursor: default;">
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-12 pl-5 pr-5">
            <form action="/store-website/generate-api-token" method="post">
                <?php echo csrf_field(); ?>

                <div class="col-md-12">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered overlay api-token-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th width="15%">Title</th>
                                    <th width="15%">Store Websites</th>
                                    <th width="20%">Location</th>
                                    <th width="20%">Type</th>
                                    <th width="45%">Description</th>
                                    <th>Is active</th>
                                    <th width="30%">Date</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody id="filter_magento_career_data">
                                <?php /** @var \App\Models\MagentoModuleCareers $career */ ?>
                                @foreach ($careers as $career)
                                    <tr>
                                        <td class="td-id-{{ $career->getId() }}">
                                            {{ $career->getId() }}
                                        </td>
                                        <td class="td-title-{{ $career->getId() }}">
                                            {{ $career->getTitle() }}
                                        </td>
                                        <td class="td-store-websites-{{ $career->getId() }}">
                                            @php echo implode(', ', array_map(fn ($item) => $item->title, (array)$career->getStoreWebsites())) @endphp
                                        </td>
                                        <td class="td-location-{{ $career->getId() }}">
                                            {{ $career->getLocation() }}
                                        </td>
                                        <td class="td-type-{{ $career->getId() }}">
                                            {{ $career->getType() }}
                                        </td>
                                        <td class="td-description-{{ $career->getId() }}">
                                            {{ Str::limit($career->getDescription(), 255) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="td-mini-container">
                                                <input type="checkbox" class="isActive td-is-active-{{ $career->getId() }}"
                                                    name="is_active" <?= $career->getIsActive() ? 'checked' : '' ?> />
                                            </span>
                                        </td>
                                        <td class="td-created-at-{{ $career->getId() }}">
                                            {{ $career->getCreatedAt() }}
                                        </td>
                                        <td>
                                            <a href="#"
                                                class="btn btn-xs btn-secondary btn-edit-career td-edit-{{ $career->getId() }}"
                                                data-id="{{ $career->getId() }}"
                                                data-json='@json($career)'>Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="modal fade" id="career-create-new" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Create new Position</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form action="{{ route('magento_module_listing_careers_create') }}" method="post">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive mt-3">
                                            <input hidden type="text" class="form-control" name="id"
                                                placeholder="Enter id" id="career-id">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" class="form-control" name="title"
                                                    placeholder="Enter title" id="career-title">
                                            </div>
                                            <div class="form-group">
                                                <label>Store websites</label>
                                                <select id="multi_store_websites"
                                                    class="form-control input-sm career-store-websites"
                                                    name="store_websites[]" required multiple>
                                                    @foreach ($storeWebsites as $storeWebsite)
                                                        <option value="{{ $storeWebsite->id }}">
                                                            {{ $storeWebsite->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Location</label>
                                                <input type="text" class="form-control" name="location"
                                                    placeholder="Enter location" id="career-location">
                                            </div>
                                            <div class="form-group">
                                                <label>Type</label>
                                                <input type="text" class="form-control" name="type"
                                                    placeholder="Enter type" id="career-type">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-check-label">Is active</label>
                                                <input type="checkbox" class="form-check-input" name="is_active"
                                                    id="career-is-active" value="yes">
                                            </div>
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea class="form-control" name="description" required placeholder="Enter description" id="career-description"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit"
                                                class="btn btn-secondary submit_create_career float-right float-lg-right">
                                                Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function updateCareerData(careerId) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var url = "{{ route('fetchedCareerRecords') }}";
            var formData = {
                'careerId': careerId
            }

            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(resp) {
                    if (resp['success']) {
                        $('#loading-image-preview').hide();
                        $('#career-create-new').modal('show');
                        restoreForm();
                        $('#career-id').val(resp['career-id']);
                        let data = JSON.parse(resp['data-json']);
                        $('#career-title').val(data['title']);
                        $('#career-description').val(data['description']);
                        $('#career-type').val(data['type']);
                        $('#career-location').val(data['location']);
                        if (data['is_active']) {
                            $('#career-is-active').attr('checked', data['is_active']);
                        }
                        $('#career-id').val(data['id']);
                        $.each(data['store_website_id'], function(i, e) {
                            $("#multi_store_websites option[value='" + e + "']").prop(
                                "selected",
                                true);
                        });
                        $("#multi_store_websites").select2();
                    }
                },
                error: function(err) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#career-create-new').modal('hide');
                    toastr["error"](err.responseJSON.message);
                }
            })
        }

        $("#multi_store_websites").select2();
        $("#store_ids").select2();
        $(document).on("click", ".create-new-career", function(e) {
            e.preventDefault();
            $('#career-create-new').modal('show');
            restoreForm();

            $("#multi_store_websites").select2();
        });

        $(document).on("click", ".submit_create_career", function(e) {
            e.preventDefault();
            var url = "{{ route('magento_module_listing_careers_create') }}";
            var formData = $(this).closest('form').serialize();

            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function(resp) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#store-create-project').modal('hide');
                    if (resp.code == 200) {
                        toastr["success"](resp.message);
                        let career = resp.career;
                        let careerId = career.id;
                        $('.td-description-' + careerId).text(career.description);
                        $('.td-title-' + careerId).text(career.title);
                        $('.td-type-' + careerId).text(career.type);
                        $('.td-location-' + careerId).text(career.location);
                        $('.td-created-at-' + careerId).text(career.created_at);
                        $('.td-store-websites-' + careerId).text(career.store_website_id);
                        $('.td-edit-' + careerId).attr('data-json', resp.career_json);
                        if (!career.is_active) {
                            $('.td-is-active-' + careerId).removeAttr('checked');
                        } else {
                            $('.td-is-active-' + careerId).attr('checked', career.is_active);
                        }


                    } else {
                        toastr["error"](resp.message);
                    }
                },
                error: function(err) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#career-create-new').modal('hide');
                    toastr["error"](err.responseJSON.message);
                }
            })
        });

        $('a.btn-edit-career').click(function(e) {
            e.preventDefault();
            $('#career-create-new').modal('show');

            restoreForm();

            $('#career-id').val($(this).attr('data-id'));

            let data = JSON.parse($(this).attr('data-json'));

            $('#career-description').val(data.description);
            $('#career-title').val(data.title);
            $('#career-type').val(data.type);
            $('#career-location').val(data.location);
            if (data.is_active) {
                $('#career-is-active').attr('checked', data.is_active);
            }
            $('#career-id').val(data.id);

            $.each(data.store_website_id, function(i, e) {
                $("#multi_store_websites option[value='" + e + "']").prop("selected", true);
            });

            $("#multi_store_websites").select2();
        });

        var restoreForm = function() {
            $('#career-id').val('');
            $('#career-title').val('');
            $('#career-description').val('');
            $('#career-type').val('');
            $('#career-location').val('');
            $("#multi_store_websites").val('');
            $('#career-is-active').removeAttr('checked');
        }

        $(document).on("click", ".btn-search", function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var url = "{{ route('magento_module_listing_careers_by_filter') }}";
            var formData = {
                'location': $('.location-search').val(),
                'type': $('.type-search').val(),
                'descriptions': $('.description-search').val(),
                'store_ids': $('#store_ids').val()
            }

            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(resp) {

                    if (resp['success']) {
                        $('#filter_magento_career_data').html('');
                        $('#filter_magento_career_data').html(resp['html']);
                        $('#loading-image-preview').hide();
                    }
                },
                error: function(err) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#career-create-new').modal('hide');
                    toastr["error"](err.responseJSON.message);
                }
            })
        });
    </script>
@endsection
