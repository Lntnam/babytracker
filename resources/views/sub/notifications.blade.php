@foreach ($notifications as $n)
    <div class="alert alert-{{  $n->type }} alert-dismissible fade show" role="alert">
        <input type="hidden" value="{{ $n->id }}" name="alert-id">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>{{ $n->title }}</strong> {{ $n->message }}
    </div>
@endforeach
