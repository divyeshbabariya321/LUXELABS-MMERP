<script type="text/x-jsrender" id="template-result-block">
<div class="table-responsive mt-3" {{:pageUrl}}>
    <table class="table table-bordered"style="table-layout:fixed;">
        <thead>
            <tr>
            <th width="5%">Id</th>
            <th width="10%">Store Website</th>
            <th width="20%">Path</th>
            <th width="20%">Value</th>
            <th width="20%">Command</th>
            <th width="10%">Created At</th>
            <th width="10%">Actions</th>
            </tr>
        </thead>
        <tbody>
            {{props data}} 
                <tr>
                <td>&nbsp;{{:prop.id}}</td>
                <td class="Website-task" title="{{:prop.store_website_name}}">{{:prop.store_website_name}}</td>
                <td class="Website-task" title="{{:prop.path}}">{{:prop.path}}</td>
                <td class="Website-task" title="{{:prop.path}}">{{:prop.value}}</td>
                <td class="Website-task" title="{{:prop.command}}">{{:prop.command}}</td>
                <td>{{:prop.created_at}}</td>
                <td>
                    <button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template" style="padding: 0px 5px !important;">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                    </button>
                    <button type="button" title="Update Value" data-id="{{>prop.id}}" class="btn btn-update-value" style="padding: 0px 5px !important;">
                        <i class="fa fa-upload" aria-hidden="true"></i>
                    </button>
                    <button type="button" title="History" data-id="{{>prop.id}}" class="btn btn-history" style="padding: 0px 5px !important;">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                </td>
                </tr>
            {{/props}}
        </tbody>
    </table>
    {{:pagination}}
</div>
</script> 