@extends(config('dotenveditor.template', 'dotenv-editor::master'))

{{--
Feel free to extend your custom wrapping view.
All needed files are included within this file, so nothing could break if you extend your own master view.
--}}

@section('content')
<div id="app">

  <div class="container">
   <!-- <h1><a href="{{ url(config('dotenveditor.route.prefix')) }}">{{ trans('dotenv-editor::views.title') }}</a></h1>-->

        <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Env Manager</h2>
                      <input id="env-search-input" type="text" placeholder="Search..">
                  <div style="display: flex; font-weight: 500;"><div>Total:-</div><div id="total">@{{ entries.length > 0 ? entries.length : '' }}</div></div>
                  @if(auth()->user()->isAdmin() || auth()->user()->isEnvManager())
                    <button type="button" id="add-new" class="btn btn-primary float-right">
                        Add New
                    </button>
                    @endif
                </div>
            </div>
        </div>

    </div>
    
  <!--  <div class="row">
      <div class="col-md-12">
        <ul class="nav nav-tabs">
          <li v-for="view in views" role="presentation" class="@{{ view.active ? 'active' : '' }}">
            <a href="javascript:;" @click="setActiveView(view.name)">@{{ view.name }}</a>
          </li>
        </ul>
      </div>
    </div>-->

    <br><br>

    <div class="row">

      <div class="col-md-12 col-sm-12">

        {{-- Error-Container --}}
        <div>
          {{-- VueJS-Errors --}}
          <div class="alert alert-success" role="alert" v-show="alertsuccess">
            <button type="button" class="close" @click="closeAlert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            @{{ alertmessage }}
          </div>
          {{-- Errors from POST-Requests --}}
          @if(session('dotenv'))
          <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" aria-label="Close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            {{ session('dotenv') }}
          </div>
          @endif
        </div>

        {{-- Overview --}}
        <div v-show="views[0].active">

          <div class="panel panel-default">
    <!--        <div class="panel-heading">
              <h2 class="panel-title">
                {{ trans('dotenv-editor::views.overview_title') }}
              </h2>
            </div>-->
      <!--      <div class="panel-body">
              <p>
                {!! trans('dotenv-editor::views.overview_text') !!}
              </p>
              <p>
                <a href="javascript:;" class="btn btn-primary hide" @click="loadEnv">
                  {{ trans('dotenv-editor::views.overview_button') }}
                </a>
              </p>
            </div>-->
            
              <a href="javascript:;" class="btn btn-primary hide loadEnvButton" id="loadEnvButton" @click="loadEnv">
                  {{ trans('dotenv-editor::views.overview_button') }}
              </a>
            <div class="table-responsive" v-show="!loadButton">
            
              <table class="table table-striped" id="env-table">
                <tr>
                <th>Sr No.</th>
                  <th>{{ trans('dotenv-editor::views.overview_table_key') }}</th>
                  <th>{{ trans('dotenv-editor::views.overview_table_value') }}</th>
                  <th style="width: 20%">Description</th>
                  <th>{{ trans('dotenv-editor::views.overview_table_options') }}</th>
                </tr>
                <tr v-for="(index,entry) in entries">
                  <td>@{{ index+1 }}</td>
                  <td style="word-wrap: anywhere;">@{{ entry.key }}</td>
                  <td style="word-wrap: anywhere;">@{{ entry.value }}</td>
                  <td style="word-wrap: anywhere;">@{{ entry.description }}</td>

                  <td>
                    @if(auth()->user()->isAdmin() || auth()->user()->isEnvManager())

                    <a href="javascript:;" @click="editEntry(entry)"
                    title="{{ trans('dotenv-editor::views.overview_table_popover_edit') }}">
                    <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                  </a>
                  <a href="javascript:;" @click="modal(entry)"
                  title="{{ trans('dotenv-editor::views.overview_table_popover_delete') }}">
                  <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                </a>
                    @endif

                    <div @click="copyData(entry)" style="cursor: pointer">Copy</div>
              </td>
            </tr>
          </table>
        </div>
      </div>

      {{-- Modal delete --}}
      <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
            <h4 class="modal-title">@{{ deleteModal.title }}</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
              <p>{!! trans('dotenv-editor::views.overview_delete_modal_text') !!}</p>
              <p class="text text-warning">
                <strong>@{{ deleteModal.content }}</strong>
              </p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">
                {!! trans('dotenv-editor::views.overview_delete_modal_no') !!}
              </button>
              <button type="button" class="btn btn-danger" @click="deleteEntry">
                {!! trans('dotenv-editor::views.overview_delete_modal_yes') !!}
              </button>
            </div>
          </div>
        </div>
      </div>

      {{-- Modal edit --}}
      <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">{!! trans('dotenv-editor::views.overview_edit_modal_title') !!}</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
              <strong>{!! trans('dotenv-editor::views.overview_edit_modal_key') !!}:</strong> @{{ toEdit.key }}<br><br>
              <div class="form-group">
                <label for="editvalue">{!! trans('dotenv-editor::views.overview_edit_modal_value') !!}</label>
                <input type="text" v-model="toEdit.value" id="editvalue" class="form-control">
              </div>
              <div class="form-group">
                <label for="editdescription">New Description</label>
                <input type="text" v-model="toEdit.description" id="editdescription" class="form-control">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">
                {!! trans('dotenv-editor::views.overview_edit_modal_quit') !!}
              </button>
              <button type="button" class="btn btn-primary" @click="updateEntry">
                {!! trans('dotenv-editor::views.overview_edit_modal_save') !!}
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>

        <div class="modal fade" id="add-new-modal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add New</h5>
                    <button type="button" id="close_add_new" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                  <div>
          <div class="panel panel-default">
            <!--<div class="panel-heading">
              <h2 class="panel-title">{!! __('dotenv-editor::views.addnew_title') !!}</h2>
            </div>-->

            <div class="panel-body">
              <p>
                Here you can add a new key-value-pair to your current .env-file.
              </p>

              <form @submit.prevent="addNew()">
                <div class="form-group">
                  <label for="newkey">{!! __('dotenv-editor::views.addnew_label_key') !!}</label>
                  <input type="text" name="newkey" id="newkey" v-model="newEntry.key" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="newvalue">{!! __('dotenv-editor::views.addnew_label_value') !!}</label>
                  <input type="text" name="newvalue" id="newvalue" v-model="newEntry.value" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="newkey">Description</label>
                  <input type="text" name="description" id="description" v-model="newEntry.description" class="form-control" required>
                </div>
                @if(config('app.env') === 'production')
                <input type="checkbox" id="add-to-live" name="add-to-live" value="1" style="height: 12px !important;">
                <label for="add-to-live">Add into Staging .env to</label><br>
                @elseif(config('app.env') === 'staging')
              <input type="checkbox" id="add-to-live" name="add-to-live" value="1" style="height: 12px !important;">
              <label for="add-to-live">Add into Production .env to</label><br>
                  @endif
                <div>

                <button class="btn btn-default custom-close-modal" type="submit">
                  {!! __('dotenv-editor::views.addnew_button_add') !!}
                </button>
              </form>
            </div>
          </div>
        </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Add new --}}
    <div v-show="views[1].active">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h2 class="panel-title">{!! trans('dotenv-editor::views.addnew_title') !!}</h2>
        </div>
        <div class="panel-body">
          <p>
            {!! trans('dotenv-editor::views.addnew_text') !!}
          </p>

          <form @submit.prevent="addNew()">
            <div class="form-group">
              <label for="newkey">{!! trans('dotenv-editor::views.addnew_label_key') !!}</label>
              <input type="text" name="newkey" id="newkey" v-model="newEntry.key" class="form-control">
            </div>
            <div class="form-group">
              <label for="newvalue">{!! trans('dotenv-editor::views.addnew_label_value') !!}</label>
              <input type="text" name="newvalue" id="newvalue" v-model="newEntry.value" class="form-control">
            </div>
            <button class="btn btn-default" type="submit">
              {!! trans('dotenv-editor::views.addnew_button_add') !!}
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- Backups --}}
    <div v-show="views[2].active">
      {{-- Create Backup --}}
      <div class="panel panel-default">
        <div class="panel-heading">
          <h2 class="panel-title">{!! trans('dotenv-editor::views.backup_title_one') !!}</h2>
        </div>
        <div class="panel-body">
          <a href="{{ url(config('dotenveditor.route.prefix') . "/createbackup") }}" class="btn btn-primary">
            {!! trans('dotenv-editor::views.backup_create') !!}
          </a>
          <a href="{{ url(config('dotenveditor.route.prefix') . "/download") }}" class="btn btn-primary">
            {!! trans('dotenv-editor::views.backup_download') !!}
          </a>
        </div>
      </div>

      {{-- List of available Backups --}}
      <div class="panel panel-default">
        <div class="panel-heading">
          <h2 class="panel-title">{!! trans('dotenv-editor::views.backup_title_two') !!}</h2>
        </div>
        <div class="panel-body">
          <p>
            {!! trans('dotenv-editor::views.backup_restore_text') !!}
          </p>
          <p class="text-danger">
            {!! trans('dotenv-editor::views.backup_restore_warning') !!}
          </p>
          @if(!$backups)
          <p class="text text-info">
            {!! trans('dotenv-editor::views.backup_no_backups') !!}
          </p>
          @endif
        </div>
        @if($backups)
        <div class="table-responsive">
          <table class="table table-striped">
            <tr>
              <th>{!! trans('dotenv-editor::views.backup_table_nr') !!}</th>
              <th>{!! trans('dotenv-editor::views.backup_table_date') !!}</th>
              <th>{!! trans('dotenv-editor::views.backup_table_options') !!}</th>
            </tr>
            <?php $c = 1;?>
            @foreach($backups as $backup)

            <tr>
              <td>{{ $c++ }}</td>
              <td>{{ $backup['formatted'] }}</td>
              <td>
                <a href="javascript:;" @click="showBackupDetails('{{ $backup['unformatted'] }}', '{{ $backup['formatted'] }}')" title="{!! trans('dotenv-editor::views.backup_table_options_show') !!}">
                  <span class="glyphicon glyphicon-zoom-in"></span>
                </a>
                <a href="javascript:;" @click="restoreBackup({{ $backup['unformatted'] }})"
                title="{!! trans('dotenv-editor::views.backup_table_options_restore') !!}"
                >
                <span class="glyphicon glyphicon-refresh" title="{!! trans('dotenv-editor::views.backup_table_options_restore') !!}"></span>
              </a>
              <a href="{{ url(config('dotenveditor.route.prefix') . "/download/" . $backup['unformatted']) }}">
                <span class="glyphicon glyphicon-download" title="{!! trans('dotenv-editor::views.backup_table_options_download') !!}"></span>
              </a>
              <a href="{{ url(config('dotenveditor.route.prefix') . "/deletebackup/" . $backup["unformatted"]) }}" title="{!! trans('dotenv-editor::views.backup_table_options_delete') !!}">
                <span class="glyphicon glyphicon-trash"></span>
              </a>
            </td>
          </tr>
          @endforeach
        </table>
      </div>
      @endif
    </div>

    @if($backups)
    {{-- Details Modal --}}
    <div class="modal fade" id="showDetails" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{!! trans('dotenv-editor::views.backup_modal_title') !!}</h4>
          </div>
          <div class="modal-body">
            <table class="table table-striped">
              <tr>
                <th>{!! trans('dotenv-editor::views.backup_modal_key') !!}</th>
                <th>{!! trans('dotenv-editor::views.backup_modal_value') !!}</th>
              </tr>
              <tr v-for="entry in details">
                <td>@{{ entry.key }}</td>
                <td>@{{ entry.value }}</td>
                <td>@{{ entry.description }}</td>

              </tr>
            </table>
          </div>
          <div class="modal-footer">
            <a href="javascript:;" @click="restoreBackup(currentBackup.timestamp)"
            title="Stelle dieses Backup wieder her"
            class="btn btn-primary"
            >
            {!! trans('dotenv-editor::views.backup_modal_restore') !!}
          </a>

          <button type="button" class="btn btn-default" data-dismiss="modal">{!! trans('dotenv-editor::views.backup_modal_close') !!}</button>

          <a href="{{ url(config('dotenveditor.route.prefix') . "/deletebackup/" . $backup["unformatted"]) }}" class="btn btn-danger">
            {!! trans('dotenv-editor::views.backup_modal_delete') !!}
          </a>
        </div>
      </div>
    </div>
  </div>
  @endif

</div>

{{-- Upload --}}
<div v-show="views[3].active">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h2 class="panel-title">{!! trans('dotenv-editor::views.upload_title') !!}</h2>
    </div>
    <div class="panel-body">
      <p>
        {!! trans('dotenv-editor::views.upload_text') !!}<br>
        <span class="text text-warning">
          {!! trans('dotenv-editor::views.upload_warning') !!}
        </span>
      </p>
      <form method="post" action="{{ url(config('dotenveditor.route.prefix') . "/upload") }}" enctype="multipart/form-data">
        <div class="form-group">
          <label for="backup">{!! trans('dotenv-editor::views.upload_label') !!}</label>
          <input type="file" name="backup">
        </div>
        <button type="submit" class="btn btn-primary" title="Ein Backup von deinem Computer hochladen">
          {!! trans('dotenv-editor::views.upload_button') !!}
        </button>
      </form>
    </div>
  </div>
</div>
</div>

</div>
</div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.26/vue.js"></script>
<script>
  new Vue({
    el: '#app',
    data: {
      loadButton: true,
      alertsuccess: 0,
      alertmessage: '',
      views: [
      {name: "{{ trans('dotenv-editor::views.overview') }}", active: 1},
      {name: "{{ trans('dotenv-editor::views.addnew') }}", active: 0},
      {name: "{{ trans('dotenv-editor::views.backups') }}", active: 0},
      {name: "{{ trans('dotenv-editor::views.upload') }}", active: 0}
      ],
      newEntry: {
        key: "",
        value: "",
        description: ""
      },
      details: {},
      currentBackup: {
        timestamp: ''
      },
      toEdit: {},
      toDelete: {},
      deleteModal: {
        title: '',
        content: ''
      },
      token: "{!! csrf_token() !!}",
      entries: [

      ]
    },
    methods: {
      loadEnv: function(){
        var vm = this;
        var envDescription = [];
        this.loadButton = false;
        $.ajax({
          url: "/get-env-description",
          type: "get",
          success: function(response){
            envDescription = response;
            //window.location.reload();
          },
          error: function (request, status, error) {
            console.log('ERROR: ',error);
          }
        })
        $.getJSON("/{{ $url }}/getdetails", function(items){
          for (let i = 0; i < items.length; i++) {
            const name = items[i].key;
            const matchingObj = envDescription.find(obj => obj.key === name);
            if (matchingObj) {
              items[i].description = matchingObj.description;
            }
          }
          console.log(items)
          vm.entries = items;
        });
      },
      setActiveView: function(viewName){
        $.each(this.views, function(key, value){
          if(value.name == viewName){
            value.active = 1;
          } else {
            value.active = 0;
          }
        })
      },
      addNew: function(){
        var vm = this;
        var newkey = this.newEntry.key;
        var newvalue = this.newEntry.value;
        var newDescription = this.newEntry.description;
        var checkedValue = $('#add-to-live:checked').val();

        $.ajax({
          url: "/api/add-env",
          type: "post",
          data: {
            _token: "{!! csrf_token() !!}",
            key: newkey,
            value: newvalue,
            description: newDescription,
            addToLive: checkedValue ? checkedValue : false
          },
          success: function(response){
            console.log(response);
            vm.entries.push({
              key: newkey,
              value: newvalue
            });
            $("#newkey").val("");
            vm.newEntry.key = "";
            vm.newEntry.value = "";
            vm.newEntry.description = "";
            $("#newvalue").val("");
            $("#description").val("");
            toastr["success"]("Key added successfully", "Message");
            $("#add-new-modal").modal("hide");
            
            //window.location.reload();
          },
          error: function (request, status, error) {
            console.log('ERROR: ',error);
          }
        })


      },
      editEntry: function(entry){
        this.toEdit = {};
        this.toEdit = entry;
        $('#editModal').modal('show');
      },
      updateEntry: function(){
        var vm = this;
        $.ajax({
          url: "/api/edit-env",
          type: "post",
          data: {
            _token: this.token,
            key: vm.toEdit.key,
            value: vm.toEdit.value,
            description: vm.toEdit.description,
            server: "{{config('app.env')}}"
          },
          success: function(){
            var msg = "{{ trans('dotenv-editor::views.entry_edited') }}";
            vm.showAlert("success", msg);
            $('#editModal').modal('hide');
          },
          error: function (request, status, error) {
              alert(request.responseText);
          }
        })
      },
      makeBackup: function(){
        var vm = this;
        $.ajax({
          url: "/{{ $url }}/createbackup",
          type: "get",
          success: function(){
            vm.showAlert('success', "{{ trans('dotenv-editor::views.backup_created') }}");
          },
          error: function (request, status, error) {
              alert(request.responseText);
          }
        })
      },
      showBackupDetails: function(timestamp, formattedtimestamp){
        this.currentBackup.timestamp = timestamp;
        var vm = this;
        $.getJSON("/{{ $url }}/getdetails/" + timestamp, function(items){
          vm.details = items;
          $('#showDetails').modal('show');
        });
      },
      restoreBackup: function(timestamp){
        var vm = this;
        $.ajax({
          url: "/{{ $url }}/restore/" + timestamp,
          type: "get",
          success: function(){
            vm.loadEnv();
            $('#showDetails').modal('hide');
            vm.setActiveView('overview');
            vm.showAlert('success', '{{ trans('dotenv-editor::views.backup_restored') }}');
          },
          error: function (request, status, error) {
              alert(request.responseText);
          }
        })
      },
      copyData: function(entry){
        let copyObj = entry.key +' : '+ entry.value
        navigator.clipboard.writeText(copyObj);
        toastr["success"]("Copy successfully", "Message");
      },
      deleteEntry: function(){
        var entry = this.toDelete;
        var vm = this;

        $.ajax({
          url: "/{{ $url }}/delete",
          type: "post",
          data: {
            _token: this.token,
            key: entry.key
          },
          success: function(){
            var msg = "{{ trans('dotenv-editor::views.entry_deleted') }}";
            vm.showAlert("success", msg);
          },
          error: function (request, status, error) {
              alert(request.responseText);
          }
        });
        this.entries.$remove(entry);
        this.toDelete = {};
        $('#deleteModal').modal('hide');
      },
      showAlert: function(type, message){
        this.alertmessage = message;
        this.alertsuccess = 1;
      },
      closeAlert: function(){
        this.alertsuccess = 0;
      },
      modal: function(entry){
        this.toDelete = entry;
        this.deleteModal.title = "{{ trans('dotenv-editor::views.delete_entry') }}";
        this.deleteModal.content = entry.key + "=" + entry.value;
        $('#deleteModal').modal('show');
      }
    }
  })
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script>

  $(document).ready(function(){
    $(function () {
      $('[data-toggle="popover"]').popover()
    });
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    
    $(window).on('load',function(){
      $('.loadEnvButton')[0].click();
    });
    $("#add-new").click(function(){
      $("#add-new-modal").modal("show");
    })
  })
  

  $("#env-search-input").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#env-table tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });

</script>

@endsection