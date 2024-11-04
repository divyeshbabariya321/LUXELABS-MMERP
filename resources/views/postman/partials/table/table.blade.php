<div class="row m-0">
  <div class="col-12" style="border: 1px solid;border-color: #dddddd;">
    <div class="table-responsive mt-2" style="overflow-x: auto !important;">

        @if ($message = Session::get('success'))
            <div class="col-lg-12">
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        
    <table class="table table-bordered text-nowrap" id="postman-table">
        <thead>
            @include('postman.partials.table.thead')
        </thead>
        <tbody>
            @include('postman.partials.table.tbody')
        </tbody>
    </table>
    <div class="text-center" id="pagination">
        @include('postman.partials.table.pagination')
    </div>
    </div>
  </div>
  <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
  </div>
</div>