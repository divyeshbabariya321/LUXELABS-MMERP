@extends('layouts.app')

@section('title', 'Comment List')

@section('styles')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
  

.card {
    
    border: none;
    box-shadow: 5px 6px 6px 2px #e9ecef;
    border-radius: 4px;
}


.dots{

    height: 4px;
  width: 4px;
  margin-bottom: 2px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
}

.badge{

        padding: 7px;
        padding-right: 9px;
    padding-left: 16px;
    box-shadow: 5px 6px 6px 2px #e9ecef;
}

.user-img{

    margin-top: 4px;
}

.check-icon{

    font-size: 17px;
    color: #c3bfbf;
    top: 1px;
    position: relative;
    margin-left: 3px;
}

.form-check-input{
    margin-top: 6px;
    margin-left: -24px !important;
    cursor: pointer;
}


.form-check-input:focus{
    box-shadow: none;
}


.icons i{

    margin-left: 8px;
}
.reply{

    margin-left: 12px;
}

.reply small{

    color: #b7b4b4;

}


.reply small:hover{

    color: green;
    cursor: pointer;

}
</style>
@endsection
@section('content')


                @if(count($commentsList) > 0) 
                 <h2 class="text-center">Comment List</h2>
                 
                <table class="table table-bordered">
               
                      <thead>
                        <tr>
                            <th>Commnet</th>
                            <th>Like Count</th>
                            <th>Create Date & Time</th>
                        </tr>
                    </thead>
                      
                    <tbody>
                        @foreach($commentsList as $value)

                        <tr>
                                <td>{{$value->title}}</td>
                                <td>{{$value->like_count}}</td>
                                <td>{{date('d-m-Y H:i:s a', strtotime($value->create_time))}}</td>
                        </tr>

                        @endforeach
                    </tbody>

                   
                 </table>
                    {{ $commentsList->links() }}

                    @else
                    <h4 class="Text-danger text-center jumbotron">Comments Not Available...</h4>
                    @endif

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>