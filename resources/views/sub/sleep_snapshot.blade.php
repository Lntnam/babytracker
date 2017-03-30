<div class="row"> <!-- current value header -->
    <div class="col-6 text-center">
        Today so far
    </div>
    <div class="col-6 text-center">
        wake up @
        <mark id="last-wakeup-time">{{ (new Carbon($last_sleep->wake))->format('H:i') }}</mark>
    </div>
</div>
<div class="row"> <!-- current values -->
    <div class="col-6 text-center">
        <label id="today-sleep-total-hour" class="display-3 text-info">{{ $sleep->hours }}h</label>
        <br/><label id="today-sleep-total-minute" class="display-4 text-info">{{ $sleep->minutes }}m</label>
    </div>
    <div class="col-6 text-center">
        <label id="last-sleep-value-hour" class="display-3">{{ $last_sleep->hours }}h</label>
        <br/><label id="last-sleep-value-minute" class="display-4">{{ $last_sleep->minutes }}m</label>
    </div>
</div>