@extends('layouts.app')

@section('styles')

@endsection
@section('content')

    <div class="row">
        <div class="col-12">
            <h2 class="page-heading">
                Blog Centralize (<span id="translation_count">{{ $allblogCentralize->total() }}</span>)

                <div class="pull-right">
                    
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#email-receive-modal">Email Receive</button>
                    
                </div>
            </h2>
        </div>
        
       

        <div class="col-lg-12 margin-tb">
            
            @if ($message = Session::get('danger'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @include('partials.flash_messages')

            <div class="table-responsive col-md-12" style="margin-top : 30px;">
                <table class="table table-striped table-bordered" id='tblBlogCentrelize' style="border: 1px solid #ddd;">
                    <thead>
                        <tr>
                            <th style="width: 2%;">#</th>
                            <th style="">Title</th>
                            <th style="">Content</th>
                            <th style="">From</th>
                            <th style="">Created By</th>
                            <th >Created at</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('blog-centralize.partial_index')
                    </tbody>
                </table>
            </div>
            <div id="centralize_blog_paginate">
                {{ $allblogCentralize->render() }}
            </div>
        </div>
    </div>
    @include('blog-centralize.partial_email_description')
    
    @include('blog-centralize.partials.modal-email-receive')
   

@endsection
@section('scripts')
    
   
    <script>

        $(document).on('click',"#centralize_blog_paginate .page-item a.page-link",function(e){
                e.preventDefault();
                let ajax_url = $(this).attr('href');
                $.ajax({
                    url: ajax_url,
                    method: "GET",
                    beforeSend: function() {
                        $("#loading-image-preview").show();
                    },
                    dataType : 'json',
                    success: function(data) {
                        if(data.tbody) {
                            $("#tblBlogCentrelize tbody").html(data.tbody);
                            $("#centralize_blog_paginate").html(data.links);
                            $("#translation_count").html(data.count);

                        } else {
                            toastr['error']('Error! Please try again', 'error');
                        }
                    },
                    complete : function(xhr,status) {
                        $("#loading-image-preview").hide();
                    },
                    error: function(xhr, status, error) {
                        // Handle the error here
                        toastr['error'](error, 'error');
                    }
                })


        })   
    </script>
   
@endsection
