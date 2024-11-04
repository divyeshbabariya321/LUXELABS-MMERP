<div class="col-sm-12">
    <div class="form-group">
        <label>location</label>
        {{ html()->text('location')->id('location')->placeholder('Magento Frontend location')->class('form-control location')->required() }}

    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>Admin Configuration</label>
        {{ html()->text('admin_configuration')->id('admin_configuration')->placeholder('Magento Admin Configuration')->class('form-control admin_configuration') }}
    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>Frontend configuration</label>
        {{ html()->text('frontend_configuration')->id('frontend_configuration')->placeholder('Magento Frontend configuration')->class('form-control frontend_configuration') }}                 
    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>Parent folder</label>
        {{ html()->text('parent_folder')->id('parent_folder')->placeholder('Parent Folder')->class('form-control parent_folder') }}                 
    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>Child Folder</label>
        {{ html()->text('child_folder')->id('child_folder')->placeholder('Child Folder')->class('form-control child_folder') }}                 
    </div>
</div>