@extends('layouts.app')
@section('title', 'SE Ranking Data')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">SE Ranking - Backlinks</h2>
        </div>        
    </div>
    <div class="container">
        <div class="row">
            @include('se-ranking.buttons-area')
            <div class="col-md-12">
                <table class="table" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Total</th>
                            <th>Anchors</th>
                            <th>IP</th>
                            <th>Domains</th>
                            <th>DoFollow</th>
                            <th>NoFollow</th>
                            <th>Limit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$backlinks->total}}</td>
                            <td>{{$backlinks->anchors}}</td>
                            <td>{{$backlinks->ip}}</td>
                            <td>{{$backlinks->domains}}</td>
                            <td>{{$backlinks->dofollow}}</td>
                            <td>{{$backlinks->nofollow}}</td>
                            <td>{{$backlinks->limit}}</td>
                        </tr>
                    </tbody>
                </table>
                {{-- <nav>
                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Project Tab 1</a>
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Project Tab 2</a>
                        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Project Tab 3</a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        
                    </div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <table class="table" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Employer</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="#">Work 1</a></td>
                                    <td>Doe</td>
                                    <td>john@example.com</td>
                                </tr>
                                <tr>
                                    <td><a href="#">Work 2</a></td>
                                    <td>Moe</td>
                                    <td>mary@example.com</td>
                                </tr>
                                <tr>
                                    <td><a href="#">Work 3</a></td>
                                    <td>Dooley</td>
                                    <td>july@example.com</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                        <table class="table" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Contest Name</th>
                                    <th>Date</th>
                                    <th>Award Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="#">Work 1</a></td>
                                    <td>Doe</td>
                                    <td>john@example.com</td>
                                </tr>
                                <tr>
                                    <td><a href="#">Work 2</a></td>
                                    <td>Moe</td>
                                    <td>mary@example.com</td>
                                </tr>
                                <tr>
                                    <td><a href="#">Work 3</a></td>
                                    <td>Dooley</td>
                                    <td>july@example.com</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection