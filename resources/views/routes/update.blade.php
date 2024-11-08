@extends('layouts.app')

@section('content')
	<?php $base_url = URL::to('/');?>
	<div class="row">
		
		@if(Session::has('message'))
			<p class="alert alert-info">{{ Session::get('message') }}</p>
		@endif
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <a class="btn btn-secondary" href="{{ route('routes.index') }}"> Back</a>
            </div>
        </div>
		<div class="col-lg-12 margin-tb">
			<div class="pull-left">
                <h2>Update Route</h2>
            </div>
        </div> 
    </div>
    
	<div class="row">
        <div class="col-lg-12 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                           Routes : {{$base_url.'/'.$routes->url}}
                        </h4>
                    </div>
					<div class="panel-body">
						{{ html()->modelForm($routes, 'POST', route('routes.update', [$routes->id]))->open() }}
							<div class="col-xs-12 col-sm-12 col-md-12">
								<div class="form-group">
									<strong>Page Title:</strong>
									{{ html()->text('page_title', $routes->page_title)->placeholder('Page Title')->class('form-control') }}
								</div>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<div class="form-group">
									<strong>Page Description:</strong>
									{{ html()->textarea('page_description', $routes->page_description)->placeholder('Page Description')->class('form-control') }}
								</div>
							</div>
							<div class="col-xs-2 col-sm-2 col-md-2">
								<div class="form-group">
									{{ html()->submit('Update')->class('form-control btn btn-default') }}
								</div>
							</div>
						{{ html()->closeModelForm() }}
                    </div>
                </div>
            </div>
		</div>
	</div>
	
   

@endsection