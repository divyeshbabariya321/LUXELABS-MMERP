<div id="lawyerFormModal" class="modal fade" role="dialog">
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
                    <div class="form-group">
                        {{ html()->label('Name', 'name')->class('form-control-label') }}
                        {{ html()->text('name')->class('form-control')->placeholder('')->required() }}
                        @if ($errors->has('name'))
                            <div class="alert alert-danger">{{$errors->first('name')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        {{ html()->label('Speciality', 'speciality_id')->class('form-control-label') }}
                        {{ html()->select('speciality_id', $specialities)->class('form-control')->placeholder('Select Speciality of a Lawyer')->required() }}
                        @if ($errors->has('speciality_id'))
                            <div class="alert alert-danger">{{$errors->first('speciality_id')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        {{ html()->label('Rating', 'rating')->class('form-control-label') }}
                        {{ html()->select('rating', [1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->class('form-control')->placeholder('Rate this Lawyer') }}
                        @if ($errors->has('rating'))
                            <div class="alert alert-danger">{{$errors->first('rating')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        {{ html()->label('Address', 'address')->class('form-control-label') }}
                        {{ html()->text('address')->class('form-control')->placeholder('') }}
                        @if ($errors->has('address'))
                            <div class="alert alert-danger">{{$errors->first('address')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        {{ html()->label('Phone', 'phone')->class('form-control-label') }}
                        {{ html()->text('phone')->class('form-control')->placeholder('') }}
                        @if ($errors->has('phone'))
                            <div class="alert alert-danger">{{$errors->first('phone')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        {{ html()->label('Email', 'email')->class('form-control-label') }}
                        {{ html()->input('email', 'email')->class('form-control')->placeholder('') }}
                        @if ($errors->has('email'))
                            <div class="alert alert-danger">{{$errors->first('email')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        {{ html()->label('Referenced By', 'referenced_by')->class('form-control-label') }}
                        {{ html()->text('referenced_by')->class('form-control')->placeholder('') }}
                        @if ($errors->has('referenced_by'))
                            <div class="alert alert-danger">{{$errors->first('referenced_by')}}</div>
                        @endif
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