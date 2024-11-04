<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {

        // Datatable 
        const $datatableData = $('#seoTable').DataTable({
            serverSide:true,
            processing:true,
            lengthMenu: [ [50, 100, 150, -1], [50, 100, 150, "All"] ],
            ajax:{
                url:'',
                data:{
                    companyTypeId:() => $(document).find(".typeSelect option:selected").val(),
                    websiteId:() => $(document).find(".websiteFilter option:selected").val(),
                    userId:() => $(document).find(".userFilter option:selected").val(),
                    status:() => $(document).find(".statusFilter option:selected").val(),
                }
            },
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (aData[12] != '') {
                    $('td', nRow).css('background-color', aData.status_color);
                } 
            },
            columns:[
                @if(!empty($dynamicColumnsToShowsc))
                    @if (!in_array('#', $dynamicColumnsToShowsc))
                        { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    @else
                        { data: 'DT_RowIndex', 'orderable': false, 'searchable': false, 'visible': false },
                    @endif

                    @if (!in_array('Type', $dynamicColumnsToShowsc))
                        { data: 'company', name:'company' },
                    @else
                        { data: 'company', name:'company', 'visible': false },
                    @endif

                    @if (!in_array('Website', $dynamicColumnsToShowsc))
                        {data:'website.website', name:'website.website'},
                    @else
                        {data:'website.website', name:'website.website', 'visible': false},
                    @endif

                    @if (!in_array('DA', $dynamicColumnsToShowsc))
                        {data:'da', name:'da'},
                    @else
                        {data:'da', name:'da', 'visible': false},
                    @endif

                    @if (!in_array('PA', $dynamicColumnsToShowsc))
                        {data:'pa', name:'pa'},
                    @else
                        {data:'pa', name:'pa', 'visible': false},
                    @endif

                    @if (!in_array('SS', $dynamicColumnsToShowsc))
                        {data:'ss', name:'ss'},
                    @else
                        {data:'ss', name:'ss', 'visible': false},
                    @endif

                    @if (!in_array('User', $dynamicColumnsToShowsc))
                        {data:'user.name', name:'user.name'},
                    @else
                        {data:'user.name', name:'user.name', 'visible': false},
                    @endif

                    @if (!in_array('Username', $dynamicColumnsToShowsc))
                        {data:'username', name:'username'},
                    @else
                        {data:'username', name:'username', 'visible': false},
                    @endif

                    @if (!in_array('Password', $dynamicColumnsToShowsc))
                        {data:'password', name:'password'},
                    @else
                        {data:'password', name:'password', 'visible': false},
                    @endif

                    @if (!in_array('Live link', $dynamicColumnsToShowsc))
                        {data:'liveLink', name:'liveLink'},
                    @else
                        {data:'liveLink', name:'liveLink', 'visible': false},
                    @endif

                    @if (!in_array('Date', $dynamicColumnsToShowsc))
                        {data:'created_at', name:'created_at'},
                    @else
                        {data:'created_at', name:'created_at', 'visible': false},
                    @endif

                    @if (!in_array('Status', $dynamicColumnsToShowsc))
                        {data:'status', name:'status'},
                    @else
                        {data:'status', name:'status', 'visible': false},
                    @endif

                    @if (!in_array('Action', $dynamicColumnsToShowsc))
                        {data:'actions', name:'actions'},
                    @else
                        {data:'actions', name:'actions', 'visible': false},
                    @endif
                    {data:'status_color', name:'status_color', 'visible': false},
                @else
                    { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    { data: 'company', name:'company' },
                    {data:'website.website', name:'website.website'},
                    {data:'da', name:'da'},
                    {data:'pa', name:'pa'},
                    {data:'ss', name:'ss'},
                    {data:'user.name', name:'user.name'},
                    {data:'username', name:'username'},
                    {data:'password', name:'password'},
                    {data:'liveLink', name:'liveLink'},
                    {data:'created_at', name:'created_at'},
                    {data:'status', name:'status'},
                    {data:'actions', name:'actions'},
                    {data:'status_color', name:'status_color', 'visible': false},
                @endif
            ]
        });

        // Histroy Table
        $(function() {
            let $historyTable = null;
            $(document).on('click', '.historyBtn', function() {
                let companyId = $(this).attr('data-id');
                let $historyModal = $(document).find('#historyModal');
                $($historyModal).modal('show')
                
                $historyTable = $($historyModal).find('#historyTable').DataTable({
                    serverSide:true,
                    processing:true,
                    lengthMenu: [ [50, 100, 150, -1], [50, 100, 150, "All"] ],
                    ajax:{
                        url:'',
                        data:{
                            companyId:companyId,
                            type:"COMPANY_HISTORY",
                        },
                    },
                    columns:[
                        { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                        { data: 'company', name:'company' },
                        {data:'website.website', name:'website.website'},
                        {data:'da', name:'da'},
                        {data:'pa', name:'pa'},
                        {data:'ss', name:'ss'},
                        {data:'username', name:'username'},
                        {data:'password', name:'password'},
                        {data:'liveLink', name:'liveLink'},
                        {data:'created_at', name:'created_at'},
                        {data:'status', name:'status'},
                    ]
                })
            })
    
            $(document).on('hide.bs.modal', '#historyModal', function() {
                $historyTable.destroy();
                $historyTable = null;
            })
        })

        // Form popup 
        $(function() {

            let $formModal = $(document).find('#companyFormModal');
            $(document).on('click', '.editBtn, .addNewBtn', function() {
                let url = $(this).attr('data-url');
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {

                    },
                    dataType: "json",
                    success: function (response) {
                        $($formModal).find('.modal-body').html(response.data);
                        $($formModal).find('.modal-title').text(response.title);
                        $($formModal).modal('show');
                    }
                });
            })

            $(document).on('click', '.saveFormBtn', function() {
                let $form = $(document).find('#companyForm');
                if(!$form.valid()) {
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    dataType: "json",
                    success: function (response) {
                        $($formModal).modal('hide');
                        
                        $datatableData.clear().draw();
                    }
                });
            });

            $($formModal).find('hide.bs.modal', function() {
                $($formModal).find('.modal-body').html('');
            });
        })

        $(document).on('click', '.searchBtn', function() {
            $datatableData.clear().draw();
        });

        // Type Module
        $(function() {
            let $typeModal = $(document).find('#typeModal');
            let $typeFormModal = $(document).find('#typeFormModal');
            let $typeTable = null;

            $(document).on('click', '.typeModuleBtn', function() {
                $($typeModal).modal('show');
                $typeTable = $(document).find('#typeTable').DataTable({
                    serverSide:true,
                    processing:true,
                    lengthMenu: [ [50, 100, 150, -1], [50, 100, 150, "All"] ],
                    ajax:{
                        url:`{{ route('seo.company-type.index') }}`
                    },
                    columns:[
                        { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                        { data: 'name', name:'name' },
                        {data:'actions', name:'actions'},
                    ]
                });
            })

            $($typeModal).on('hide.bs.modal', function() {
                $typeTable.destroy();
            });

            $(document).on('click', '.addNewTypeBtn, .typeEditBtn', function() {
                let url = $(this).attr('data-url');
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {},
                    dataType: "json",
                    success: function (response) {
                        $($typeFormModal).modal('show');
                        $($typeFormModal).find('.modal-title').html(response.title);
                        $($typeFormModal).find('.modal-body').html(response.data);
                    }
                });
            });

            $(document).on('click', '.typeDeleteBtn', function() {
                let isValid = confirm("are you sure want to delete?");
                if(isValid) {
                    let url = $(this).attr('data-url');
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            _token:`{{ csrf_token() }}`
                        },
                        dataType: "json",
                        success: function (response) {
                            $typeTable.clear().draw();
                        }
                    });
                }
            });

            $(document).on('click', '#typeFormModal .saveBtn', function() {
                let $form = $(document).find("#typeFormModal #companyForm");
                if(!$form.valid()) {
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    dataType: "json",
                    success: function (response) {
                        $($typeFormModal).modal('hide');
                        $($typeFormModal).find('.modal-body').html('');
                        $typeTable.clear().draw();
                    }
                });
            })
        })

    });

</script>