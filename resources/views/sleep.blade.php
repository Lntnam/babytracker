@extends('layouts.app')

@section('title', 'Sleep Report')

@section('content')
    <div class="main-info">
        <h3>Sleep Reports</h3>
    </div>

    <!-- ################## -->
    <!-- PAST 10 DAYS -->
    <div class="main-info">
        <h4>Past 10 days</h4>
    </div>

    <div class="row">
        @foreach ($past_records as $day)
        <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3">
            <div class="row report-cell">
                <div class="col-6"><strong>{{ (new Carbon($day->day))->format('M-d') }}</strong></div>
                <div class="col-6">{{ floor($day->sleep / 60).'h '.($day->sleep % 60).'m' }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- 10 Days chart -->
    <div class="row">
        <div id="ten-day-chart" class="col-12" style="height: 300px"></div>
    </div>

    <!-- ################## -->
    <!-- Time Average -->
    <div class="main-info">
        <h4>10 Days Time Average</h4>
    </div>

    <div class="row">
        @foreach ($sleeps_by_time as $k => $v)
        <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3">
            <div class="row report-cell">
                <div class="col-6"><strong>{{ $k }}</strong></div>
                <div class="col-6">{{ floor($v / 60).'h '.($v % 60).'m'  }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Time chart -->
    <div class="row">
        <div id="time-chart" class="col-12" style="height: 300px"></div>
    </div>

    <!-- Back to home -->
    <div class="row">
        <div class="mx-auto" style="width: 28px">
            <a href="{!! route('dashboard') !!}" class="button btn-secondary"><i class="fa fa-home fa-2x" aria-hidden="true"></i></a>
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
                ['Day', 'Amount'],
                @for ($i = count($past_records) - 1; $i >=0 ; $i--)
                    ['{{ (new Carbon($past_records[$i]->day))->format('j/n')  }}', {{ $past_records[$i]->sleep }}],
                @endfor
            ]);

            var time_data = google.visualization.arrayToDataTable([
                ['Day', 'Amount'],
                @foreach ($sleeps_by_time as $k => $v)
                    ['{{ $k }}', {{ $v }}],
                @endforeach
            ]);

            var options = {
                curveType: 'function',
                legend: {position: 'none'},
                vAxis: {title: 'minute'}
            };

            var ten_day_chart = new google.visualization.LineChart(document.getElementById('ten-day-chart'));
            var time_chart = new google.visualization.LineChart(document.getElementById('time-chart'));

            ten_day_chart.draw(ten_day_data, options);
            time_chart.draw(time_data, options);
        }
    </script>
@endsection
