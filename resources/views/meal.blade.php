@extends('layouts.app')

@section('title', 'Meal Report')

@section('content')
    <div class="main-info">
        <h4>Meal Reports</h4>
    </div>

    <h5 class="text-center">Daily Total Intake</h5>

    <div class="row">
        <div id="daily-intake-chart" class="col-12" style="height: 300px"></div>
    </div>

    <div class="row">
        @for ($i = 0; $i < min(10, count($past_records)); $i++)
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3">
                <div class="row report-cell">
                    <div class="col-6"><strong>{{ (new Carbon($past_records[$i]->day))->format('M-d') }}</strong></div>
                    <div class="col-6">{{ $past_records[$i]->meal }}ml</div>
                </div>
            </div>
        @endfor
    </div>

    <h5 class="text-center">Time to Time Comparison</h5>

    <div class="row">
        <div id="time-comparison-chart" class="col-12" style="height: 300px"></div>
    </div>

    <div class="row">
        @foreach ($meals_by_time as $k => $v)
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3">
                <div class="row report-cell">
                    <div class="col-7"><strong>{{ $k }} hrs</strong></div>
                    <div class="col-5">{{ \App\Utilities::findArrayMedian(array_values($v))  }}ml</div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Back to home -->
    <div class="row">
        <div class="mx-auto" style="width: 28px">
            <a href="{!! route('dashboard') !!}" class="button btn-secondary"><i class="fa fa-home fa-2x"
                                                                                 aria-hidden="true"></i></a>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            var daily_intake_data = google.visualization.arrayToDataTable([
                ['Age', 'Amount'],
                    @foreach ($past_records as $record)
                [{{ $dob->diffInDays(new Carbon($record->day))  }}, {{ $record->meal }}],
                @endforeach
            ]);

            var time_comparison_data = google.visualization.arrayToDataTable([
                ['Time of Day', 'Amount'],
                    @foreach ($meals_by_time as $time_block => $meals)
                ['{{ $time_block }}', {{ \App\Utilities::findArrayMedian(array_values($meals))  }}],
                @endforeach
            ]);

            var scatter_options = {
                vAxis: {title: 'Intake (ml)'},
                hAxis: {
                    title: 'Age (days)',
                    format: '#0',
                    maxValue: {{ (new Carbon($dob))->diffInDays(\Carbon\Carbon::today()->addDay(2)) }}
                },
                legend: {position: 'none'},
                chartArea: {left: '10%', top: '5%', width: '85%', height: '80%'},
                trendlines: {
                    0: {
                        type: 'exponential'
                    }
                }
            };
            var column_options = {
                vAxis: {title: 'Intake Median (ml)'},
                hAxis: {title: 'Time of day'},
                legend: {position: 'none'},
                chartArea: {left: '10%', top: '5%', width: '85%', height: '80%'},
            };

            var daily_intake_chart = new google.visualization.ScatterChart(document.getElementById('daily-intake-chart'));
            var time_comparison_chart = new google.charts.Bar(document.getElementById('time-comparison-chart'));
            daily_intake_chart.draw(daily_intake_data, scatter_options);
            time_comparison_chart.draw(time_comparison_data, google.charts.Bar.convertOptions(column_options));
        }
    </script>
@endsection
