<div class="row">
    <div class="col-md-12">
        <div class="collapse" id="plannedFilterCount">
            <div class="card card-body">
                @if(!empty($countPlanned))
                    <div class="row col-md-12">
                        @foreach($countPlanned as $listFilter)
                            <div class="col-md-2">
                                <div class="card">
                                    <div class="card-header">{{$listFilter["name"]}}</div>
                                    <div class="card-body">{{$listFilter["count"]}}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    Sorry , No data available
                @endif
            </div>
        </div>
        <div class="collapse" id="inProgressFilterCount">
            <div class="card card-body">
                @if (!empty($countInProgress))
                    <div class="row col-md-12">
                        @foreach ($countInProgress as $listFilter)
                            <div class="col-md-2">
                                <div class="card">
                                    <div class="card-header">{{$listFilter["name"]}}</div>
                                    <div class="card-body">{{$listFilter["count"]}}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    Sorry , No data available
                @endif
            </div>
        </div>
    </div>
</div>
