<div class="row"> <!-- current value header -->
    <div class="col-6 text-center">
        Today so far
    </div>
    <div class="col-6 text-center">
        last meal @
        <mark id="last-meal-time">{{ !empty($last_meal) ? (new Carbon($last_meal->at))->format('H:i') : '' }}</mark>
    </div>
</div>
<div class="row"> <!-- current values -->
    <div class="col-6 text-center">
        <label id="today-meal-total" class="display-3 text-info">{{ $meal }}</label>ml
    </div>
    <div class="col-6 text-center">
        <label id="last-meal-value" class="display-3">{{ $last_meal->value }}</label>ml
    </div>
</div>
