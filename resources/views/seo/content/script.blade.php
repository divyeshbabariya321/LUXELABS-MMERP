<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        // Datatable 
        const $datatable = $('#seoProcessTbl').DataTable({
            serverSide: true
            , lengthMenu: [
                [50, 100, 150, -1]
                , [50, 100, 150, "All"]
            ]
            , searching: false
            , responsive: true
            , ajax: {
                url: ''
                , data: {
                    filter: {
                        website_id: () => $(document).find('.websiteFilter').val()
                        , price_status: () => $(document).find('.priceStatusFilter').val()
                        , user_id: () => $(document).find('.userFilter').val()
                        , status: () => $(document).find('.statusFilter').val()
                    , }
                }
            , },
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (aData[14] != '') {
                    $('td', nRow).css('background-color', aData.status_color);
                } 
            }
            , columns: [
                @if(!empty($dynamicColumnsToShowSeo))
                    @if (!in_array('#', $dynamicColumnsToShowSeo))
                        {data: 'DT_RowIndex', 'orderable': false, 'searchable': false},
                    @else
                        {data: 'DT_RowIndex', 'orderable': false, 'searchable': false, 'visible': false},
                    @endif

                    @if (!in_array('Website', $dynamicColumnsToShowSeo))
                        {data: 'website_id', name: 'website_id'},
                    @else
                        {data: 'website_id', name: 'website_id', 'visible': false},
                    @endif

                    @if (!in_array('Keywords', $dynamicColumnsToShowSeo))
                        {data: 'keywords', name: 'keywords'},
                    @else
                        {data: 'keywords', name: 'keywords', 'visible': false},
                    @endif

                    @if (!in_array('User', $dynamicColumnsToShowSeo))
                        {data: 'user_id', name: 'user_id'},
                    @else
                        {data: 'user_id', name: 'user_id', 'visible': false},
                    @endif

                    @if (!in_array('Price', $dynamicColumnsToShowSeo))
                        {data: 'price', name: 'price'},
                    @else
                        {data: 'price', name: 'price', 'visible': false},
                    @endif

                    @if (!in_array('Document Link', $dynamicColumnsToShowSeo))
                        {data: 'documentLink', name: 'documentLink'},
                    @else
                        {data: 'documentLink', name: 'documentLink', 'visible': false},
                    @endif

                    @if (!in_array('Word count', $dynamicColumnsToShowSeo))
                        {data: 'word_count', name: 'word_count'},
                    @else
                        {data: 'word_count', name: 'word_count', 'visible': false},
                    @endif

                    @if (!in_array('Suggestion', $dynamicColumnsToShowSeo))
                        {data: 'suggestion', name: 'suggestion'},
                    @else
                        {data: 'suggestion', name: 'suggestion', 'visible': false},
                    @endif

                    @if (!in_array('Status', $dynamicColumnsToShowSeo))
                        {data: 'status', name: 'status'},
                    @else
                        {data: 'status', name: 'status', 'visible': false},
                    @endif

                    @if (!in_array('SEO Checklist', $dynamicColumnsToShowSeo))
                        {data: 'seoChecklist', name: 'seoChecklist'},
                    @else
                        {data: 'seoChecklist', name: 'seoChecklist', 'visible': false},
                    @endif

                    @if (!in_array('Publish Checklist', $dynamicColumnsToShowSeo))
                        {data: 'publishChecklist', name: 'publishChecklist'},
                    @else
                        {data: 'publishChecklist', name: 'publishChecklist', 'visible': false},
                    @endif

                    @if (!in_array('Live Status Link', $dynamicColumnsToShowSeo))
                        {data: 'liveStatusLink', name: 'liveStatusLink'},
                    @else
                        {data: 'liveStatusLink', name: 'liveStatusLink', 'visible': false},
                    @endif

                    @if (!in_array('Publish Date', $dynamicColumnsToShowSeo))
                        {data: 'liveStatusLink', name: 'liveStatusLink'},
                    @else
                        {data: 'liveStatusLink', name: 'liveStatusLink', 'visible': false},
                    @endif

                    @if (!in_array('Actions', $dynamicColumnsToShowSeo))
                        {data: 'actions', name: 'actions'}
                    @else
                        {data: 'actions', name: 'actions', 'visible': false},
                    @endif
                    {data:'status_color', name:'status_color', 'visible': false}
                @else             
                    {data: 'DT_RowIndex', 'orderable': false, 'searchable': false},       
                    {data: 'website_id', name: 'website_id'},
                    {data: 'keywords', name: 'keywords'},
                    {data: 'user_id', name: 'user_id'},
                    {data: 'price', name: 'price'}, 
                    {data: 'documentLink', name: 'documentLink'},
                    {data: 'word_count', name: 'word_count'},
                    {data: 'suggestion', name: 'suggestion'},
                    {data: 'status', name: 'status'},
                    {data: 'seoChecklist', name: 'seoChecklist'},
                    {data: 'publishChecklist', name: 'publishChecklist'},
                    {data: 'liveStatusLink', name: 'liveStatusLink'},
                    {data: 'published_at', name: 'published_at'},
                    {data: 'actions', name: 'actions'},
                    {data:'status_color', name:'status_color', 'visible': false}
                @endif
            , ]
        });

        $(function() {
            $(document).on('click', '.addNewBtn', function() {
                let $formModal = $(document).find('#formModal');
                $($formModal).modal('show');
                $.ajax({
                    type: "GET"
                    , url: "{{ route('seo.content.create') }}"
                    , data: {
                        formType: "CREATE_FORM"
                    }
                    , dataType: "json"
                    , success: function(response) {
                        $($formModal).find('.modal-body').html(response.data)
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                let url = $(this).attr('data-url');
                let $formModal = $(document).find('#formModal');
                $($formModal).modal('show');
                $.ajax({
                    type: "GET"
                    , url: url
                    , data: {}
                    , dataType: "json"
                    , success: function(response) {
                        $($formModal).find('.modal-body').html(response.data)
                    }
                });
            });

            $(document).on('click', '.searchBtn', function() {
                $datatable.clear().draw();
            })
        })
    });

    $(document).ready(function() {
        let kwRowIdCount = 1;
        $(document).on('click', '.addKeywordBtn', function() {
            let $kwRow = $('.kwRow:last').clone();
            $('input', $kwRow).val('');
            $('select option', $kwRow).removeAttr('selected');
            $kwRow.appendTo($('.kwRowSec'));
            kwRowIdCount++;
        });

        $(document).on('click', '.kwRow .kwRmBtn', function() {
            let $kwSec = $(document).find('.kwRowSec');
            if ($kwSec.find('.kwRow').length != 1) {
                let $per = $(this).closest('.kwRow');
                $per.remove();
            }
        });

        $(function() {
            let $formModal = $(document).find('#formModal');
            $(document).on('click', '.addNewBtn', function() {
                $($formModal).modal('show');
                $.ajax({
                    type: "GET"
                    , url: "{{ route('seo.content.create') }}"
                    , data: {
                        formType: "CREATE_FORM"
                    }
                    , dataType: "json"
                    , success: function(response) {
                        $($formModal).find('.modal-body').html(response.data)
                    }
                });
            });

            $($formModal).on('hide.bs.modal', function() {
                $($formModal).find('.modal-body').html('');
            })
            $(document).on('click', '.saveFormBtn', function(e) {
                let $form = $(document).find('#seoForm');
                if (!$form.valid()) {
                    return false;
                }
                $.ajax({
                    type: "POST"
                    , url: $form.attr('action')
                    , data: $form.serialize()
                    , dataType: "json"
                    , success: function(response) {
                        if (response.success) {
                            $($formModal).modal('hide');
                            $('#seoProcessTbl').DataTable().clear().draw();
                        }
                    }
                });
            })
        })

        let $historyModal = $(document).find('#historyModal');
        $(document).on('click', '.priceHistoryBtn, .userHistoryBtn', function() {
            let data = {
                seoProcessId: $(this).attr('data-id')
                , seoType: $(this).attr('data-type')
                , type: "GET_HISTORY"
            , }
            $.ajax({
                type: "GET"
                , url: ""
                , data: data
                , dataType: "json"
                , success: function(response) {
                    $($historyModal).find('.modal-body').html(response.data);
                    $($historyModal).find('.modal-title').text(response.title);
                    $($historyModal).modal('show');
                }
            });
        });


        // Status Section
        $(function() {
            let $statusListModal = $(document).find('#statusListModal');
            let $statusFormModal = $(document).find('#statusFormModal');
            let $statusTable = null;
            $(document).on('click', '.statusListBtn', function() {
                $($statusListModal).modal('show');

                $statusTable = $(document).find('#statusTable').DataTable({
                    serverSide:true,
                    processing:true,
                    ajax:{
                        url:`{{ route('seo.content-status.index') }}`,
                        data:{
                            type:() => $($statusListModal).find('[name=type]').val()
                        }
                    },
                    columns:[
                        { data: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'label', name:'label'},
                        { data: 'type', name:'type'},
                        { data: 'created_at', name:'created_at'},
                        { data: 'action', name:'action'},
                    ]
                })
            });

            $($statusListModal).on('hide.bs.modal', function() {
                $statusTable.clear().destroy();
                $($statusListModal).find('[name=type]').val('').trigger('change');
            });

            $($statusFormModal).on('hide.bs.modal', function() {
                $($statusFormModal).find('.modal-body').html('');
            });

            $(document).on('click', '.addStatusBtn, .editStatusBtn', function() {
                let url = $(this).attr('data-url');
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {

                    },
                    dataType: "json",
                    success: function (response) {
                        $($statusFormModal).find('.modal-body').html(response.data);
                        $($statusFormModal).find('.modal-title').html(response.title);
                        $($statusFormModal).modal('show');
                    }
                });
            });

            $($statusFormModal).on('click', '.saveBtn', function() {
                let $form = $($statusFormModal).find('form#statusForm');
                $.ajax({
                    type: "POST",
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    dataType: "json",
                    success: function (response) {
                        $($statusFormModal).modal('hide');
                       $statusTable.clear().draw();
                    }
                });
            });

            $($statusListModal).on('click', '.searchBtn', function() {
                $statusTable.clear().draw();
            });
        })

        // Checklist Section
        $(function() {
            let $checklistModal = $(document).find('#checklistModal')
            $(document).on('click', '.checkListBtn', function() {
                let type = $(this).attr('data-type');
                let url = $(this).attr('data-url');
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        type:"CHECKLIST",
                        checklistType:type,
                    },
                    dataType: "json",
                    success: function (response) {
                        $($checklistModal).modal('show');
                        $($checklistModal).find('.modal-body').html(response.data)
                        $($checklistModal).find('.modal-title').html(response.title)
                    }
                });
            });

            $($checklistModal).on('click', '.saveBtn', function() {
                let $form = $($checklistModal).find('form#checkListForm');
                $.ajax({
                    type: "POST",
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    dataType: "json",
                    success: function (response) {
                        $($checklistModal).modal('hide');
                    }
                });
            });
        });

        $(function() {
            $(document).on('change', '.statusSelect', function() {
                let data = {
                    statusId: $(this).val(),
                    url: $(this).attr('data-url'),
                    type:"STATUS",
                    statusType:$(this).attr('data-type'),
                    _token: `{{ csrf_token() }}`
                };

                $.ajax({
                    type: "POST",
                    url: data.url,
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        
                    }
                });
            });

            let $checklistHistroyModal = $(document).find('#checklistHistoryModal');
            let $statusHistoryModal = $(document).find('#statusHistoryModal');
            $(document).on('click', '.checkListHistory', function() {
                let data = {
                    type:"CHECKLIST_HISTORY",
                    url:$(this).attr('data-url'),
                    field_name:$(this).attr('data-label'),
                    statusType:$(this).attr('data-type'),
                }
                $.ajax({
                    type: "GET",
                    url: data.url,
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        $($checklistHistroyModal).modal('show');
                        $($checklistHistroyModal).find('.modal-body').html(response.data);
                    }
                });
            });

            $(document).on('click', '.statusHistoryBtn', function() {
                let data = {
                    url: $(this).attr('data-url'),
                    type: "STATUS_HISTORY",
                    statusType: $(this).attr('data-type')
                };
                $.ajax({
                    type: "GET",
                    url: data.url,
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        $($statusHistoryModal).find('.modal-body').html(response.data);
                        $($statusHistoryModal).modal('show');
                    }
                });
            })
        })
    });

    

</script>
