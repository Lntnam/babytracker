@if (!empty($sleeping_record))
    <div class="row"> <!-- current sleeping status -->
        <div class="col-12 text-center">
            <p class="lead">sleeping from
                <mark
                        id="sleep-from">{{ Utilities::displayTimeString($sleeping_record->sleep) }} [<span
                            id="sleeping-duration">{{ Utilities::displayTimeDuration($sleeping_record->sleep, Carbon::now()) }}</span>]
                </mark>
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
            <button id="cancel-sleep-button" type="button" class="btn btn-danger btn-block"
                    onclick="cancelSleepClick()"><i
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
