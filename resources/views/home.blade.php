@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h5>{{ (new Carbon($current_date))->toFormattedDateString() }}
        <span class="badge badge-primary">{{ $age->weeks . 'w ' . $age->daysExcludeWeeks . 'd' }}</span>
        <span class="badge badge-info">{{ $weight }}kg</span>
        <span class="badge badge-info">{{ $height }}cm</span>
    </h5>

    <!-- notifications -->
    @if (!empty($notifications))
        <div class="row">
            <div class="col-12">
                @foreach ($notifications as $n)
                    <div class="alert alert-{{  $n->type }} alert-dismissible fade show" role="alert">
                        <input type="hidden" value="{{ $n->id }}" name="alert-id">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>{{ $n->title }}</strong> {{ $n->message }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Tab -->
    <ul class="nav nav-tabs nav-fill" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#eat" role="tab"><i class="fa fa-cutlery fa-fw"
                                                                                   aria-hidden="true"></i> Eat</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#sleep" role="tab"><i class="fa fa-moon-o fa-fw"
                                                                              aria-hidden="true"></i> Sleep</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#next-day" role="tab"><i class="fa fa-step-forward fa-fw"
                                                                                 aria-hidden="true"></i> End Day</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="eat" role="tabpanel">
            <!-- Meal -->
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
                    <label id="today-meal-value" class="display-3 text-info">{{ $meal }}</label>ml
                </div>
                <div class="col-6 text-center">
                    <label id="last-meal-value" class="display-3">{{ $last_meal->value }}</label>ml
                </div>
            </div>
            <div class="row"><!-- add meal button -->
                <div class="col-6 push-3">
                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal"
                            data-target="#addMealModal"><i class="fa fa-plus-square" aria-hidden="true"></i> add
                    </button>
                </div>
            </div>
            <div class="row"><!-- Meal today vs yesterday -->
                <div class="col-6">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th colspan="2">Today</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($today_meals as $meal)
                            <tr>
                                <th scope="row">{{ (new Carbon($meal->at))->format('H:i') }}</th>
                                <td>{{  $meal->value }}
                                    ml {!! $meal->feed_type == 'breast' ? '<i class="fa fa-user-o text-success" aria-hidden="true"></i>' : '' !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th colspan="2">Yesterday</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($yesterday_meals as $meal)
                            <tr>
                                <th scope="row">{{ (new Carbon($meal->at))->format('H:i') }}</th>
                                <td>{{ $meal->value }}ml</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="sleep" role="tabpanel">
            <!-- Sleep -->
            <div class="row"> <!-- current value header -->
                <div class="col-6 text-center">
                    Today so far
                </div>
                <div class="col-6 text-center">
                    wake up @
                    <mark id="last-meal-time">{{ (new Carbon($last_sleep->wake))->format('H:i') }}</mark>
                </div>
            </div>
            <div class="row"> <!-- current values -->
                <div class="col-6 text-center">
                    <label id="today-sleep-value-hour" class="display-3 text-info">{{ $sleep->hours }}h</label>
                    <br/><label id="today-sleep-value-minute" class="display-4 text-info">{{ $sleep->minutes }}m</label>
                </div>
                <div class="col-6 text-center">
                    <label id="last-sleep-value-hour" class="display-3">{{ $last_sleep->hours }}h</label>
                    <br/><label id="last-sleep-value-minute" class="display-4">{{ $last_sleep->minutes }}m</label>
                </div>
            </div>
            @if (!empty($sleeping_record))
                <div class="row"> <!-- current sleeping status -->
                    <div class="col-12 text-center">
                        <p class="lead">sleeping from
                            <mark
                                    id="sleep-from">{{ (new Carbon($sleeping_record->sleep))->format('H:i') }}</mark>
                        </p>
                    </div>
                </div>
                <div class="row"><!-- wake up buttons -->
                    <div class="col-5 push-1">
                        <button id="sleep-button" type="button"
                                class="btn btn-success btn-block"
                                data-toggle="modal" data-target="#wakeUpModal"><i class="fa fa-sun-o"
                                                                                  aria-hidden="true"></i> wake up
                        </button>
                    </div>
                    <div class="col-5 push-1">
                        <button id="cancel-sleep-button" type="button" class="btn btn-danger btn-block"><i
                                    class="fa fa-minus-square" aria-hidden="true"></i> cancel
                        </button>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-6 push-3">
                        <button id="sleep-button" type="button"
                                class="btn btn-primary btn-block"
                                data-toggle="modal" data-target="#addSleepModal"><i class="fa fa-moon-o"
                                                                                    aria-hidden="true"></i> sleep
                        </button>
                    </div>
                </div>
            @endif
            <div class="row"> <!-- Sleep today vs yesterday -->
                <div class="col-6">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th colspan="2">Today</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($today_sleeps as $sleep)
                            <tr>
                                <th scope="row">{{ (new Carbon($sleep->sleep))->format('H:i') }}</th>
                                <td>{{  $sleep->hours }}h {{ $sleep->minutes }}m</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th colspan="2">Yesterday</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($yesterday_sleeps as $sleep)
                            <tr>
                                <th scope="row">{{ (new Carbon($sleep->sleep))->format('H:i') }}</th>
                                <td>{{  $sleep->hours }}h {{ $sleep->minutes }}m</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="next-day" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    @if ($can_close)
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
                    @if ($can_close)
                        <a id="end-day-button" class="btn btn-danger btn-block" href="{{ route('CloseDay') }}"><i
                                    class="fa fa-step-forward" aria-hidden="true"></i> End Day
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Change Weight Modal -->
    <div class="modal fade" id="changeWeightModal" tabindex="-1" role="dialog" aria-labelledby="Edit Weight"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeWeightModalLabel">Edit Weight</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="weight-input" class="sr-only">Weight in kg</label>
                        <div class="input-group">
                            <input class="form-control focus" type="text" value="{{ $weight }}" id="weight-input"
                                   placeholder="Weight in kg">
                            <div class="input-group-addon">kg</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Meal Modal -->
    <div class="modal fade" id="addMealModal" tabindex="-1" role="dialog" aria-labelledby="Add Meal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMealModalLabel">Add Meal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="meal-time-input" class="sr-only">Time</label>
                        <input class="form-control" type="time" type="text" value="{{ Carbon::now()->format('H:i') }}"
                               id="meal-time-input">
                    </div>
                    <div class="form-group">
                        <label for="meal-input" class="sr-only">ml</label>
                        <div class="input-group">
                            <input class="form-control" type="text" value="" id="meal-input"
                                   placeholder="Amount in ml">
                            <div class="input-group-addon">ml</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="sr-only">Feeding Type</label>
                        <label class="custom-control custom-radio">
                            <input id="bottle-fed" name="feed_type" value="bottle" type="radio"
                                   class="custom-control-input" checked="checked">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Bottle</span>
                        </label>
                        <label class="custom-control custom-radio">
                            <input id="breast-fed" name="feed_type" value="breast" type="radio"
                                   class="custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Breast</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Sleep Modal -->
    <div class="modal fade" id="addSleepModal" tabindex="-1" role="dialog" aria-labelledby="Sleeping"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSleepModalLabel">Sleeping</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="meal-time-input" class="sr-only">Time</label>
                        <input class="form-control" type="time" type="text" value="{{ Carbon::now()->format('H:i') }}"
                               id="sleep-time-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Wake Up Modal -->
    <div class="modal fade" id="wakeUpModal" tabindex="-1" role="dialog" aria-labelledby="Wake Up" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="wakeUpModalLabel">Wake Up</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="meal-time-input" class="sr-only">Time</label>
                        <input class="form-control" type="time" type="text" value="{{ Carbon::now()->format('H:i') }}"
                               id="wake-time-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //-- closing alerts
        $('.alert').on('closed.bs.alert', function () {
            $.post("{{ route('Ajax.CloseNotification') }}", {id: $(this).find('input').val()});
        });

        //-- saving weight
        $('#changeWeightModal').find('button.btn-primary').on('click', function () {
            $(this).append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>')
            $(this).off('click');
            $.post("{{ route('Ajax.SaveWeight') }}", {value: $('#weight-input').val()}, function (data) {
                if (data != '-1') {
                    location.reload();
                }
            });
        });

        //-- adding meal
        $('#addMealModal').find('button.btn-primary').on('click', function () {
            $(this).append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>')
            $(this).off('click');
            $.post("{{ route('Ajax.AddMeal') }}", {
                value: $('#meal-input').val(),
                type: $('#bottle-fed').prop("checked") ? $('#bottle-fed').attr('value') : $('#breast-fed').attr('value'),
                at: $('#meal-time-input').val()
            }, function () {
                location.reload();
            });
        });

        //-- sleep
        function sleepClick() {
            $(this).append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>')
            $(this).off('click');
            $.post("{{ route('Ajax.ToggleSleep') }}", {
                sleep_time: $('#sleep-time-input').val(),
                wake_time: $('#wake-time-input').val()
            }, function () {
                location.reload();
            });
        };
        $('#addSleepModal').find('button.btn-primary').on('click', sleepClick);
        $('#wakeUpModal').find('button.btn-primary').on('click', sleepClick);
        $('#cancel-sleep-button').on('click', function () {
            $(this).append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>')
            $(this).off('click');
            $.post("{{ route('Ajax.CancelSleep') }}", {}, function () {
                location.reload();
            });
        })
    </script>
@endsection
