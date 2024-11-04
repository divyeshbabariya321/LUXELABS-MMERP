@extends('layouts.app')

@section('title', 'Chanel List')

@section('styles')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
@endsection
@section('content')

    <table class="table table-bordered">
    <thead>
    <h4 style="text-align:center">Chanel List</h4>
    <br>
      <tr>
        
        <th>Name</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
 
      <tr>
        <td>{{ $chanelsList['snippet']['title']}} </td>
        <td><button class="btn btn-primary "><a href="{{ route('videoList', ['chanelId' => $chanelsList['id'], 'websiteId' => $websiteId]) }}" style="color:#fff" >List Videos</a></button></td>
      </tr>
     
     
    </tbody>
  </table>

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>