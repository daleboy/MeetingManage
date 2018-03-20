$(function () {
    ChangeOffset();
    window.onresize = function () {
        ChangeOffset();
    }
});

function ChangeOffset() {
    $('.show_num').each(function () {
        $(this).css({ left: $(this).parent().find('img').offset().left + 77, top: $(this).parent().find('img').offset().top + 5 });
    });
}