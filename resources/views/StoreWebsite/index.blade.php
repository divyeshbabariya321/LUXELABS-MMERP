@extends('layouts.app')

@section('title', 'Website List')

@section('styles')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
@endsection
@section('content')

    <table class="table table-bordered">
    <thead>
      <tr>
        <th>Name</th>
        <th>URL</th>
        <th>Login With Youtube</th>
      </tr>
    </thead>
    <tbody>
    @foreach($websites as $value)
      <tr>
        <td>{{$value->title}}</td>
        <td>{{$value->website}}</td>
        <td><button class="btn btn-primary "><a style="color:#fff" href="{{ route('youtuberedirect', ['id' => $value->id]) }}">Login With Youtube</a></button></td>
      </tr>
     @endforeach 
     
    </tbody>
  </table>

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>