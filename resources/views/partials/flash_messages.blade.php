@if ($message = Session::get('success'))
  <div class="alert alert-success">
    <p>{!! $message !!}</p>
  </div>
@endif

@if ($message = Session::get('error'))
  <div class="alert alert-danger">
    <p>{{ $message }}</p>
  </div>
@endif

@if ($message = Session::get('warning'))
  <div class="alert alert-warning">
    <p>{{ $message }}</p>
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="alert alert-danger hidden unauthorised">
</div>

@if (session('status'))
    @if(isset($withRole) && $withRole)
      <div class="alert alert-success" role="alert">
        {{ session('status') }}
      </div>
    @else
      <div class="alert alert-success">
        {{ session('status') }}
      </div>
    @endif
@endif

@if(session()->has('success'))
    @if (isset($extraDiv) && $extraDiv)
      <div class="col-lg-12 margin-tb">
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      </div>
    @elseif(isset($extraRow) && $extraRow)
      <div class="row m-2">
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      </div>
    @elseif (isset($withIcon) && $withIcon)
        <div class="alert alert-success">
          <i class="fe fe-check mr-2"></i> 
          {{ session('success') }}
        </div>

    @elseif(isset($withButton) && $withButton)
        <div class="col-sm-12">
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>

    @elseif(isset($withRole) && $withRole)
      <div class="alert alert-success" role="alert">
        {{ session('success') }}
      </div>

    @elseif(isset($withWidth) && $withWidth)
      <div class="alert alert-success w-100">
        {{ session('success') }}
      </div>

    @elseif(isset($colAlert) && $colAlert)
      <div class="col-lg-12 alert alert-success">
        {{ session('success') }}
      </div>

    @else
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

@endif

@if(session()->has('error'))
    @if (isset($extraDiv) && $extraDiv)
      <div class="col-lg-12">
        <div class="alert alert-danger">
          {{ session()->get('error') }}
        </div>
      </div>

    @elseif(isset($withIcon) && $withIcon)
      <div class="alert alert-danger">
        <i class="fe fe-alert-triangle mr-2"></i>{{ session()->get('error') }}
      </div>

    @elseif(isset($withButton) && $withButton)
      <div class="col-sm-12">
        <div class="alert  alert-danger" role="alert">
            {{ session()->get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
      </div>

    @elseif(isset($colAlert) && $colAlert)
      <div class="col-lg-12 alert alert-danger">
        {{ session()->get('error') }}
      </div>

    @elseif(isset($colMargin) && $colMargin)
      <div class="col-lg-12 margin-tb">
		    <div class="alert alert-danger">
          {{ session()->get('error') }}
        </div>
      </div>
    @else
      <div class="alert alert-danger">
        {{ session()->get('error') }}
      </div>

    @endif  
@endif

@if (session('alert'))
    <div class="alert alert-danger">
        {{ session('alert') }}
    </div>
@endif

@if(session()->has('message'))
    @if(isset($extraContent) && $extraContent)
      <div class="row">
        <div class="col-lg-12 margin-tb page-heading">
            @php $type = Session::get('alert-type', 'info'); @endphp
            @if($type == "info")
            <div class="alert alert-secondary">
                {{ session()->get('message') }}
            </div>
            @elseif($type == "warning")
            <div class="alert alert-warning">
                {{ session()->get('message') }}
            </div>
            @elseif($type == "success")
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
            @elseif($type == "error")
            <div class="alert alert-error">
                {{ session()->get('message') }}
            </div>
            @endif
        </div>
      </div>
    @elseif(isset($withMessageButton) && $withMessageButton)
      <div class="alert alert-success alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ Session::get('message') }}</strong>
      </div>
    @elseif(isset($withInfo) && $withInfo)
    <div class="mt-1">
        <div class="alert alert-info">
          {{ session()->get('message') }}
        </div>
    </div>
    @else
      <div class="alert alert-success">
          {{ session()->get('message') }}
      </div>
    @endif
@endif

@if(session()->has('errors'))
    @if(is_array(session()->get('errors')))
        @foreach(session()->get('errors') as $err)
            <div class="col-lg-12 alert alert-danger">
                {{ $err }}
            </div>
        @endforeach
    @else
        <div class="col-lg-12 alert alert-danger">
            {{ session()->get('errors') }}
        </div>
    @endif
@endif