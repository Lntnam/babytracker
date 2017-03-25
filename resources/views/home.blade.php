@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="main-info">
        <h3>{{ $name }}</h3>
        <h3>{{ Carbon::now()->toFormattedDateString()}}
            ({{ $age->weeks . 'w ' . $age->daysExcludeWeeks . 'd' }})</h3>
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

    <!-- Weight -->
    <div class="row">
        <div class="col-2">
            <h4><i class="fa fa-balance-scale fa-fw" aria-hidden="true"></i></h4>
        </div>
        <div class="col-6">
            <h4><label id="weight-value">{{ $weight }}</label>kg
                <i class="fa {{ isset($last_weight) ? ($last_weight > $weight ? 'fa-long-arrow-down text-danger' : ($last_weight < $weight ? 'fa-long-arrow-up text-success' : 'fa-long-arrow-right text-muted')) : 'fa-long-arrow-right text-muted' }}"
                   aria-hidden="true"></i>
            </h4>
        </div>
        <div class="col-4">
            <button type="button" class="btn btn-primary btn-block" data-toggle="modal"
                    data-target="#changeWeightModal">change
            </button>
        </div>
    </div>

    <!-- Meal -->
    <div class="row">
        <div class="col-2">
            <h4><i class="fa fa-cutlery fa-fw" aria-hidden="true"></i></h4>
        </div>
        <div class="col-6">
            <h4><label id="meal-value">{{ $meal }}</label>ml</h4>
            last meal @ <label id="last-meal">{{ !empty($last_meal) ? $last_meal->format('H:i') : '' }}</label>
        </div>
        <div class="col-4">
            <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#addMealModal">
                add
            </button>
        </div>
    </div>

    <!-- Sleep -->
    <div class="row">
        <div class="col-2">
            <h4><i class="fa fa-moon-o fa-fw" aria-hidden="true"></i></h4>
        </div>
        <div class="col-6">
            <h4><label id="sleep-value">{{ $sleep->hours . 'h ' . $sleep->minutes . 'm' }}</label></h4>
            @if (!empty($sleeping_record))
                sleep from <label id="sleep-from">{{ (new Carbon($sleeping_record->sleep))->format('H:i') }}</label>
            @endif
        </div>
        <div class="col-4">
            <button id="sleep-button" type="button" class="btn btn-{{ $sleeping ? 'success' : 'primary' }} btn-block"
                    data-toggle="modal" data-target="#{{ $sleeping ? 'wakeUp' : 'addSleep' }}Modal">
                {{ $sleeping ? 'wake up' : 'sleep' }}
            </button>
        </div>
    </div>

    <!-- Reports -->
    <div class="row">
        <div class="col-4">
            <a href="{!! route('MealReport') !!}" class="btn btn-info btn-block"><i class="fa fa-line-chart"
                                                                                    aria-hidden="true"></i> Meals</a>
        </div>
        <div class="col-4">
            <a href="{!! route('WeightReport') !!}" class="btn btn-info btn-block"><i class="fa fa-line-chart"
                                                                                      aria-hidden="true"></i>Weight</a>
        </div>
        <div class="col-4">
            <a href="{!! route('SleepReport') !!}" class="btn btn-info btn-block"><i class="fa fa-line-chart"
                                                                                     aria-hidden="true"></i>Sleeps</a>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <button type="button" class="btn btn-danger btn-block"><i class="fa fa-sliders" aria-hidden="true"></i>
                Config
            </button>
        </div>
        <div class="col-6">
            <a href="{{ route('CloseDay') }}" class="btn btn-danger btn-block"><i class="fa fa-hourglass-end"
                                                                                  aria-hidden="true"></i> End Day</a>
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
                            <input class="form-control" type="text" value="{{ $meal }}" id="meal-input"
                                   placeholder="Amount in ml">
                            <div class="input-group-addon">ml</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="sr-only">Full?</label>
                        <label class="custom-control custom-radio">
                            <input id="meal-full-yes" name="is_full" value="true" type="radio"
                                   class="custom-control-input" checked="checked">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Full</span>
                        </label>
                        <label class="custom-control custom-radio">
                            <input id="meal-full-no" name="is_full" value="false" type="radio"
                                   class="custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Not full</span>
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
                full: $('#meal-full-yes').prop("checked", true) ? 1 : 0,
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
    </script>
@endsection
