@extends('layouts.app')

@section('content')
<div class="container container-grow">
    <form action="{{ url('github/sync/start') }}" method="GET">
        <div class="row mb-3">
            <div class="col-md-3"></div>

            <div class="col-md-6">
                <h2 class="text-center">Sync Github</h2>

                <label for="" class="form-label">Organization</label>
                <select name="organizationId" id="organizationId" class="form-control" required>
                    @foreach ($githubOrganizations as $githubOrganization)
                        <option value="{{ $githubOrganization->id }}" data-repos='{{ $githubOrganization->repos }}' {{ ($githubOrganization->name == 'MMMagento' ? 'selected' : '' ) }}>{{  $githubOrganization->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">    
            <div class="col-md-3"></div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Sync Github data</button>
            </div>
        </div>
    </form>
</div>    
@endsection