@foreach($message_class as $mc_k => $mc)
    @if ($messages[$mc_k])
    <div class="alert alert-{!! $mc !!}" role="alert">
        {!! implode('<br>', $messages[$mc_k]) !!}
    </div>
    @endif
@endforeach