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

    @if (!empty($WHO_table))
        <div class="main-info">
            <h4>{{ $next_milestone / 30 }} Month WHO Standard</h4>
        </div>
        <div class="row">
            <p class="lead text-center col-12">
                @for ($i = 0; $i < count($WHO_table); $i ++)
                    @if ($weight <= $WHO_table[$i] && ($i == 0 || $weight >= $WHO_table[$i-1]))
                        <i class="fa fa-hand-o-right" aria-hidden="true"></i> <span
                                class="badge badge-primary">{{ $weight }}</span>
                    @endif
                    <span class="badge badge-{{ $i==0 || $i==4 ? 'danger' :
                        ($i==1 || $i==3 ? 'warning' : 'success') }}">{{ $WHO_table[$i] }}</span>
                    @if ($i < count($WHO_table) - 1)
                        <i class="fa fa-angle-left" aria-hidden="true"></i>
                    @endif
                    @if ($weight > $WHO_table[$i])
                        <i class="fa fa-hand-o-right" aria-hidden="true"></i> <span
                                class="badge badge-primary">{{ $weight }}</span>
                    @endif
                @endfor
            </p>
        </div>
    @endif

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
                ['Age', 'Weight', 'L2 Under', 'L1 Under', 'Standard', 'L1 Over', 'L2 Over'],
                    @foreach ($records as $record)
                [{{ $dob->diffInDays(new Carbon($record->day)) }}, {{ $record->weight }}, null, null, null, null, null],
                    @endforeach
                    @if (!empty($WHO_table))
                [{{ $next_milestone }}, null, {{ $WHO_table[0] }}, {{ $WHO_table[1] }}, {{ $WHO_table[2] }}, {{ $WHO_table[3] }}, {{ $WHO_table[4] }}]
                @endif
            ]);

            var options = {
                curveType: 'function',
                legend: 'none',
                chartArea: {left: '10%', top: '10%', width: '90%', height: '80%'},
                vAxis: {title: 'kg'}
            };

            var trend_options = {
                vAxis: {
                    title: 'Weight (kg)',
                },
                hAxis: {
                    title: 'Age (days)',
                    format: '#0',
                },
                legend: 'none',
                chartArea: {left: '10%', top: '10%', width: '85%', height: '80%'},
                colors: ['#0275D8', '#D9534F', '#F0AD4E', '#5CB85C', '#F0AD4E', '#D9534F'],
                trendlines: {0: {}}
            };

            var weekly_chart = new google.visualization.LineChart(document.getElementById('weekly-chart'));
            var trend_chart = new google.visualization.ScatterChart(document.getElementById('weight-trend-chart'));

            weekly_chart.draw(week_weight_data, options);
            trend_chart.draw(weight_trend_data, trend_options);
        }
    </script>
@endsection
