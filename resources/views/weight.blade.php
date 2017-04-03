@extends('layouts.app')

@section('title', 'Weight Report')

@section('content')
    <div class="main-info">
        <h3>Weight Comparison with WHO's Recommendation</h3>
    </div>

    <h5 class="text-center">As at Day {{ $max_age }}</h5>

    @if (!empty($current_zscores))
        <div class="row justify-content-center no-gutters">
            @for ($i = 0; $i < count($current_zscores); $i ++)
                @if ($current_weight <= $current_zscores[$i] && ($i == 0 || $current_weight > $current_zscores[$i-1]))
                    <div class="col-auto text-center"><span class="badge badge-primary"><h6>{{ $current_weight }}kg</h6></span>
                    </div>
                @endif
                <div class="col-1 text-center">
                <span class="badge badge-{{
                    abs($i + 1 - 5) >= 3 ? 'danger' : (
                    abs($i + 1 - 5) >= 1 ? 'warning' : 'success')
                    }}">{{ round($current_zscores[$i], 1) }}</span>
                </div>
                @if ($i == 4 && $current_weight > $current_zscores[$i])
                    <div class="col-auto text-center"><span class="badge badge-primary"><h6>{{ $current_weight }}kg</h6></span>
                    </div>
                @endif
            @endfor
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            <strong>No Data!</strong> Check Z-score text file in storage/app.
        </div>
    @endif

    <h5 class="text-center pt-3">History</h5>

    <div class="row">
        <div class="col-12" id="weight-trend-chart" style="height: 350px"></div>
    </div>

    <div class="row text-primary">
        @php
            $count = count($weight_records);
            $half = ceil($count / 2);
            $i = 0;
            $col = 1;
        @endphp
        @while ($i < $count && ($col == 2 || $i < $half))
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3">
                <div class="row report-cell">
                    <div class="col-6">
                        <strong>{{ (new Carbon($dob))->diffInDays(new Carbon($weight_records[$i]->day)) }}
                            days</strong></div>
                    <div class="col-6">{{ round($weight_records[$i]->weight, 1) }}kg</div>
                </div>
            </div>
            @php
                if ($col === 1) {
                $i += $half;
                $col = 2;
                }
                else {
                $i -= $half - 1;
                $col = 1;
                }
            @endphp
        @endwhile
    </div>

    <h5 class="text-center pt-3">Next Month Projection</h5>

    <div class="row">
        <div class="col-12" id="projection-chart" style="height: 350px"></div>
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
            var weight_trend_data = google.visualization.arrayToDataTable([
                ['Age', 'SD-4', 'SD-3', 'SD-2', 'SD-1', 'SD0', 'SD+1', 'SD+2', 'SD+3', 'SD+4', 'Weight'],
                    @foreach ($zscore_table as $age => $values)
                [
                    {{ $age }},
                    @foreach ($values as $val)
                    {{ $val }},
                    @endforeach
                            @if (!empty($weight_array[$age]))
                            {{ $weight_array[$age] }}
                            @else
                        null
                    @endif
                ],
                @endforeach
            ]);

            var projection_data = google.visualization.arrayToDataTable([
                ['Age', 'Weight', 'SD-4', 'SD-3', 'SD-2', 'SD-1', 'SD0', 'SD+1', 'SD+2', 'SD+3', 'SD+4'],
                @foreach ($weight_array as $age => $weight)
                [{{ $age }}, {{ $weight }}, null, null, null, null, null, null, null, null, null],
                    @endforeach
                @if (!empty($previous_zscore_milestone))
                [{{ $past_milestone }}, null
                    @foreach ($previous_zscore_milestone as $value),{{ $value }}@endforeach
                ],
                @endif
                [{{ $next_milestone }}, null
                    @foreach ($next_zscore_milestone as $value),{{ $value }}@endforeach
                ],
            ]);

            var trend_options = {
                vAxis: {
                    title: 'Weight (kg)',
                },
                hAxis: {
                    title: 'Age (days)',
                    format: '#0',
                    viewWindowMode: 'explicit',
                    viewWindow: {
                        max: {{ $max_age + $weight_frequency }},
                        min: {{ $min_age - $weight_frequency }},
                    },
                },
                legend: 'none',
                chartArea: {left: '10%', top: '5%', width: '85%', height: '80%'},
                colors: ['#D9534F', '#D9534F', '#F0AD4E', '#F0AD4E', '#5CB85C', '#F0AD4E', '#F0AD4E', '#D9534F', '#D9534F', '#0275D8'],
                curveType: 'function',
                trendlines: {
                    9: {type: 'exponential', lineWidth: 5, opacity: 0.5}
                }
            };

            var projection_options = {
                vAxis: {
                    title: 'Weight (kg)',
                },
                hAxis: {
                    title: 'Age (days)',
                    format: '#0',
                    viewWindowMode: 'explicit',
                    viewWindow: {
                        max: {{ $next_milestone + 15 }},
                        min: {{ $past_milestone - 15 }},
                    },
                    gridlines: {
                        count: 5,
                    },
                },
                legend: 'none',
                chartArea: {left: '15%', top: '10%', width: '80%', height: '80%'},
                colors: ['#0275D8', '#D9534F', '#D9534F', '#F0AD4E', '#F0AD4E', '#5CB85C', '#F0AD4E', '#F0AD4E', '#D9534F', '#D9534F'],
                trendlines: {
                    0: {type: 'exponential', lineWidth: 5, opacity: 0.5}
                }
            };

            var trend_chart = new google.visualization.LineChart(document.getElementById('weight-trend-chart'));
            var projection_chart = new google.visualization.ScatterChart(document.getElementById('projection-chart'));

            trend_chart.draw(weight_trend_data, trend_options);
            projection_chart.draw(projection_data, projection_options);
        }
    </script>
@endsection
