<div id="createBloggerProductModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('blogger-product.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- blogger_id -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('blogger_id')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                 {{ html()->label(__('Blogger'), 'blogger_id')->class('form-control-label') }}
                                {{ html()->select('blogger_id', $select_bloggers)->class('form-control  ' . ($errors->has('blogger_id') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : ''))) }}
                                    @if($errors->has('blogger_id'))
                            <div class="form-control-feedback">{{$errors->first('blogger_id')}}</div>
                                        @endif
                            </div>
                        </div>
                        <!-- brand_id -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('brand_id')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                 {{ html()->label(__('Brand'), 'brand_id')->class('form-control-label') }}
                                {{ html()->select('brand_id', $select_brands)->class('form-control  ' . ($errors->has('brand_id') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : ''))) }}
                                    @if($errors->has('brand_id'))
                            <div class="form-control-feedback">{{$errors->first('brand_id')}}</div>
                                        @endif
                            </div>
                        </div>
                        <!-- shoot_date -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('shoot_date')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Shoot date'), 'shoot_date')->class('form-control-label') }}
                                {{ html()->input('date', 'shoot_date')->class('form-control ' . ($errors->has('shoot_date') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('shoot_date'))
                                    <div class="form-control-feedback">{{$errors->first('shoot_date')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- first_post -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('first_post')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('1st Post'), 'first_post')->class('form-control-label') }}
                                {{ html()->input('date', 'first_post')->class('form-control ' . ($errors->has('first_post') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('first_post'))
                                    <div class="form-control-feedback">{{$errors->first('first_post')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- second_post -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('second_post')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('2nd Post'), 'second_post')->class('form-control-label') }}
                                {{ html()->input('date', 'second_post')->class('form-control ' . ($errors->has('second_post') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('second_post'))
                                    <div class="form-control-feedback">{{$errors->first('second_post')}}</div>
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
                        <!-- initial_quote -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('initial_quote')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Initial Quote'), 'initial_quote')->class('form-control-label') }}
                                {{ html()->text('initial_quote')->class('form-control ' . ($errors->has('initial_quote') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('initial_quote'))
                                    <div class="form-control-feedback">{{$errors->first('initial_quote')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- final_quote -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('final_quote')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Final Quote'), 'final_quote')->class('form-control-label') }}
                                {{ html()->text('final_quote')->class('form-control ' . ($errors->has('final_quote') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('final_quote'))
                                    <div class="form-control-feedback">{{$errors->first('final_quote')}}</div>
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

<div id="updateBloggerProductModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="#" method="POST">
                @csrf
                @method('put')
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- blogger_id -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('blogger_id')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                 {{ html()->label(__('Blogger'), 'blogger_id')->class('form-control-label') }}
                                {{ html()->select('blogger_id', $select_bloggers)->class('form-control  ' . ($errors->has('blogger_id') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : ''))) }}
                                    @if($errors->has('blogger_id'))
                            <div class="form-control-feedback">{{$errors->first('blogger_id')}}</div>
                                        @endif
                            </div>
                        </div>
                        <!-- brand_id -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('brand_id')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                 {{ html()->label(__('Brand'), 'brand_id')->class('form-control-label') }}
                                {{ html()->select('brand_id', $select_brands)->class('form-control  ' . ($errors->has('brand_id') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : ''))) }}
                                    @if($errors->has('brand_id'))
                            <div class="form-control-feedback">{{$errors->first('brand_id')}}</div>
                                        @endif
                            </div>
                        </div>
                        <!-- shoot_date -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('shoot_date')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Shoot date'), 'shoot_date')->class('form-control-label') }}
                                {{ html()->input('date', 'shoot_date')->class('form-control ' . ($errors->has('shoot_date') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('shoot_date'))
                                    <div class="form-control-feedback">{{$errors->first('shoot_date')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- first_post -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('first_post')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('1st Post'), 'first_post')->class('form-control-label') }}
                                {{ html()->input('date', 'first_post')->class('form-control ' . ($errors->has('first_post') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('first_post'))
                                    <div class="form-control-feedback">{{$errors->first('first_post')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- first_post_likes -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('first_post_likes')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('First post likes'), 'first_post_likes')->class('form-control-label') }}
                                {{ html()->text('first_post_likes')->class('form-control ' . ($errors->has('first_post_likes') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('first_post_likes'))
                                    <div class="form-control-feedback">{{$errors->first('first_post_likes')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- first_post_engagement -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('first_post_engagement')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('First post engagement'), 'first_post_engagement')->class('form-control-label') }}
                                {{ html()->text('first_post_engagement')->class('form-control ' . ($errors->has('first_post_engagement') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('first_post_engagement'))
                                    <div class="form-control-feedback">{{$errors->first('first_post_engagement')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- first_post_response -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('first_post_response')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('First post response'), 'first_post_response')->class('form-control-label') }}
                                {{ html()->text('first_post_response')->class('form-control ' . ($errors->has('first_post_response') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('first_post_response'))
                                    <div class="form-control-feedback">{{$errors->first('first_post_response')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- first_post_sales -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('first_post_sales')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('First post sales'), 'first_post_sales')->class('form-control-label') }}
                                {{ html()->text('first_post_sales')->class('form-control ' . ($errors->has('first_post_sales') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('first_post_sales'))
                                    <div class="form-control-feedback">{{$errors->first('first_post_sales')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- second_post -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('second_post')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('2nd Post'), 'second_post')->class('form-control-label') }}
                                {{ html()->input('date', 'second_post')->class('form-control ' . ($errors->has('second_post') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('second_post'))
                                    <div class="form-control-feedback">{{$errors->first('second_post')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- second_post_likes -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('second_post_likes')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Second post likes'), 'second_post_likes')->class('form-control-label') }}
                                {{ html()->text('second_post_likes')->class('form-control ' . ($errors->has('second_post_likes') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('second_post_likes'))
                                    <div class="form-control-feedback">{{$errors->first('second_post_likes')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- second_post_engagement -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('second_post_engagement')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Second post engagement'), 'second_post_engagement')->class('form-control-label') }}
                                {{ html()->text('second_post_engagement')->class('form-control ' . ($errors->has('second_post_engagement') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('second_post_engagement'))
                                    <div class="form-control-feedback">{{$errors->first('second_post_engagement')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- second_post_response -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('second_post_response')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Second post response'), 'second_post_response')->class('form-control-label') }}
                                {{ html()->text('second_post_response')->class('form-control ' . ($errors->has('second_post_response') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('second_post_response'))
                                    <div class="form-control-feedback">{{$errors->first('second_post_response')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- second_post_sales -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('second_post_sales')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Second post sales'), 'second_post_sales')->class('form-control-label') }}
                                {{ html()->text('second_post_sales')->class('form-control ' . ($errors->has('second_post_sales') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('second_post_sales'))
                                    <div class="form-control-feedback">{{$errors->first('first_post_sales')}}</div>
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
                        <!-- initial_quote -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('initial_quote')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Initial Quote'), 'initial_quote')->class('form-control-label') }}
                                {{ html()->text('initial_quote')->class('form-control ' . ($errors->has('initial_quote') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('initial_quote'))
                                    <div class="form-control-feedback">{{$errors->first('initial_quote')}}</div>
                                @endif
                            </div>
                        </div>
                        <!-- final_quote -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('final_quote')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {{ html()->label(__('Final Quote'), 'final_quote')->class('form-control-label') }}
                                {{ html()->text('final_quote')->class('form-control ' . ($errors->has('final_quote') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : ''))) }}
                                @if($errors->has('final_quote'))
                                    <div class="form-control-feedback">{{$errors->first('final_quote')}}</div>
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