<div id="createBloggerModal" class="modal fade" role="dialog">
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
                    <div class="row">
                        <!-- name -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('name')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Name'), 'name')->class('form-control-label') }}
                                {{ html()->text('name')->class('form-control ' . ($errors->has('name') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : '')))->required() }}
                                @if($errors->has('name'))
                                    <div class="form-control-feedback">{{$errors->first('name')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- agency -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('agency')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Agency'), 'agency')->class('form-control-label') }}
                                {{ html()->text('agency')->class('form-control ' . ($errors->has('agency') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('agency'))
                                    <div class="form-control-feedback">{{$errors->first('agency')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- phone -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('phone')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Phone'), 'phone')->class('form-control-label') }}
                                {{ html()->text('phone')->class('form-control ' . ($errors->has('phone') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : ''))) }}
                                @if($errors->has('phone'))
                                    <div class="form-control-feedback">{{$errors->first('phone')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- email -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('email')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Email'), 'email')->class('form-control-label') }}
                                {{ html()->text('email')->class('form-control ' . ($errors->has('email') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('email'))
                                    <div class="form-control-feedback">{{$errors->first('email')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- city -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('city')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('City'), 'city')->class('form-control-label') }}
                                {{ html()->text('city')->class('form-control ' . ($errors->has('city') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('city'))
                                    <div class="form-control-feedback">{{$errors->first('city')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- country -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('country')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Country'), 'country')->class('form-control-label') }}
                                {{ html()->text('country')->class('form-control ' . ($errors->has('country') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('country'))
                                    <div class="form-control-feedback">{{$errors->first('country')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- instagram_handle -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('instagram_handle')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Instagram handle'), 'instagram_handle')->class('form-control-label') }}
                                {{ html()->text('instagram_handle')->class('form-control ' . ($errors->has('instagram_handle') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('instagram_handle'))
                                    <div class="form-control-feedback">{{$errors->first('instagram_handle')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- followers -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('followers')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Followers'), 'followers')->class('form-control-label') }}
                                {{ html()->text('followers')->class('form-control ' . ($errors->has('followers') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('followers'))
                                    <div class="form-control-feedback">{{$errors->first('followers')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- followings -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('followings')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Following'), 'followings')->class('form-control-label') }}
                                {{ html()->text('followings')->class('form-control ' . ($errors->has('followings') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('followings'))
                                    <div class="form-control-feedback">{{$errors->first('followings')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- avg_engagement -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('avg_engagement')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Avg engagement'), 'avg_engagement')->class('form-control-label') }}
                                {{ html()->text('avg_engagement')->class('form-control ' . ($errors->has('avg_engagement') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('avg_engagement'))
                                    <div class="form-control-feedback">{{$errors->first('avg_engagement')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- fake_followers -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('fake_followers')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Fake followers'), 'fake_followers')->class('form-control-label') }}
                                {{ html()->text('fake_followers')->class('form-control ' . ($errors->has('fake_followers') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('fake_followers'))
                                    <div class="form-control-feedback">{{$errors->first('fake_followers')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- industry -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('industry')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Industry'), 'industry')->class('form-control-label') }}
                                {{ html()->text('industry')->class('form-control ' . ($errors->has('industry') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('industry'))
                                    <div class="form-control-feedback">{{$errors->first('industry')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- brands -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('brands')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                 {{ html()->label(__('Brands'), 'brands[]')->class('form-control-label') }}
                                {{ html()->multiselect('brands[]', $select_brands)->class('form-control  ' . ($errors->has('brands') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : '')))->id('brands') }}
                                    @if($errors->has('brands'))
                            <div class="form-control-feedback">{{$errors->first('brands')}}</div>
                                        @endif
                            </div>
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