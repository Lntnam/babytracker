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
            <div class="col-12" id="notifications">
                @include('sub.notifications')
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
                            <th colspan="2">Yesterday
                                <small>{{ $yesterday_total_meal }}ml</small>
                            </th>
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
                <div class="col-12">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>Sleep</th>
                            <th>Wake up</th>
                            <th>Duration</th>
                        </tr>
                        </thead>
                        <tbody id="today-sleep-table">
                        @include('sub.sleeps_table', ['sleep_list' => $today_sleeps])
                        </tbody>
                    </table>
                </div>
                {{--<div class="col-6">--}}
                    {{--<table class="table table-sm">--}}
                        {{--<thead>--}}
                        {{--<tr>--}}
                            {{--<th colspan="2">Today</th>--}}
                        {{--</tr>--}}
                        {{--</thead>--}}
                        {{--<tbody id="today-sleep-table">--}}
                        {{--@include('sub.sleeps_table', ['sleep_list' => $today_sleeps])--}}
                        {{--</tbody>--}}
                    {{--</table>--}}
                {{--</div>--}}
                {{--<div class="col-6">--}}
                    {{--<table class="table table-sm">--}}
                        {{--<thead>--}}
                        {{--<tr>--}}
                            {{--<th colspan="2">Yesterday--}}
                                {{--<small>{{ $yesterday_total_sleep->hours }}h {{ $yesterday_total_sleep->minutes }}m--}}
                                {{--</small>--}}
                            {{--</th>--}}
                        {{--</tr>--}}
                        {{--</thead>--}}
                        {{--<tbody>--}}
                        {{--@include('sub.sleeps_table', ['sleep_list' => $yesterday_sleeps])--}}
                        {{--</tbody>--}}
                    {{--</table>--}}
                {{--</div>--}}
            </div>
        </div>
        <div class="tab-pane" id="next-day" role="tabpanel">
            @include('sub.end_day')
        </div>
    </div>

    <div class="modal fade" id="changeWeightModal" tabindex="-1" role="dialog" aria-labelledby="Edit Weight"
         aria-hidden="true">
        @include('modals.change_weight_modal')
    </div>

    <div class="modal fade" id="addMealModal" tabindex="-1" role="dialog" aria-labelledby="Add Meal" aria-hidden="true">
        @include('modals.add_meal_modal')
    </div>

    <div class="modal fade" id="addSleepModal" tabindex="-1" role="dialog" aria-labelledby="Sleeping"
         aria-hidden="true">
        @include('modals.add_sleep_modal')
    </div>

    <div class="modal fade" id="wakeUpModal" tabindex="-1" role="dialog" aria-labelledby="Wake Up" aria-hidden="true">
        @include('modals.wake_up_modal')
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
        var clockpicker_options = {
            autoclose: true
        };

        $(document).ready(function () {
            initClockpicker();

            assignEvents();

            autoload();
            autoloadNotifications();
        });

        function autoload() {
            timeout_id = setTimeout(function () {
                $('#age-weight-height').load('{!! route('Ajax.LoadAgeWeightHeightView') !!}', function () {
                    post_autload(Math.pow(2, 0))
                });
                $('#meal-snapshot').load('{!! route('Ajax.LoadMealSnapshotView') !!}', function () {
                    post_autload(Math.pow(2, 1))
                });
                $('#today-meals-table').load('{!! route('Ajax.LoadTodayMealsView') !!}', function () {
                    post_autload(Math.pow(2, 2))
                });
                $('#sleep-status').load('{!! route('Ajax.LoadSleepStatusView') !!}', function () {
                    post_autload(Math.pow(2, 3))
                });
                $('#sleep-snapshot').load('{!! route('Ajax.LoadSleepSnapshotView') !!}', function () {
                    post_autload(Math.pow(2, 4))
                });
                $('#today-sleep-table').load('{!! route('Ajax.LoadTodaySleepsView') !!}', function () {
                    post_autload(Math.pow(2, 5))
                });

                $('#next-day').load('{!! route('Ajax.LoadEndDayView') !!}', function () {
                    post_autload(Math.pow(2, 6))
                });

                {{--$('#addMealModal').load('{!! route('Ajax.LoadModal', ['name' => 'add_meal_modal']) !!}', function () {--}}
                {{--post_autload(Math.pow(2, 6))--}}
                {{--});--}}
                {{--$('#addSleepModal').load('{!! route('Ajax.LoadModal', ['name' => 'add_sleep_modal']) !!}', function () {--}}
                {{--post_autload(Math.pow(2, 7))--}}
                {{--});--}}
                {{--$('#changeWeightModal').load('{!! route('Ajax.LoadModal', ['name' => 'change_weight_modal']) !!}', function () {--}}
                {{--post_autload(Math.pow(2, 8))--}}
                {{--});--}}
                {{--$('#wakeUpModal').load('{!! route('Ajax.LoadModal', ['name' => 'wake_up_modal']) !!}', function () {--}}
                {{--post_autload(Math.pow(2, 9))--}}
                {{--});--}}

            }, 30000);
        }

        function autoloadNotifications() {
            timeout_id = setTimeout(function () {
                $('#notifications').load('{!! route('Ajax.LoadNotifications') !!}', function () {
                    autoloadNotifications()
                });
            }, 600000);
        }

        function post_autload(value) {
            autotask_checker += value;

//            initClockpicker();
            assignEvents();

            if (autotask_checker == 0
                + Math.pow(2, 0)
                + Math.pow(2, 1)
                + Math.pow(2, 2)
                + Math.pow(2, 3)
                + Math.pow(2, 4)
                + Math.pow(2, 5)
                + Math.pow(2, 6)
            ) {
                autotask_checker = 0;
                autoload();
            }
        }

        function initClockpicker() {
            $('#addMealModal').on('show.bs.modal', function (e) {
                $.ajax({
                    type: "GET",
                    url: '{!! route('Ajax.GetServerTime') !!}',
                    complete: function(data) {
                        $('#meal-time-input').val(data.responseText);
                    }
                });
            });

            $('#addSleepModal').on('show.bs.modal', function (e) {
                $.ajax({
                    type: "GET",
                    url: '{!! route('Ajax.GetServerTime') !!}',
                    complete: function(data) {
                        $('#sleep-time-input').val(data.responseText);
                    }
                });
            });

            $('#wakeUpModal').on('show.bs.modal', function (e) {
                $.ajax({
                    type: "GET",
                    url: '{!! route('Ajax.GetServerTime') !!}',
                    complete: function(data) {
                        $('#wake-time-input').val(data.responseText);
                    }
                });
            });

            $('#meal-time-input').clockpicker(clockpicker_options);
            $('#sleep-time-input').clockpicker(clockpicker_options);
            $('#wake-time-input').clockpicker(clockpicker_options);
        }

        function assignEvents() {

            $('.alert').on('closed.bs.alert', closeAlert);

            $('#changeWeightModal').find('button.btn-primary').off('click').on('click', saveWeight);
            $('#addMealModal').find('button.btn-primary').off('click').on('click', saveMeal);
            $('#addSleepModal').find('button.btn-primary').off('click').on('click', saveSleep);
            $('#wakeUpModal').find('button.btn-primary').off('click').on('click', saveSleep);

            $('#cancel-sleep-button').off('click').on('click', cancelSleep);
        }

        function closeAlert() {
            $.post("{{ route('Ajax.CloseNotification') }}", {id: $(this).find('input').val()});
        }

        function saveWeight() {
            $(this).append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>')
            $(this).prop('disabled', true);
            clearTimeout(timeout_id);
            $.post("{{ route('Ajax.SaveMeasurements') }}", {
                weight: $('#weight-input').val(),
                height: $('#height-input').val()
            }, function () {
                $('#age-weight-height').load('{!! route('Ajax.LoadAgeWeightHeightView') !!}', function () {
                    $('#changeWeightModal').find('button.btn-primary').prop('disabled', false).empty().text('Save');
                    $('#changeWeightModal').modal('hide');

                    assignEvents();
                    autoload();
                });
            });
        }

        function saveMeal() {
            $(this).append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>')
            $(this).prop('disabled', true);
            clearTimeout(timeout_id);
            $.post("{{ route('Ajax.AddMeal') }}", {
                value: $('#meal-input').val(),
                type: $('#bottle-fed').prop("checked") ? $('#bottle-fed').attr('value') : $('#breast-fed').attr('value'),
                at: $('#meal-time-input').val()
            }, function () {
                $('#meal-snapshot').load('{!! route('Ajax.LoadMealSnapshotView') !!}', function () {
                    $('#today-meals-table').load('{!! route('Ajax.LoadTodayMealsView') !!}', function () {
                        $('#addMealModal').find('button.btn-primary').prop('disabled', false).empty().text('Save');
                        $('#addMealModal').modal('hide');

                        assignEvents();
                        autoload();
                    });
                });
            });
        }

        function saveSleep() {
            $(this).append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>')
            $(this).prop('disabled', true);
            clearTimeout(timeout_id);
            $.post("{{ route('Ajax.ToggleSleep') }}", {
                sleep_time: $('#sleep-time-input').val(),
                wake_time: $('#wake-time-input').val()
            }, function (data) {
                $('#sleep-status').load('{!! route('Ajax.LoadSleepStatusView') !!}', function () {
                    if (data.sleeping) { // from wake to sleep
                        $('#addSleepModal').find('button.btn-primary').prop('disabled', false).empty().text('Save');
                        $('#addSleepModal').modal('hide');

                        assignEvents();
                        autoload();
                    }
                    else { // from sleep to wake
                        $('#sleep-snapshot').load('{!! route('Ajax.LoadSleepSnapshotView') !!}', function () {
                            $('#today-sleep-table').load('{!! route('Ajax.LoadTodaySleepsView') !!}', function () {
                                $('#wakeUpModal').find('button.btn-primary').prop('disabled', false).empty().text('Save');
                                $('#wakeUpModal').modal('hide');

                                assignEvents();
                                autoload();
                            });
                        });

                    }
                });
            });
        }

        function cancelSleep() {
            if (!confirm('Are you sure')) return;

            $('#cancel-sleep-button').append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>')
            $('#cancel-sleep-button').prop('disabled', true);
            clearTimeout(timeout_id);
            $.post("{{ route('Ajax.CancelSleep') }}", {}, function () {
                $('#sleep-status').load('{!! route('Ajax.LoadSleepStatusView') !!}', function () {
                    assignEvents();
                    autoload();
                });
            });
        }
    </script>
@endsection
