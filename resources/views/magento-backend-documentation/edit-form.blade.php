<div class="col-sm-12">
    <div class="form-group">
        <label>Feature</label>
        {{ html()->text('features')->id('features')->placeholder('Feature')->class('form-control features')->required() }}

    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>description</label>
        {{ html()->text('description')->id('description')->placeholder('Template Files')->class('form-control description') }}
    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>admin Configuration</label>
        {{ html()->text('admin_configuration')->id('admin_configuration')->placeholder('Template AdminConfiguration')->class('form-control admin_configuration') }}
    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>Teamplate File</label>
        {{ html()->text('template_file')->id('template_file')->placeholder('Bug Solutions')->class('form-control template_file') }}                 
    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>Bug Details	</label>
        {{ html()->text('bug_details')->id('bug_details')->placeholder('Bug Details')->class('form-control bug_details') }}                 
    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>Bug Solutions</label>
        {{ html()->text('bug_resolution')->id('bug_resolution')->placeholder('Bug Solutions')->class('form-control bug_resolution') }}                 
    </div>
</div>