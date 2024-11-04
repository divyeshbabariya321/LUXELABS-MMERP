@extends('layouts.app')
@section('title', 'SE Ranking Data')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">SE Ranking - Competitors</h2>
        </div>        
    </div>
    <div class="container">
        <div class="row">
            @include('se-ranking.buttons-area')
            <div class="haslayout mt-5">
                <nav>
                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">General Info</a>
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Competitor's keyword positions</a>                        
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <table class="table" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>URL</th>
                                    <th>Keyword Positions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $new_items = App\Helpers::customPaginator(request(), $competitors, 25);
                                @endphp
                                @foreach ($new_items as $key => $competitor)
                                    <tr>
                                        <td>{{$competitor->id}}</td>
                                        <td><a href="{{!empty($competitor->name) ? url($competitor->name) : 'javascript:void(0)'}}">{{(!empty($competitor->name) ? $competitor->name : 'N/A')}}</a></td>
                                        <td><a href="{{!empty($competitor->url) ? url($competitor->url) : 'javascript:void(0)'}}">{{(!empty($competitor->url) ? $competitor->url : 'N/A')}}</a></td>
                                        <td><a href="{{!empty($competitor->id) ? route('getCompetitorsKeywordPos', ['id' => $competitor->id]) : 'javascript:void(0)'}}">KeyWord Positions</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            {!! $new_items->links() !!}
                        </div>
                    </div>
                    <div class="tab-pane active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <table class="table" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Position Date</th>
                                    <th>Position Number</th>
                                    <th>Position Change</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($keywords_pos_data as $key => $data)
                                @php
                                    // $keywords_pos = App\Helpers::customPaginator(request(), $data->keywords, 25);
                                @endphp
                                    @foreach ($data->keywords as $key => $k_data)
                                        <tr>
                                            <td>{{$k_data->id}}</td>
                                            <td>{{$k_data->positions[0]->date}}</td>
                                            <td>{{$k_data->positions[0]->pos}}</td>
                                            <td>{{$k_data->positions[0]->change}}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">                            
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
@endsection