<div class="row">
    <div class="col col-md-12">
        @foreach($allStatus as $statusName => $v)
            <div class="col-md-2 pl-0">
                Status {{ $statusName }} count = {{ $allStatusCounts[$statusName] ?? 0 }}
            </div>
        @endforeach
    </div>    
</div>
