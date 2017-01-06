@extends('crud::blank')

@section('content')

<script>
    window.parent.$('iframe.crud_langs').each(function(elem){
        alert('frame');
        //$(elem).contents().find('form').submit();
        //var form = $(elem).contents().find('form');


        $(this).contents().find('form').submit();

        //if ($(elem).contents().find('form').length)
        //    alert('frame submitted');

    });
</script>

@endsection