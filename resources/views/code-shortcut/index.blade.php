@extends('layouts.app')
@section('title', 'Code Shortcut')
@section('content')
<script>

    function confirmDelete(code, url) {
        let result = confirm("Are you sure you want to delete the code " + code + "?");
        if (result) {
            window.location.href = url;
        }
    }
</script>
<style type="text/css">
	#loading-image {
		position: fixed;
		top: 50%;
		left: 50%;
		margin: -50px 0px 0px -50px;
		z-index: 60;
	}
	/* CSS to make specific modal body scrollable */
    #show_full_log_modal .modal-body {
      max-height: 400px; /* Maximum height for the scrollable area */
      overflow-y: auto; /* Enable vertical scrolling when content exceeds the height */
    }
</style>
<div id="myDiv">
	<img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>
<div class="row" id="product-template-page">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">Code Shortcut (<span id="user_count">{{ $codeshortcut->total() }}</span>)</h2>
		<div class="pull-left">
			<div class="form-group">
				<div class="row">
					<div class="col-md-3">
						<select class="form-control select-multiple" id="supplier-select">
							<option value="">Select Supplier</option>
							@foreach($suppliers as $supplier)
							<option value="{{ $supplier->id }}">{{ $supplier->supplier }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3">
						<input name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}" placeholder="Name of Code" id="term">
					</div>
					<br><br>
					<div class="col-md-3">
						<input name="title" type="text" class="form-control" value="{{ isset($title) ? $title : '' }}" placeholder="Name of title" id="code_title">
					</div>
					<div class="col-md-3">
						<select class="form-control select-multiple" id="createdAt-select">
							<option value="">Select SortBy CreatedAt</option>						
							<option value="asc">Asc</option>
							<option value="desc">Desc</option>
						</select>
					</div>
					<div class="col-md-3">	
						<h5>Search Platform	</h5>	
						<select class="form-control globalSelect2" multiple="true" id="platform-select" name="platforms" placeholder="Select Platform">
							<option value="">Select Platform</option>
							@foreach($platforms as $platform)
							<option value="{{ $platform->id }}">{{ $platform->name }}</option>
							@endforeach
						</select>
						
					</div>
					
					<div class="col-md-3">
						<h5>Search Websites	</h5>	
						{{ html()->multiselect("websitenames[]", \App\CodeShortcut::pluck('website', 'website')->toArray(), request('websitenames'))->class("form-control globalSelect2")->placeholder("Select Website")->id("website_select") }}
					</div>
					<div class="col-md-2">
						<br>
						<br>
						<button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png" /></button>
						<button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png" /></button>
						<a href="{{route('codeShort.log.truncate')}}" class="btn btn-primary" onclick="return confirm('{{ __('Are you sure you want to Truncate a Data?Note : It will Remove All data') }}')">Truncate Data </a>
					</div>
				</div>
			</div>
		</div>
		<div class="pull-right pr-4">
			<a href="/code-shortcuts/folder/list" class="btn btn-secondary ">+Add Folder</a>
			<button type="button" class="btn btn-secondary create-platform-btn" onclick="showCodeShortcutPlatformModal()">+ Add Platform</button>
			<button type="button" class="btn btn-secondary create-product-template-btn" onclick="showCreateCodeShortcutModal()">+ Add Code</button>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		@include('partials.flash_messages', ['withRole' => true])
	</div>
</div>

<div class="col-12">
    <h3>Assign Permission to User</h3>
    <form class="form-inline" id="update_user_permission" action="/code-shortcuts/folder/user/permission" method="POST">
      <div class="form-group">
        <div class="input-group">
          <select name="per_folder_name" class="form-control" id="per_folder_name" required>
            <option value="">--select folder for Permission--</option>
            <?php
            $ops = 'id';
            foreach ($folders as $folder) {
                $selected = '';
                if ($folder->id == request('per_folder_name')) {
                    $selected = 'selected';
                }
                echo '<option value="'.$folder->id.'" '.$selected.'>'.$folder->name.'</option>';
            }
            ?>
          </select>
        </div>
      </div> &nbsp;&nbsp;&nbsp;
      <div class="form-group">
        <div class="input-group">
          <select name="per_user_name" class="form-control" id="per_user_name" required>
            <option value="">--select user for Permission--</option>
            <?php
            foreach ($users as $user) {
                $selected = '';
                if ($user->id == request('per_user_name')) {
                    $selected = 'selected';
                }
                echo '<option value="'.$user->id.'" '.$selected.'>'.$user->name.'</option>';
            }
            ?>
          </select>
        </div>
      </div> &nbsp;&nbsp;
      <button type="submit" class="btn custom-button update-userpermission">Update User Permission</button>
    </form>
  </div>

<div class="row">
    <div class="col-md-12">
        <div class="pull-right pr-4">
            <input type="text" id="search_input" placeholder="Search By Type.....">
        </div>
        <br><br>
        <table class="table table-striped table-bordered" id="code_table">
            <thead>
                <tr>
                    <th>ID</th>
					<th>Folder name</th>
                    <th>Platform name</th>
                    <th>Website</th>
                    <th>Title</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Solution</th>
                    <th>User Name</th>
                    <th>Supplier Name</th>
                    <th>Created At</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @include('code-shortcut.partials.list-code')
            </tbody>
        </table>
        
        <!-- Add pagination links -->
        <div class="text-center">
            {!! $codeshortcut->appends(Request::except('page'))->links() !!}
        </div>
    </div>
</div>

<!-- Modal -->
     <!-- Platform Modal content-->
	


<div class="modal fade" id="edit_code_shortcut" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Edit Code Shortcut</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" enctype="multipart/form-data" id="edit_code_shortcut_from">
				@csrf
				@method('put')

				<div class="modal-body">

					<div class="col-sm-12">
						<div class="form-group">
							<label>Supplier</label>
							<select name="supplier" id="supplier" class="form-control code">
								<option value="0">Selet Supplier</option>
								@foreach($suppliers as $supplier)
								<option value="{{$supplier->id}}">{{$supplier->supplier}}</option>
								@endforeach
							</select>

						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Platform</label>
							<select name="platform_id" class="form-control code" id="platform_id">
								<option value="0">Selet Platform</option>
								@foreach($platforms as $platform)
								<option value="{{$platform->id}}">{{$platform->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Folder Name</label>
							<select name="folder_id" class="form-control code" id="folder_id">
								<option value="0">Selet Platform</option>
								@foreach($folders as $folder)
								<option value="{{$folder->id}}">{{$folder->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
						<label>filename</label>
						<input type="file" name="notesfile" id="shortnotefileInput">
						<img src="" alt="Existing Image" height='50' width="50" id="filename">	
					</div>		
				</div>			
					<div class="col-sm-12">
						<div class="form-group">
							<label>Code</label>
							{{ 
								html()->text('code')
									->id('code')
									->class('form-control code')
									->required()
									->value(old('code'))
							}}
													</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Title</label>
							{{ 
								html()->text('title')
									->id('codetitle')
									->class('form-control title')
									->required()
									->value(old('title'))
							}}
													</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Solution</label>
							{{ 
								html()->text('solution')
									->id('solution')
									->class('form-control solution')
									->required()
									->value(old('solution'))
							}}
													</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Description</label>
							{{ 
								html()->text('description')
									->id('description')
									->class('form-control description')
									->required()
									->value(old('description'))
							}}
													</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>
{{-- @include('code-shortcut.partials.short-cut-notes-create') --}}


<script type="text/javascript">
	function submitSearch() {
		src = '{{route("code-shortcuts")}}'
		term = $('#term').val()
		id = $('#supplier-select').val()
		var platformIds = $('#platform-select').val();
		var websites = $('#website_select').val();
		createdAt = $('#createdAt-select').val()
	
		codeTitle = $('#code_title').val()
		$.ajax({
			url: src,
			dataType: "json",
			data: {
				term: term,
				id: id,
				platformIds: platformIds,
				codeTitle:codeTitle,
				createdAt:createdAt,
				websites:websites

			},
			beforeSend: function() {
				$("#loading-image").show();
			},

		}).done(function(data) {
			$("#loading-image").hide();
			$("#code_table tbody").empty().html(data.tbody);

		}).fail(function(jqXHR, ajaxOptions, thrownError) {
			alert('No response from server');
		});

	}

	function resetSearch() {
		src = '{{route("code-shortcuts")}}'
		blank = ''
		$.ajax({
			url: src,
			dataType: "json",
			data: {

				blank: blank,

			},
			beforeSend: function() {
				$("#loading-image").show();
			},

		}).done(function(data) {
			$("#loading-image").hide();
			$('#term').val('')
			$('#supplier-select').val('')
			$("#code_table tbody").empty().html(data.tbody);

		}).fail(function(jqXHR, ajaxOptions, thrownError) {
			alert('No response from server');
		});
	}

	$(document).on("keydown", "#search_input", function() {
		var query = $(this).val().toLowerCase();
			$("#code_table tr").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(query) > -1)
		});
	});

	$(document).on('click', '.show-full-log-text', function() {
        var fullLog = $(this).data('full-log');
		showFullLogModal();
        // $('#show_full_log_modal').modal('show');
        $('#show_full_log_modal_content').html(fullLog);
    });

</script>

<script>
	$(document).ready(function() {
		$('.edit_modal').on('click', function() {
			var id = $(this).attr("data-id")
			var url = '{{route("code-shortcuts.update",0)}}'
			url = url.replace("/0/", "/" + id + "/")
			$("#edit_code_shortcut_from").attr('action', url)
			$('#code').val($(this).attr("data-code"));
			$('#description').val($(this).attr("data-des"));
			$('#supplier').val($(this).attr("data-supplier"));
			$('#codetitle').val($(this).attr("data-title"));
			$('#solution').val($(this).attr("data-solution"));
			$('#platform_id').val($(this).attr("data-platformId"));
			$('#folder_id').val($(this).attr("data-folderId"));
			var imageUrl = $(this).attr("data-shortcutfilename"); 
			var image = "./codeshortcut-image/" + imageUrl; 
			$('#filename').attr('src', image);
			$('#edit_code_shortcut').modal('show');
		})
	});

	$(document).on("click", ".update-userpermission", function(e) {
		e.preventDefault();
		var $this = $(this);
		var id = $this.data('id');
		var per_folder_name = $('#per_folder_name').val();
		var per_user_name = $('#per_user_name').val();
		if (per_folder_name && per_user_name) {
		$.ajax({
			url: "{{route('folder.permission')}}",
			type: "post",
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
			per_folder_name: per_folder_name,
			per_user_name: per_user_name
			}

		}).done(function(response) {
			$('#loading-image').hide();
			if (response.code = '200') {
			toastr['success'](response.message, 'success');
			location.reload();
			} else {
			toastr['error'](response.message, 'error');
			}
		}).fail(function(errObj) {
			$('#loading-image').hide();
			toastr['error'](errObj.message, 'error');
		});
		} else {
		if (per_folder_name == '')
			$('#per_folder_name').addClass("alert alert-danger");
		if (per_user_name == '')
			$('#select2-per_user_name-container').addClass("alert alert-danger");
		setTimeout(function() {
			$('#per_folder_name').removeClass("alert alert-danger");
			$('#select2-per_user_name-container').removeClass("alert alert-danger");
		}, 1000);
		toastr['error']("Please Select Required fileds", 'error');
		}
  	});
</script>

@endsection