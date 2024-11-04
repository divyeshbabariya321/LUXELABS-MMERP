@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Database | Query Process List')

@section('content')

<div class="row">
	<div class="col-lg-12 margin-tb">
	    <h2 class="page-heading">Database | Query Process List <span id="process_count"></span>
</h2>
	</div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="row">
            <div class="col-md-8 pl-5">
                <h4 class="header-title mb-3">
                    Query Process List <a href="javascript:;" id="refresh-process-list"><span class="glyphicon glyphicon-refresh"></span></a>
                </h4>
            </div>
            <div class="col-md-4 pr-5">
                <form class="form-inline pull-right">
                    <!-- <div class="form-group mr-2">
                        <input type="text" class="form-control" id="databaseName" placeholder="Enter database name">
                    </div> -->
                    <div id="progress" style="display: none;">
                    <img src="/images/loading_new.gif" style="cursor: pointer; width: 30px;">
                    </div>
                    <button type="button" class="btn btn-secondary mr-2" onclick="commandLogs()">Command Logs</button>
                    <button type="button" class="btn btn-secondary" onclick="dbExport()">Export Database</button>
                </form>

            </div>
        </div>

      <div class="card-box">
        <div class="table-responsive table-process-list-disp">
        </div>
      </div>
  </div>
</div>
@include("database.partial.template")

<div id="command-logs-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <div>
                  <h4 class="modal-title"><b>Database command logs</b></h4>
              </div>
              <button type="button" class="close" data-dismiss="modal">×</button>
          </div>

          <div class="modal-body">
              <div class="row">
                  <div class="col-lg-12">
                      <div class="row">
                          <div class="col-12" id="command-logs-modal-html">

                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>

<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
</div>
<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }} "></script>
<script type="text/javascript">

  var showProcessList = function() {
      $.ajax({
          type: 'GET',
          url: "<?php echo route('database.process.list'); ?>",
          dataType:"json"
        }).done(function(response) {
          if(response.code == 200) {
            var count = response.records.length;
            var tpl = $.templates("#template-list-state-process-list");
            var tplHtml       = tpl.render(response);
                $(".table-process-list-disp").html(tplHtml);
                $("#process_count").html(" ("+count+")");
          }
        }).fail(function(response) {
          console.log("Sorry, something went wrong");
        });
  };

  showProcessList();

  $(document).on("click","#refresh-process-list",function() {
      showProcessList();
  });

  $(document).on("click",".kill-process",function(){
        var $this = $(this);
        $.ajax({
          type: 'GET',
          url: "<?php echo route('database.process.kill'); ?>",
          data : {id : $this.data("id")},
          dataType:"json"
        }).done(function(response) {
          if(response.code == 200) {
            showProcessList();
          }
        }).fail(function(response) {
          console.log("Sorry, something went wrong");
        });
  });

  $(document).on('click', '.expand-row', function () {
      var selection = window.getSelection();
      if (selection.toString().length === 0) {
          $(this).find('.td-mini-container').toggleClass('hidden');
          $(this).find('.td-full-container').toggleClass('hidden');
      }
  });

  function dbExport() {
      // let dbName = $('#databaseName').val();
      // if(dbName === '') {
      //     toastr['error']('Please enter a valid database name!', 'error');
      //     return false;
      // }

      if (confirm("Are you sure you want to run this command?")) {
          $('#progress').show();
          $.ajax({
              url: "{{ route('database.export') }}",
              type: "post",
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data: {
                 // db_name: dbName
              }
          }).done(function (response) {
              $('#progress').hide();
              if (response.code == '200') {
                  window.open(response.data, "_blank");
                  toastr['success'](response.message, 'success');
              } else {
                  toastr['error'](response.message, 'error');
              }
          }).fail(function (errObj) {
              toastr['error'](errObj.message, 'error');
          });
      } else {
          return false;
      }
  }

  function commandLogs(pageNumber = 1) {
      $.ajax({
          url: '{{ route('database.command-logs') }}',
          dataType: "json",
          data: {
              page:pageNumber,
          },
          beforeSend: function() {
          $("#loading-image-preview").show();
      }
      }).done(function(response) {
          $('#command-logs-modal-html').empty().html(response.html);
          $('#command-logs-modal').modal('show');
          renderdomainPagination(response.data);
          $("#loading-image-preview").hide();
      }).fail(function(response) {
          $('.loading-image-preview').show();
          console.log(response);
      });
  }

  function renderdomainPagination(response) {
    var paginationContainer = $(".pagination-container-db-command");
    var currentPage = response.current_page;
    var totalPages = response.last_page;
    var html = "";
    var maxVisiblePages = 10;

    if (totalPages > 1) {
        html += "<ul class='pagination'>";
        if (currentPage > 1) {
        html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeCommandLogsPage(" + (currentPage - 1) + ")'>Previous</a></li>";
        }
        var startPage = 1;
        var endPage = totalPages;

        if (totalPages > maxVisiblePages) {
        if (currentPage <= Math.ceil(maxVisiblePages / 2)) {
            endPage = maxVisiblePages;
        } else if (currentPage >= totalPages - Math.floor(maxVisiblePages / 2)) {
            startPage = totalPages - maxVisiblePages + 1;
        } else {
            startPage = currentPage - Math.floor(maxVisiblePages / 2);
            endPage = currentPage + Math.ceil(maxVisiblePages / 2) - 1;
        }

        if (startPage > 1) {
            html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeCommandLogsPage(1)'>1</a></li>";
            if (startPage > 2) {
            html += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
        }
        }

        for (var i = startPage; i <= endPage; i++) {
        html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changeCommandLogsPage(" + i + ")'>" + i + "</a></li>";
        }
        html += "</ul>";
    }
    paginationContainer.html(html);
  }

  function changeCommandLogsPage(pageNumber) {
    commandLogs(pageNumber);
  }

</script>
@endsection