
<div class="modal-header">
    <h4 class="modal-title">Stats Comparison</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 mt-2">
            <ul class="nav nav-tabs">
                <li class="nav-item active"><a class="nav-link"data-toggle="tab" href="#PageSpeed" aria-selected="true" onclick = "setactive('a_tab')">PageSpeed</a></li>
                <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#YSlow" aria-selected="false" onclick = "setactive('b_tab')">YSlow</a></li>
            </ul>

            <div class="tab-content">
                <div id="PageSpeed" class="tab-pane active">
                    <?php // var_dump($resourcedata); ?>

                    <table class="table table-striped table-lg">
                        <tr>
                            <th>Recommendation</th>
                            @foreach ($page_data[array_key_first($page_data)] as $i => $d)
                            <th>{{$Colname[$i]}}</th>
                            @endforeach

                        </tr>
                        @foreach ($page_data as $k => $data)
                        <tr>
                            <td>{{$k}}</td>
                            @foreach ($data as $d)

                            @php $color="white" @endphp
                            @if($d >= 89)
                            @php $color = 'bg-success' ; @endphp
                            @endif
                            @if($d <= 48)
                            @php $color = 'bg-danger' ; @endphp
                            @endif
                            @if($d <= 60 && $d >= 48 )
                            @php $color = 'bg-warning' ; @endphp
                            @endif
                            <td><div class="progress">
                                <div class="progress-bar {{$color}} progress-bar-striped " role="progressbar" style="width: {{$d}}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{$d}}%</div>
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </table>
                </div>
                <div id="YSlow" class="tab-pane">
                    <table class="table table-striped table-lg">
                        <tr>
                        <th>Recommendation</th>
                            @foreach ($yslow_data[array_key_first($yslow_data)] as $i => $d)
                            <th>{{$Colname[$i]}}</th>
                            @endforeach
                        </tr>
                        @foreach ($yslow_data as $k => $data)
                        <tr>
                            <td>{{$k}}</td>
                            @foreach ($data as $d)
                            @php $color="white" @endphp
                            @if($d >= 89)
                            @php $color = 'bg-success' ; @endphp
                            @endif
                            @if($d <= 48)
                            @php $color = 'bg-danger' ; @endphp
                            @endif
                            @if($d <= 60 && $d >= 48 )
                            @php $color = 'bg-warning' ; @endphp
                            @endif
                            <td><div class="progress">
                                <div class="progress-bar {{$color}} progress-bar-striped " role="progressbar" style="width: {{$d}}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{$d}}%</div>
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
