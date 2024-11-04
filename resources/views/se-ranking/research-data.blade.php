@extends('layouts.app')
@section('title', 'SE Ranking Data')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">SE Ranking - Domain Overview (SEO/PPC Research Data)</h2>
        </div>        
    </div>
    <div class="container">
        <div class="row">
            @include('se-ranking.buttons-area')
            <div class="col-md-12">
                <table class="table" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th>Year Month</th>
                            <th>Organic Keyword Count</th>
                            <th>Organic Price Sum</th>
                            <th>Organic Traffic Sum</th>
                            <th>Paid Keyword Count</th>
                            <th>Paid Price Sum</th>
                            <th>Paid Traffic Sum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $new_items = App\Helpers::customPaginator(request(), $r_data, 25);
                        @endphp
                        @foreach ($new_items as $key => $data)
                            <tr>
                                <td>{{$data->source}}</td>
                                <td>{{$data->month}}, {{$data->year}}</td>
                                <td>{{$data->organic->keywords_count}}</td>
                                <td>{{$data->organic->price_sum}}</td>
                                <td>{{$data->organic->traffic_sum}}</td>
                                <td>{{$data->adv->traffic_sum}}</td>
                                <td>{{$data->adv->price_sum}}</td>
                                <td>{{$data->adv->keywords_count}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-center">
                    {!! $new_items->links() !!}
                </div>                
            </div>
        </div>
    </div>
@endsection