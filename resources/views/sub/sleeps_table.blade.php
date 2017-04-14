@foreach ($sleep_list as $sleep)
    <tr>
        <td>{{ (new Carbon($sleep->sleep))->format('H:i') }}</td>
        <td>{{ (new Carbon($sleep->wake))->format('H:i') }}</td>
        <th scope="row">{{  $sleep->hours }}h {{ $sleep->minutes }}m</th>
    </tr>
@endforeach
