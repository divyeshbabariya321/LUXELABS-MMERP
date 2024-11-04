
@if(!empty(Request::get('status')))
    @if(Request::get('status')=='Approve' && ${$columnname . 'Var'}=='btn-success')
        </br>
        <button type="button" class="btn {{ ${$columnname . 'Var'} }} btn-sm update-scrapper-status" title="Status" data-task_id="{{$taskss_id}}" data-column_name="{{$columnname}}">
            <i class="fa {{ ${$columnname . 'Icon'} }}" aria-hidden="true"></i>
        </button>
    @elseif(Request::get('status')=='Unapprove' && ${$columnname . 'Var'}=='btn-danger')
        </br>
        <button type="button" class="btn {{ ${$columnname . 'Var'} }} btn-sm update-scrapper-status" title="Status" data-task_id="{{$taskss_id}}" data-column_name="{{$columnname}}">
            <i class="fa {{ ${$columnname . 'Icon'} }}" aria-hidden="true"></i>
        </button>
    @elseif(Request::get('status')=='Uncheck' && ${$columnname . 'Var'}=='btn-default')
        </br>
        <button type="button" class="btn {{ ${$columnname . 'Var'} }} btn-sm update-scrapper-status" title="Status" data-task_id="{{$taskss_id}}" data-column_name="{{$columnname}}">
            <i class="fa {{ ${$columnname . 'Icon'} }}" aria-hidden="true"></i>
        </button>
    @endif
@else
</br>
<button type="button" class="btn {{ ${$columnname . 'Var'} }} btn-sm update-scrapper-status" title="Status" data-task_id="{{$taskss_id}}" data-column_name="{{$columnname}}">
    <i class="fa {{ ${$columnname . 'Icon'} }}" aria-hidden="true"></i>
</button>
@endif

<!-- @if(${$columnname . 'Var'}=='btn-danger')
<button type="button" class="btn btn-default btn-sm update-scrapper-remarks" title="Remarks" data-task_id="{{ $taskss_id }}" data-column_name="{{$columnname}}">
    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
</button>

@if(!empty(${$columnname . 'Record'}))
    <button type="button" class="btn btn-default btn-sm view-scrapper-remarks" title="Remarks" data-task_id="{{ $taskss_id }}" data-column_name="{{$columnname}}" data-remarks="{{${$columnname . 'Record'}['remarks']}}">
        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
    </button>
@endif
@endif -->