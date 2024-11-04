@extends('layouts.app')

@section('content')
<h2 class="text-center">Get Users from Hubstaff</h2>
<div class="container">
@if(Session::has('message'))
  <div class="alert alert-success alert-block" >
    <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{{ Session::get('message') }}</strong>
  </div>
@endif

  <div class="row">
    <div class="col-md-5">
      <div class="well">
         {{ html()->form('POST', route('post.user-fromid'))->open() }}
          <div>
            <h3 class="text-center">Get User in a project</h3>
             
             <div class="form-group">
                <input class="form-control" name="auth_token" id="auth_token" type="text" placeholder="Your Auth Token" value="@if(auth()->user()->auth_token_hubstaff) {{ auth()->user()->auth_token_hubstaff }} @endif" required>
             </div>
            
             <div class="form-group">
               <input class="form-control" name="id" id="id" type="text" placeholder="User Id" required>
             </div>
            
             <br/>
             <div class="text-center">
              <button class="btn btn-info btn-lg" type="submit">Get User</button>
             </div>
          </div>
         {{ html()->form()->close() }}
       </div>
    </div>
   
</div>
@endsection