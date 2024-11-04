<tr>
    @if(!empty($dynamicColumnsToShowPostman))
        @if (!in_array('ID', $dynamicColumnsToShowPostman))
            <th style="width: 3%;">ID</th>
        @endif

        @if (!in_array('Folder Name', $dynamicColumnsToShowPostman))
            <th style="width: 4%;overflow-wrap: anywhere;">Folder Name</th>
        @endif

        @if (!in_array('PostMan Status', $dynamicColumnsToShowPostman))
            <th style="width: 25%;overflow-wrap: anywhere;">PostMan Status</th>
        @endif

        @if (!in_array('API Issue Fix Done', $dynamicColumnsToShowPostman))
            <th style="width: 15%;overflow-wrap: anywhere;">API Issue Fix Done</th>
        @endif

        @if (!in_array('Controller Name', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Controller Name</th>
        @endif

        @if (!in_array('Method Name', $dynamicColumnsToShowPostman))
            <th style="width: 4%;overflow-wrap: anywhere;">Method Name</th>
        @endif

        @if (!in_array('Request Name', $dynamicColumnsToShowPostman))
            <th style="width: 4%;overflow-wrap: anywhere;">Request Name</th>
        @endif

        @if (!in_array('Type', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Type</th>
        @endif

        @if (!in_array('URL', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">URL</th>
        @endif

        @if (!in_array('Request Parameter', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Request Parameter</th>
        @endif

        @if (!in_array('Params', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Params</th>
        @endif

        @if (!in_array('Headers', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Headers</th>
        @endif

        @if (!in_array('Request type', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Request type</th>
        @endif

        @if (!in_array('Request Response', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Request Response</th>
        @endif

        @if (!in_array('Response Code', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Response Code</th>
        @endif

        @if (!in_array('Grumphp Errors', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Grumphp Errors</th>
        @endif

        @if (!in_array('Magento API Standards', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Magento API Standards</th>
        @endif

        @if (!in_array('Swagger DocBlock', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Swagger DocBlock</th>
        @endif

        @if (!in_array('Used for', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Used for</th>
        @endif

        @if (!in_array('Used in', $dynamicColumnsToShowPostman))
            <th style="width: 5%;overflow-wrap: anywhere;">Used in</th>
        @endif

        @if (!in_array('Action', $dynamicColumnsToShowPostman))
            <th style="width: 22%;overflow-wrap: anywhere;">Action</th>
        @endif
    @else 
        <th style="width: 3%;">ID</th>
        <th style="width: 4%;overflow-wrap: anywhere;">Folder Name</th>
        <th style="width: 25%;overflow-wrap: anywhere;">PostMan Status</th>
        <th style="width: 15%;overflow-wrap: anywhere;">API Issue Fix Done</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Controller Name</th>
        <th style="width: 4%;overflow-wrap: anywhere;">Method Name</th>
        <th style="width: 4%;overflow-wrap: anywhere;">Request Name</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Type</th>
        <th style="width: 5%;overflow-wrap: anywhere;">URL</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Request Parameter</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Params</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Headers</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Request type</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Request Response</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Response Code</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Grumphp Errors</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Magento API Standards</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Swagger DocBlock</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Used for</th>
        <th style="width: 5%;overflow-wrap: anywhere;">Used in</th>
        <th style="width: 22%;overflow-wrap: anywhere;">Action</th>
    @endif
</tr>
