@extends('layouts.app')
@section('title', 'SE Ranking Data')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">SE Ranking - Audit Report</h2>
        </div>        
    </div>
    <div class="container">
        <div class="row">
            @include('se-ranking.buttons-area')
            <div class="col-md-12">
                <table class="table table-responsive" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Finished</th>
                            <th>AlexaRank</th>
                            <th>ArchiveOrg</th>
                            <th>Backlinks</th>
                            <th>Expdate</th>
                            <th>Index Bing</th>
                            <th>Index Google</th>
                            <th>Index Yahoo</th>
                            <th>Index Yandex</th>
                            <th>IP</th>
                            <th>Ip Country</th>
                            <th>MOZ DomainAuthority</th>
                            <th>AvgLoadSpeed</th>
                            <th>Score Percent</th>
                            <th>Total Pages</th>
                            <th>Total Warnings</th>
                            <th>Total Errors</th>
                            <th>Total Passed</th>
                            <th>Screenshot</th>
                            <th>Audit Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{($audit->is_finished = 1 ? 'True' : 'False')}}</td>
                            <td>{{$audit->domain_props->AlexaRank}}</td>
                            <td>{{$audit->domain_props->archiveOrg}}</td>
                            <td>{{$audit->domain_props->backlinks}}</td>
                            <td>{{$audit->domain_props->expdate}}</td>
                            <td>{{$audit->domain_props->index_bing}}</td>
                            <td>{{$audit->domain_props->index_google}}</td>
                            <td>{{$audit->domain_props->index_yahoo}}</td>
                            <td>{{$audit->domain_props->index_yandex}}</td>
                            <td>{{$audit->domain_props->ip}}</td>
                            <td>{{$audit->domain_props->IpCountry}}</td>
                            <td>{{$audit->domain_props->mozDomainAuthority}}</td>
                            <td>{{$audit->domain_props->avgLoadSpeed}}</td>
                            <td>{{$audit->score_percent}}%</td>
                            <td>{{$audit->total_pages}}</td>
                            <td>{{$audit->total_warnings}}</td>
                            <td>{{$audit->total_errors}}</td>
                            <td>{{$audit->total_passed}}</td>
                            <td>{{$audit->screenshot}}</td>
                            <td>{{$audit->audit_time}}</td>
                        </tr>
                    </tbody>
                </table>                
            </div>
        </div>
    </div>
@endsection