@extends('layouts.app')

@section('title', 'Sleep Report')

@section('content')
    <div class="main-info">
        <h4>Sleep Reports</h4>
    </div>

    <h5 class="text-center">Daily Total Sleep</h5>

    <div class="row">
        <div id="ten-day-chart" class="col-12" style="height: 300px"></div>
    </div>

    <div class="row">
        @for ($i = 0; $i < min(10, count($past_records)); $i++)
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3">
                <div class="row report-cell">
                    <div class="col-6"><strong>{{ (new Carbon($past_records[$i]->day))->format('M-d') }}</strong></div>
                    <div class="col-6">{{ floor($past_records[$i]->sleep / 60).'h '.($past_records[$i]->sleep % 60).'m' }}</div>
                </div>
            </div>
        @endfor
    </div>

    <h5 class="text-center">4 Weeks Median Sleep Distribution</h5>

    <!-- Median -->
    <div class="row">
        <table class="table table-sm">
            <thead class="thead-default">
            <tr>
                <th style="width: 25%">Early<br/><small class="text-muted">1am - 7am</small></th>
                <th style="width: 25%">Morning<br/><small class="text-muted">7am - 1pm</small></th>
                <th style="width: 25%">Afternoon<br/><small class="text-muted">1pm - 7pm</small></th>
                <th style="width: 25%">Evening<br/><small class="text-muted">7pm - 1am</small></th>
            </tr>
            </thead>
            <tbody>
            <tr class="table-info">
               <td scope="row" colspan="4" class="text-center">Sleep Median</td>
            </tr>
            <tr>
                @foreach ($analysis as $data)
                    <td>{{ floor($data['sleep_median']->minutes / 60) . 'h ' . ($data['sleep_median']->minutes % 60) . 'm' }}</td>
                @endforeach
            </tr>
            <tr class="table-info">
                <td scope="row" colspan="4" class="text-center">Awake Median</td>
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
        <h5>4 Weeks Total Sleep Distribution</h5>
    </div>
    <div class="row">
        <div id="sleep-time-chart" class="col-12" style="height: 350px"></div>
    </div>
    <div class="main-info">
        <h5>4 Weeks Total Awake Distribution</h5>
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
                ['Age', 'Amount'],
                    @foreach ($past_records as $record)
                [{{ (new Carbon($dob))->diffInDays(new Carbon($record->day))  }},
                    [{{ floor($record->sleep / 60) }}, {{ $record->sleep % 60 }}, 0]],
                @endforeach
            ]);

            var pie_options = {
                pieHole: 0.4,
                chartArea: {left: 0, top: 0, width: '100%', height: '80%'},
                legend: {position: 'bottom'},
                colors: ['#5C6BC0', '#03A9F4', '#E57373', '#FFA726']
            };

            var options = {
                vAxis: {
                    title: 'Sleep Duration',
                    format: 'HH:mm',
                },
                hAxis: {
                    title: 'Age (days)',
                    format: '#0',
                    maxValue: {{ (new Carbon($dob))->diffInDays(\Carbon\Carbon::today()->addDay()) }}
                },
                legend: 'none',
                colors: ['#1E88E5'],
                chartArea: {left: '15%', top: '5%', width: '80%', height: '80%'},
                trendlines: {0: {
                    type: 'exponential'
                }}
            };

            var sleep_time_chart = new google.visualization.PieChart(document.getElementById('sleep-time-chart'));
            var awake_time_chart = new google.visualization.PieChart(document.getElementById('awake-time-chart'));
            var ten_day_chart = new google.visualization.ScatterChart(document.getElementById('ten-day-chart'));

            sleep_time_chart.draw(sleep_time_data, pie_options);
            awake_time_chart.draw(awake_time_data, pie_options);
            ten_day_chart.draw(ten_day_data, options);
        }
    </script>
@endsection
