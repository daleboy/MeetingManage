$(document).scrollTop(0);
var scrollY = 0;
var maxY = $(document).height() - $(window).height();
var direction = 0;
$(function () {
    ShowTime();
    setTimeout('RefreshPage()', 60000);
    $('#already_sign_in').html(parseInt($('#already_sign_in_div li').length));
    
    //ScrollWindow();
});

function ShowTime()
{
    var d = new Date();
    var nowhour = d.getHours() > 9 ? d.getHours().toString() : '0' + d.getHours();  
    var nowminu = d.getMinutes() > 9 ? d.getMinutes().toString() : '0' + d.getMinutes();
    var nowsec = d.getSeconds() > 9 ? d.getSeconds().toString() : '0' + d.getSeconds(); 
    var today = d.toLocaleDateString().replace(/\//g, '-');
    $('#show_time').html(today + ' ' + nowhour + ':' + nowminu + ':' + nowsec);
    setTimeout(ShowTime, 1000);
}

function RefreshPage()
{
    window.location.reload();
}

function ScrollWindow()
{
    if (direction == 0) {
        //window.scrollBy(0, +1);
        $(document).scrollTop(scrollY);
        if (scrollY > maxY)
            direction = 1;
        else
            scrollY = scrollY + 1;
    }
    else {
        //window.scrollBy(0, -1);
        $(document).scrollTop(scrollY);
        if (scrollY == 0)
            direction = 0;
        else
            scrollY = scrollY - 1;
    }
    setTimeout("ScrollWindow()", 50);
}
