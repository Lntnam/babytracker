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
    <!-- Increment -->
    <div class="main-info">
        <h4>Increment By Week</h4>
    </div>

    <div class="row">
        @foreach ($increment_weeks as $week => $increment)
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3">
                <div class="row report-cell">
                    <div class="col-6"><strong>W{{ $week }}</strong></div>
                    <div class="col-6">{{ round($increment * 1000) }}g</div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Weekly increment chart -->
    <div class="row">
        <div id="weekly-increment-chart" class="col-12" style="height: 300px"></div>
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

            var week_increment_data = google.visualization.arrayToDataTable([
                ['Week', 'Increment'],
                    @foreach ($increment_weeks as $week => $increment)
                ['W{{ $week }}', {{ round($increment * 1000) }}],
                @endforeach
            ]);

            var options = {
                curveType: 'function',
                legend: {position: 'none'},
                vAxis: {title: 'kg'}
            };

            var weekly_chart = new google.visualization.LineChart(document.getElementById('weekly-chart'));
            var weekly_increment_chart = new google.visualization.LineChart(document.getElementById('weekly-increment-chart'));

            weekly_chart.draw(week_weight_data, options);

            options.vAxis = {title: 'g'};
            weekly_increment_chart.draw(week_increment_data, options);
        }
    </script>
@endsection
