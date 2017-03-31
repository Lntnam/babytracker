<tbody id="meals-today-body">
@foreach ($meal_list as $meal)
    <tr>
        <th scope="row">{{ (new Carbon($meal->at))->format('H:i') }}</th>
        <td>{{ $meal->value }}ml
            {!! $meal->feed_type == 'breast' ? '<i class="fa fa-user-o text-success" aria-hidden="true"></i>' : '' !!}
        </td>
    </tr>
@endforeach
</tbody>
