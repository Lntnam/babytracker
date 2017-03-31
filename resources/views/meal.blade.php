@extends('layouts.app')

@section('title', 'Meal Report')

@section('content')
    <div class="main-info">
        <h3>Meal Reports</h3>
    </div>

    <!-- ################## -->
    <!-- PAST 10 DAYS -->
    <div class="main-info">
        <h4>Past 4 weeks</h4>
    </div>

    <!-- 28 Days chart -->
    <div class="row">
        <div id="ten-day-chart" class="col-12" style="height: 300px"></div>
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

    <!-- ################## -->
    <!-- Time Average -->
    <div class="main-info">
        <h4>Meal Amount Analysis</h4>
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
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            var ten_day_data = google.visualization.arrayToDataTable([
                ['Age', 'Amount'],
                    @foreach ($past_records as $record)
                [{{ (new Carbon($dob))->diffInDays(new Carbon($record->day))  }}, {{ $record->meal }}],
                @endforeach
            ]);

            var options = {
                vAxis: {title: 'Intake (ml)'},
                hAxis: {
                    title: 'Age (days)',
                    maxValue: {{ (new Carbon($dob))->diffInDays(\Carbon\Carbon::today()->addDay(2)) }}
                },
                legend: 'none',
                chartArea: {left: '10%', top: '10%', width: '90%', height: '80%'},
                trendlines: {0: {
                    type: 'exponential'
                }}
            };

            var ten_day_chart = new google.visualization.ScatterChart(document.getElementById('ten-day-chart'));
            ten_day_chart.draw(ten_day_data, options);
        }
    </script>
@endsection
