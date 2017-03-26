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
    <!-- Analysis -->
    <div class="main-info">
        <h4>10 Days Analysis</h4>
    </div>

    <!-- Median -->
    <div class="row">
        <table class="table table-sm">
            <thead class="thead-default">
            <tr>
                @foreach (array_keys($analysis) as $block_name)
                    <th style="width: 25%">{{ $block_name }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            <tr>
               <th scope="row" colspan="4" class="text-center">Sleep Median</th>
            </tr>
            <tr>
                @foreach ($analysis as $data)
                    <td>{{ floor($data['sleep_median']->minutes / 60) . 'h ' . ($data['sleep_median']->minutes % 60) . 'm' }}</td>
                @endforeach
            </tr>
            <tr>
                <th scope="row" colspan="4" class="text-center">Awake Median</th>
            </tr>
            <tr>
                @foreach ($analysis as $data)
                    <td>{{ floor($data['awake_median']->minutes / 60) . 'h ' . ($data['awake_median']->minutes % 60) . 'm' }}</td>
                @endforeach
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Percentage chart -->
    <div class="main-info">
        <h5>Sleep Time</h5>
    </div>
    <div class="row">
        <div id="sleep-time-chart" class="col-12" style="height: 350px"></div>
    </div>
    <div class="main-info">
        <h5>Awake Time</h5>
    </div>
    <div class="row">
        <div id="awake-time-chart" class="col-12" style="height: 350px"></div>
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
        google.charts.load("current", {packages: ["corechart"]});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            var sleep_time_data = google.visualization.arrayToDataTable([
                ['Part of Day', 'Duration'],
                    @foreach ($analysis as $block => $data)
                ['{{ $block }}', {{ $data['sleep_sum'] }}],
                @endforeach
            ]);

            var awake_time_data = google.visualization.arrayToDataTable([
                ['Part of Day', 'Duration'],
                    @foreach ($analysis as $block => $data)
                ['{{ $block }}', {{ $data['awake_sum'] }}],
                @endforeach
            ]);

            var ten_day_data = google.visualization.arrayToDataTable([
                ['Day', 'Amount'],
                    @for ($i = count($past_records) - 1; $i >=0 ; $i--)
                ['{{ (new Carbon($past_records[$i]->day))->format('j/n')  }}', {{ $past_records[$i]->sleep }}],
                @endfor
            ]);

            var pie_options = {
                pieHole: 0.4,
                chartArea: {left: 0, top: 0, width: '100%', height: '80%'},
                legend: {position: 'bottom'},
            };

            var line_options = {
                curveType: 'function',
                legend: {position: 'none'},
                vAxis: {title: 'minute'}
            };

            var sleep_time_chart = new google.visualization.PieChart(document.getElementById('sleep-time-chart'));
            var awake_time_chart = new google.visualization.PieChart(document.getElementById('awake-time-chart'));
            var ten_day_chart = new google.visualization.LineChart(document.getElementById('ten-day-chart'));

            sleep_time_chart.draw(sleep_time_data, pie_options);
            awake_time_chart.draw(awake_time_data, pie_options);
            ten_day_chart.draw(ten_day_data, line_options);
        }
    </script>
@endsection
