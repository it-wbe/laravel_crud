@extends('crud::layout')

@section('title', 'CRUD')
@section('header', 'CRUD')

@section('content')
    {{-- for frames parent --}}
    <script>
        var iframe_submitted_form_count = 0;
        var iframe_form_count = "{{ count($iframe_urls)-1 }}";
        var form_submit = false;

        function check_frames_loaded() {
            //alert('one frame loaded');
            if (form_submit && (iframe_submitted_form_count >= iframe_form_count)) {
                iframe_submitted_form_count = 0;
                form_submit = false;
                alert('all is loaded');
                //$('div.rpd-dataform > form').submit();
            }
            parent.iframe_submitted_form_count++;
        }
    </script>


    @foreach($iframe_urls as $frame)
        <iframe frameBorder="0" class="{{ $frame['class'] }}" style="width:100%;height:0;overflow-x:hidden;" scrolling="no" onload="parent.check_frames_loaded();resizeIframe(this);" src="{{ $frame['url'] }}"></iframe>
    @endforeach
@endsection