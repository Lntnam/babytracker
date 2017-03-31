@extends('layouts.app')

@section('title', 'Weight Report')

@section('content')
    <div class="main-info">
        <h3>Weight Reports</h3>
    </div>

    <!-- ################## -->
    <!-- WEEKLY -->
    <div class="main-info">
        <h4>Weight By Week</h4>
    </div>

    <div class="row">
        @foreach ($weight_weeks as $week => $weight)
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3">
                <div class="row report-cell">
                    <div class="col-6"><strong>W{{ $week }}</strong></div>
                    <div class="col-6">{{ $weight }}kg</div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Weekly chart -->
    <div class="row">
        <div id="weekly-chart" class="col-12" style="height: 300px"></div>
    </div>

    <!-- ################## -->
    <!-- Trend -->
    <div class="main-info">
        <h4>Weight Trend</h4>
    </div>

    <div class="row">
        @foreach ($records as $record)
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3">
                <div class="row report-cell">
                    <div class="col-6"><strong>{{ (new Carbon($record->day))->format('M-d') }}</strong></div>
                    <div class="col-6">{{ round($record->weight, 1) }}kg</div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Weekly increment chart -->
    <div class="row">
        <div id="weight-trend-chart" class="col-12" style="height: 300px"></div>
    </div>

    <!-- ################## -->
    <!-- Increment -->
    <div class="main-info">
        <h4>Increment Analysis</h4>
    </div>

    <div class="row">
        @foreach ($increment_analysis as $key => $value)
            <div class="col-6 col-sm-6 col-md-3 col-lg-3">
                <div class="row report-cell">
                    <div class="col-6"><strong>{{ $key }}</strong></div>
                    <div class="col-6">{{ round($value * 1000) }}g</div>
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
            var week_weight_data = google.visualization.arrayToDataTable([
                ['Week', 'Weight'],
                    @foreach ($weight_weeks as $week => $weight)
                ['W{{ $week  }}', {{ round($weight, 1) }}],
                @endforeach
            ]);

            var weight_trend_data = google.visualization.arrayToDataTable([
                ['Age', 'Weight'],
                    @foreach ($records as $record)
                [{{ (new Carbon($dob))->diffInDays(new Carbon($record->day)) }}, {{ $record->weight }}],
                @endforeach
            ]);

            var options = {
                curveType: 'function',
                legend: 'none',
                chartArea: {left: '10%', top: '10%', width: '90%', height: '80%'},
                vAxis: {title: 'kg'}
            };

            var trend_options = {
                vAxis: {title: 'Weight (kg)'},
                hAxis: {title: 'Age (days)'},
                legend: 'none',
                chartArea: {left: '10%', top: '10%', width: '90%', height: '80%'},
                trendlines: {0: {}}
            };

            var weekly_chart = new google.visualization.LineChart(document.getElementById('weekly-chart'));
            var trend_chart = new google.visualization.ScatterChart(document.getElementById('weight-trend-chart'));

            weekly_chart.draw(week_weight_data, options);
            trend_chart.draw(weight_trend_data, trend_options);
        }
    </script>
@endsection
