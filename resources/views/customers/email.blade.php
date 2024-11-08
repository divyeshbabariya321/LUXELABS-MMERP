<div id="emailAccordion">
  @if (count($emails) > 0)
    @foreach ($emails as $key => $email)
      <div class="card">
        <div class="card-header" id="headingEmail{{ $key }}">
          <h5 class="mb-0">
            <button class="btn btn-link collapsed collapse-fix email-fetch" data-toggle="collapse" data-target="#emailAcc{{ $key }}"  data-id="{{ $email['id'] ?? '' }}" data-type="{{ $email['type']}}" aria-expanded="false" aria-controls="">
              {{ $email['subject']}}
              {{ $email['date'] }}
            </button>
          </h5>
        </div>
        <div id="emailAcc{{ $key }}" class="collapse collapse-element" aria-labelledby="headingInstruction" data-parent="#instructionAccordion">
          <div class="email-content">
            <div class="card p-3">

            </div>
          </div>
        </div>
      </div>
    @endforeach
	@if (count($emails) > 0)
      {!! $emails->appends(request()->except('page'))->links() !!}
    @endif
  
  @endif
</div>


