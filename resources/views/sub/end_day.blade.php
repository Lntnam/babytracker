<div class="row">
    <div class="col-12">
        @if (Carbon::today()->gt(new Carbon($current_date)))
            <p class="text-danger">This action is not reversible. All current values will be cleared and you
                cannot enter more data for today.</p>
            <p class="text-danger">Do you still want to end the day?</p>
        @else
            <p class="text-info">
                You cannot close today as it's not passed midnight.
            </p>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-6 push-3">
        @if (Carbon::today()->gt(new Carbon($current_date)))
            <a id="end-day-button" class="btn btn-danger btn-block" href="{{ route('CloseDay') }}"><i
                        class="fa fa-step-forward" aria-hidden="true"></i> End Day
            </a>
        @endif
    </div>
</div>
