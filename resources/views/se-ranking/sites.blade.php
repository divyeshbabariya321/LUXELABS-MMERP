@extends('layouts.app')
@section('title', 'SE Ranking Data')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">SE Ranking - Sites</h2>
        </div>        
    </div>
    <div class="container">
        <div class="row">
            @include('se-ranking.buttons-area')
            <div class="col-md-12">
                <table class="table" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Site ID</th>
                            <th>Title</th>
                            <th>Name(URL)</th>
                            <th>Subdomain Match</th>
                            <th>Check Frequency</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sites as $key => $site)
                        <tr>
                            <td>{{$site->id}}</td>
                            <td>{{$site->title}}</td>
                            <td><a href="{{url($site->name)}}">{{$site->name}}</a></td>
                            <td>{{$site->subdomain_match}}</td>
                            <td>{{$site->check_freq}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>                
            </div>
        </div>
    </div>
@endsection