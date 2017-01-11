$(function () {
    $('form.content_type_delete').submit(function () {
        if (!confirm('Дійсно видалити?'))
            return false;
        if (!confirm('Буде видалено модель, тип контенту та всі його поля. Продовжити?'))
            return false;
    });


    /*
     * Multilevel menu
     * http://bootsnipp.com/snippets/featured/multi-level-navbar-menu
     * */
    $('.navbar a.dropdown-toggle').on('click', function(e) {
        e.preventDefault();

        var $el = $(this);
        var $parent = $(this).offsetParent(".dropdown-menu");
        $(this).parent("li").toggleClass('open');

        if(!$parent.parent().hasClass('nav')) {
            $el.next().css({"top": $el[0].offsetTop, "left": $parent.outerWidth() - 4});
        }

        $('.nav li.open').not($(this).parents("li")).removeClass("open");

        return false;
    });
});



function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
}

/*$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});*/