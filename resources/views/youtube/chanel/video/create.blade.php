@extends('layouts.app')

@section('title', 'Post Video')

@section('styles')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
@endsection
@section('content')
    <div class="container">
    <h2>Upload Video</h2>
     <form method="POST" action="/youtube/video/upload" enctype="multipart/form-data" method="post">
     @csrf
     <input type="hidden" name="tableChannelId" value={{$chanelTableId}}>
        <div class="mb-3">
            <label class="form-label" for="title">Title</label>
            <input type="text" class="form-control" id="youtubeTitle" placeholder="Title" required name="title" >
             @if ($errors->has('title'))
            <span class="text-danger">{{$errors->first('title')}}</span>
             @endif
        </div>

        <div class="mb-3">
            <label class="form-label" for="description">Description</label>
            <br>
            <textarea id="description" name="description" rows="4" cols="50" required></textarea>
              @if ($errors->has('description'))
                                    <span class="text-danger">{{$errors->first('description')}}</span>
                                @endif

        </div>

         <div class="mb-3">
            <label class="form-label" for="status">Status</label>
             <select required="required" class="browser-default custom-select" id="status" name="status" style="height: auto">
                                <option value="public" selected>Public</option>
                                <option value="private">Private</option>
                            </select>
                            @if ($errors->has('status'))
                                <span class="text-danger">{{$errors->first('status')}}</span>
                            @endif

        </div>

      <div class="mb-3">
                        <label for="Category" class="col-form-label">Select Category</label>
                   
                            <select required='required' class="browser-default custom-select" id="videoCategories" name="videoCategories" required="required" style="height: auto">
                                <option value="" selected>---Select Video Category---</option>
                                @foreach($categoriesData as $key => $sw)  
                                      <option value="{{$key}}">{{$sw}}</option>  
                                  @endforeach
                            </select>
                         
        </div>

        <div class="mb-3">
            <label class="form-label" for="inputPassword">Video Upload</label>
          <input type="file" class="file-control" name="youtubeVideo">
        </div>
       
        <button type="submit" class="btn btn-primary">Upload</button>
         
    </form>
</div>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

