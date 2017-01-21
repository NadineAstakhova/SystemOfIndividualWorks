/**
 * Created by Nadine on 14.04.2016.
 */
$(document).ready(function (){
   $('.btn-default').click(function(){
       $('#modal').modal('show')
           .find('#modalContent')
           .load($(this).attr('value'));
   });
});

$(document).ready(function (){
    $('.btn_create').click(function(){
        $('#modalCreate').modal('show')
            .find('#modalContentCreate')
            .load($(this).attr('value'));
    });
});

$(document).ready(function (){
    $('#modalButtonGroup').click(function(){
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
    });
});

$(document).ready(function (){
    $('#modalButtonTask').click(function(){
        $('#modalTask').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
    });
});


$(document).ready(function (){
    $('#modalButtonName').click(function(){
        $('#modalUpdateName').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
    });
});

$(document).ready(function (){
    $('.modalButtonDate').click(function(){
        $('#modalDate').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
    });
});

$(document).ready(function (){
    $('.modalButtonChangeG').click(function(){
        $('#modalChangeGroup').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
    });
});


$(document).ready(function (){
    $('#modalButtonUpdateInf').click(function(){
        $('#modalUpdateInf').modal('show')
            .find('#modalContentUpdate')
            .load($(this).attr('value'));
    });
});

$(document).ready(function (){
    $('.modalUpdateStudent').click(function(){
        $('#modalUpdateStudent').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
    });
});

$(document).ready(function (){
    $('#btn_change_pass').click(function(){
        $('#modalUpdatePass').modal('show')
            .find('#modalContentUpdatePass')
            .load($(this).attr('value'));
    });
});

$('a[href^="#"]').bind('click.smoothscroll',function (e) {
    e.preventDefault();

    var target = this.hash,
        $target = $(target);

    $('html, body').stop().animate({
        'scrollTop': $target.offset().top
    }, 900, 'swing', function () {
        window.location.hash = target;
    });
});

$(window).scroll(function () {
    if ($(this).scrollTop() > 0) {
        $('#scroller').fadeIn();}
    else {$('#scroller').fadeOut();}
});
$('#scroller').click(function () {
    $('body,html').animate({scrollTop: 0}, 400); return false;
 });







