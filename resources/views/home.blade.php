@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row">
        <div class="col-3">
            <h5>{{ (new Carbon($current_date))->format('M j') }}</h5>
        </div>
        <div id="age-weight-height" class="col-7">
            @include('sub.age_weight_height')
        </div>
        <div class="col-1">
            <button type="button" role="button" class="btn btn-sm" data-toggle="modal"
                    data-target="#changeWeightModal"><i class="fa fa-balance-scale" aria-hidden="true"></i></button>
        </div>
    </div>

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
            <div id="meal-snapshot">
                @include('sub.meal_snapshot')
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
                        <tbody id="today-meals-table">
                        @include('sub.meals_table', ['meal_list' => $today_meals])
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
                        @include('sub.meals_table', ['meal_list' => $yesterday_meals])
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="sleep" role="tabpanel">
            <!-- Sleep -->
            <div id="sleep-snapshot">
                @include('sub.sleep_snapshot')
            </div>
            <div id="sleep-status">
                @include('sub.sleep_status')
            </div>
            <div class="row"> <!-- Sleep today vs yesterday -->
                <div class="col-6">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th colspan="2">Today</th>
                        </tr>
                        </thead>
                        <tbody id="today-sleep-table">
                        @include('sub.sleeps_table', ['sleep_list' => $today_sleeps])
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
                        @include('sub.sleeps_table', ['sleep_list' => $yesterday_sleeps])
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
                            <input class="form-control focus" type="number" value="{{ $weight }}" id="weight-input"
                                   placeholder="Weight in kg">
                            <div class="input-group-addon">kg</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="weight-input" class="sr-only">Height in cm</label>
                        <div class="input-group">
                            <input class="form-control focus" type="number" value="{{ $height }}" id="height-input"
                                   placeholder="Height in cm">
                            <div class="input-group-addon">cm</div>
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
                        <div class="input-group clockpicker" data-placement="right" data-align="top"
                             data-autoclose="true">
                            <input id="meal-time-input" type="text" class="form-control"
                                   value="{{ Carbon::now()->format('H:i') }}" readonly="true">
                            <span class="input-group-addon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="meal-input" class="sr-only">ml</label>
                        <div class="input-group">
                            <input class="form-control" type="number" id="meal-input" placeholder="Amount in ml">
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
                        <div class="input-group clockpicker" data-placement="right" data-align="top"
                             data-autoclose="true">
                            <input id="sleep-time-input" type="text" class="form-control"
                                   value="{{ Carbon::now()->format('H:i') }}" readonly="true">
                            <span class="input-group-addon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
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
                        <div class="input-group clockpicker" data-placement="right" data-align="top"
                             data-autoclose="true">
                            <input id="wake-time-input" type="text" class="form-control"
                                   value="{{ Carbon::now()->format('H:i') }}">
                            <span class="input-group-addon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
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
@endsection

@section('stylesheets')
    <link href="{{ asset('css/bootstrap-clockpicker.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/bootstrap-clockpicker.min.js') }}"></script>
    <script type="text/javascript">
        var timeout_id;
        var autotask_checker = 0;

        $(document).ready(function () {
            var clockpicker_options = {
                default: 'now',
                autoclose: true
            };
            $('#meal-time-input').clockpicker(clockpicker_options);
            $('#sleep-time-input').clockpicker(clockpicker_options);
            $('#wake-time-input').clockpicker(clockpicker_options);

            //-- auto reload
            autoload();

            //-- closing alerts
            $('.alert').on('closed.bs.alert', function () {
                $.post("{{ route('Ajax.CloseNotification') }}", {id: $(this).find('input').val()}, function () {
                    console.log('CloseNotification returned.');
                });
            });

            //-- saving weight & height
            $('#changeWeightModal').find('button.btn-primary').on('click', function () {
                $(this).append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>')
                $(this).prop('disabled', true);
                clearTimeout(timeout_id);
                $.post("{{ route('Ajax.SaveMeasurements') }}", {
                    weight: $('#weight-input').val(),
                    height: $('#height-input').val()
                }, function () {
                    console.log('SaveMeasurements returned.');

                    $('#age-weight-height').load('{!! route('Ajax.LoadAgeWeightHeightView') !!}', function() {
                        $('#changeWeightModal').find('button.btn-primary').empty().text('Save');
                        $('#changeWeightModal').find('button.btn-primary').prop('disabled', false);
                        $('#changeWeightModal').modal('hide');
                        autoload();
                    });
                });
            });

            //-- adding meal
            $('#addMealModal').find('button.btn-primary').on('click', function () {
                $(this).append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>')
                $(this).prop('disabled', true);
                clearTimeout(timeout_id);
                $.post("{{ route('Ajax.AddMeal') }}", {
                    value: $('#meal-input').val(),
                    type: $('#bottle-fed').prop("checked") ? $('#bottle-fed').attr('value') : $('#breast-fed').attr('value'),
                    at: $('#meal-time-input').val()
                }, function () {
                    console.log('AddMeal returned.');

                    $('#meal-snapshot').load('{!! route('Ajax.LoadMealSnapshotView') !!}', function() {
                        $('#today-meals-table').load('{!! route('Ajax.LoadTodayMealsView') !!}', function() {
                            $('#addMealModal').find('button.btn-primary').empty().text('Save');
                            $('#addMealModal').modal('hide');
                            $('#addMealModal').find('button.btn-primary').prop('disabled', false);
                            autoload();
                        });
                    });
                });
            });

            $('#addSleepModal').find('button.btn-primary').on('click', sleepClick);
            $('#wakeUpModal').find('button.btn-primary').on('click', sleepClick);
        });

        //-- sleep
        function sleepClick() {
            $(this).append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>')
            $(this).prop('disabled', true);
            clearTimeout(timeout_id);
            $.post("{{ route('Ajax.ToggleSleep') }}", {
                sleep_time: $('#sleep-time-input').val(),
                wake_time: $('#wake-time-input').val()
            }, function (data) {
                console.log('ToggleSleep returned:');
                console.log(data);

                $('#sleep-status').load('{!! route('Ajax.LoadSleepStatusView') !!}', function() {
                    if (data.sleeping) { // from wake to sleep
                        $('#addSleepModal').find('button.btn-primary').empty().text('Save');
                        $('#addSleepModal').modal('hide');
                        $('#addSleepModal').find('button.btn-primary').prop('disabled', false);
                        autoload();
                    }
                    else { // from sleep to wake
                        $('#sleep-snapshot').load('{!! route('Ajax.LoadSleepSnapshotView') !!}', function() {
                            $('#today-sleep-table').load('{!! route('Ajax.LoadTodaySleepsView') !!}', function() {
                                $('#wakeUpModal').find('button.btn-primary').empty().text('Save');
                                $('#wakeUpModal').modal('hide');
                                $('#wakeUpModal').find('button.btn-primary').prop('disabled', false);
                                autoload();
                            });
                        });

                    }
                });
            });
        };

        function cancelSleepClick() {
            $('#cancel-sleep-button').append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>')
            $('#cancel-sleep-button').prop('disabled', true);
            clearTimeout(timeout_id);
            $.post("{{ route('Ajax.CancelSleep') }}", {}, function () {
                console.log('CancelSleep returned.');

                $('#sleep-status').load('{!! route('Ajax.LoadSleepStatusView') !!}', function() {
                    $('#cancel-sleep-button').prop('disabled', false);
                    autoload();
                });
            });
        }

        function autoload() {
            timeout_id = setTimeout(function () {
                $('#age-weight-height').load('{!! route('Ajax.LoadAgeWeightHeightView') !!}', function() {autotask_check(Math.pow(2, 0))});
                $('#meal-snapshot').load('{!! route('Ajax.LoadMealSnapshotView') !!}', function() {autotask_check(Math.pow(2, 1))});
                $('#today-meals-table').load('{!! route('Ajax.LoadTodayMealsView') !!}', function() {autotask_check(Math.pow(2, 2))});
                $('#sleep-status').load('{!! route('Ajax.LoadSleepStatusView') !!}', function() {autotask_check(Math.pow(2, 3))});
                $('#sleep-snapshot').load('{!! route('Ajax.LoadSleepSnapshotView') !!}', function() {autotask_check(Math.pow(2, 4))});
                $('#today-sleep-table').load('{!! route('Ajax.LoadTodaySleepsView') !!}', function() {autotask_check(Math.pow(2, 5))});
            }, 30000);
        }

        function autotask_check(value) {
            autotask_checker += value;
            if (autotask_checker == Math.pow(2, 6) - 1) {
                autotask_checker = 0;
                autoload();
            }
        }
    </script>
@endsection
