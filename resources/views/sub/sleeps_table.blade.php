<table class="table table-sm">
    <thead>
    <tr>
        <th colspan="2">Today</th>
    </tr>
    </thead>
    <tbody id="sleeps-today-body">
    @foreach ($sleep_list as $sleep)
        <tr>
            <th scope="row">{{ (new Carbon($sleep->sleep))->format('H:i') }}</th>
            <td>{{  $sleep->hours }}h {{ $sleep->minutes }}m</td>
        </tr>
    @endforeach
    </tbody>
</table>