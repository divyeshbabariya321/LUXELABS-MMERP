@extends('layouts.app')



@section('title', $title)

@section('styles')

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
        
        .disabled{
            pointer-events: none;
            background: #bababa;
        }
        .glyphicon-refresh-animate {
            -animation: spin .7s infinite linear;
            -webkit-animation: spin2 .7s infinite linear;
        }

        @-webkit-keyframes spin2 {
            from { -webkit-transform: rotate(0deg);}
            to { -webkit-transform: rotate(360deg);}
        }

        @keyframes spin {
            from { transform: scale(1) rotate(0deg);}
            to { transform: scale(1) rotate(360deg);}
        }
        table.dataTable{
          margin:0;

        }
        .dataTables_scrollHeadInner,table.dataTable{
            width: 100% !important;
        }
        #gtmetrix-report-modal .modal-body {
    height: calc(100vh - 50vh);
    overflow-x: auto;
}
    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="table-responsive mt-3 pr-2 pl-2">
        <div class="gtmetrix_table_data">
            <table class="table table-bordered " id="gtmetrix_table">
                <thead>
                    <tr>
                        <th width="5%"> Id </th>
                        <th width="15%"> Website URL </th>
                        <th width="15%"> Test ID </th>
                        <th width="5%"> Action </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="gtmetrix-report-modal" role="dialog">
    <div class="modal-dialog modal-lg model-width">
      <!-- Modal content-->
        <div class="modal-content message-modal" style="width: 100%;">
            
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).on('click', '#searchReset', function(e) {
            //alert('success');
            $('#dateform').trigger("reset");
            e.preventDefault();
            oTable.draw();
        });

        $('#dateform').on('submit', function(e) {
            e.preventDefault();
            oTable.draw();

            return false;
        });

        $('#extraSearch').on('click', function(e) {
            e.preventDefault();
            oTable.draw();
        }); 

        // START Print Table Using datatable
        var oTable;
        $(document).ready(function() {
            oTable = $('#gtmetrix_table').DataTable({
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                sScrollX: true,
                order: [
                    [0, 'desc']
                ],
                targets: 'no-sort',
                bSort: false,

                oLanguage: {
                    sLengthMenu: "Show _MENU_",
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).attr('role', 'row');
                    $(row).find("td").last().addClass('text-danger');
                },
                ajax: {
                    "url": "{{ route('gtmetrix.Report.list') }}",
                },
                
                columnDefs: [{
                    targets: [],
                    orderable: false,
                    searchable: false,
                    
                }],
                columns: [{
                        data: null,
                        render: function (data, type, full, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'website_url',
                        name: 'store_views_gt_metrix.website_url',
                        render: function(data, type, row, meta) {
                            return  data;
                        }
                    },
                    {
                        data: 'test_id',
                        name: 'store_views_gt_metrix.test_id',
                        render: function(data, type, row, meta) {
                            return  data;
                        }
                    },
                    {
                        data: 'id',
                        name: 'store_gt_metrix_categories_value.id',
                        render: function(data, type, row, meta) {
                            var show_data = actionShowButtonWithClass('show-gtmetrix-report-details', row['id']);
                            return `<div class="flex justify-left items-center"> ${show_data}  </div>`;
                        }
                    },
                ],
            });
        });
        // END Print Table Using datatable
        
        $(document).on('click', '.show-gtmetrix-report-details', function(e){
            e.preventDefault();
            var id = $(this).data("id");
            $('.message-modal').html(''); 
            $('#loading-image').show();     
            $.ajax({
                url: '{{ route('gtmetrix.single.report') }}',
                type: 'POST',
                dataType: 'html',
                data:{
                    id: id,
                    _token: '{{ csrf_token() }}',
                },
            })
            .done(function(data){
                $('.message-modal').html('');    
                $('.message-modal').html(data); // load response 
                $("#gtmetrix-report-modal").modal("show");
                $('#loading-image').hide();        // hide ajax loader 
            })
            .fail(function(){
                toastr["error"]("Something went wrong please check log file");
                $('#loading-image').hide();
            });
        });
    </script>

@endsection