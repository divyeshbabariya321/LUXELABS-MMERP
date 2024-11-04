<div id="caseFormModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <form action="#" method="POST">
                @csrf

                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- case_number -->
                    <div class="@if($errors->has('case_number')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                        <div class="form-group">
                            {{ html()->label('Case Number', 'case_number')->class('form-control-label') }}
                            {{ html()->text('case_number')->class('form-control ' . ($errors->has('case_number') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : ''))) }}
                            @if($errors->has('case_number'))
                                <div class="form-control-feedback">{{$errors->first('case_number')}}</div>
                            @endif
                        </div>
                    </div>
                    <!-- lawyer_id -->
                    <div class="@if($errors->has('lawyer_id')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                        <div class="form-group">
                             {{ html()->label('Lawyer', 'lawyer_id')->class('form-control-label') }}
                            {{ html()->select('lawyer_id', $lawyers)->class('form-control  ' . ($errors->has('lawyer_id') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : '')))->placeholder('Choose Lawyer for this case') }}
                            @if($errors->has('lawyer_id'))
                                <div class="form-control-feedback">{{$errors->first('lawyer_id')}}</div>
                            @endif
                        </div>
                    </div>
                    <!-- for_against -->
                    <div class="@if($errors->has('for_against')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                        <div class="form-group">
                            {{ html()->label('For/Against', 'for_against')->class('form-control-label') }}
                            {{ html()->text('for_against')->class('form-control ' . ($errors->has('for_against') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : ''))) }}
                            @if($errors->has('for_against'))
                                <div class="form-control-feedback">{{$errors->first('for_against')}}</div>
                            @endif
                        </div>
                    </div>
                    <!-- court_detail -->
                    <div class="@if($errors->has('court_detail')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                        <div class="form-group">
                            {{ html()->label('Court and other Case Details', 'court_detail')->class('form-control-label') }}
                            {{ html()->textarea('court_detail')->class('form-control ' . ($errors->has('court_detail') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : '')))->rows(4) }}
                            @if($errors->has('court_detail'))
                                <div class="form-control-feedback">{{$errors->first('court_detail')}}</div>
                            @endif
                        </div>
                    </div>
                    <!-- status -->
                    <div class="@if($errors->has('status')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                        <div class="form-group">
                            {{ html()->label('Status', 'status')->class('form-control-label') }}
                            {{ html()->select('status', $statuses)->class('form-control  ' . ($errors->has('status') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : '')))->placeholder('Choose status') }}
                            @if($errors->has('status'))
                                <div class="form-control-feedback">{{$errors->first('status')}}</div>
                            @endif
                        </div>
                    </div>
                    <!-- resource -->
                    <div class="@if($errors->has('resource')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                        <div class="form-group">
                            {{ html()->label('Resource', 'resource')->class('form-control-label') }}
                            {{ html()->text('resource')->class('form-control ' . ($errors->has('resource') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : ''))) }}
                            @if($errors->has('resource'))
                                <div class="form-control-feedback">{{$errors->first('resource')}}</div>
                            @endif
                        </div>
                    </div>
                    <!-- last_date -->
                    <div class="@if($errors->has('last_date')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                        <div class="form-group">
                            {{ html()->label('Last Date', 'last_date')->class('form-control-label') }}
                            {{ html()->input('date', 'last_date')->class('form-control ' . ($errors->has('last_date') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : ''))) }}
                            @if($errors->has('last_date'))
                                <div class="form-control-feedback">{{$errors->first('last_date')}}</div>
                            @endif
                        </div>
                    </div>
                    <!-- next_date -->
                    <div class="@if($errors->has('next_date')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                        <div class="form-group">
                            {{ html()->label('Next Date', 'next_date')->class('form-control-label') }}
                            {{ html()->input('date', 'next_date')->class('form-control ' . ($errors->has('next_date') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : ''))) }}
                            @if($errors->has('next_date'))
                                <div class="form-control-feedback">{{$errors->first('next_date')}}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Add</button>
                </div>
            </form>
        </div>

    </div>
</div>