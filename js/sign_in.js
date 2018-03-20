$(function() {
    ShowTime();

    $('.sign_in').click(function () {
        var obj = $(this);
        var userName = $(this).attr('rel');
        layer.confirm('确定' + userName + '出席吗？', { icon: 3 }, function (index) {
            MyAjax.request({
                url: projectUrl + 'MyMeetingApply/ClickSignIn',
                data: {
                    'sId': $('#s_id').val(),
                    'userId': obj.prop('id')
                }
            }, function (result) {
                if (result == 1) {
                    obj.removeClass('sign_in');
                    obj.addClass('bg-red');
                    obj.html('已到');
                    $('#already_sign_in').html(parseInt($('#already_sign_in').text()) + 1);
                }
                else {
                    layer.alert('签到失败！', { icon: 2 });
                }
                layer.close(index);
            });
        });
    });

    $('#already_sign_in').html($('.bg-red').length);

    $('#show_code_div').click(function () {
        if ($('#scan_code_div').css('right') == '-180px') {
            $('#scan_code_div').animate({right: '+0px'}, "slow");
            $('#code').focus();
            var t="";
            $('#code').bind('input propertychange', function () {
                clearTimeout(t);
                t = setTimeout("ReadCode()", 1000);
            });
        }
        else {
            $('#scan_code_div').animate({right: '-180px'}, "slow");
        }
    });
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

function ReadCode()
{
    MyAjax.request({
        url: projectUrl + 'MyMeetingApply/ReadCode',
        data: {
            'sId': $('#s_id').val(),
            'code': $('#code').val()
        },
        beforeSend: function () {
            layer.closeAll();
        },
        complete: function(){
            $('#code').val('');
            $('#code').focus();
        }
    }, function (data) {
        if (data.status == 1) {
            if (data.refresh == 0) {
                var obj = document.getElementById(data.userId);
                $(obj).removeClass('sign_in');
                $(obj).addClass('bg-red');
                $(obj).html('已到');
                $('#already_sign_in').html(parseInt($('#already_sign_in').text()) + 1);
                layer.msg('<span style="font-size: 24px; font-weight: bold;">' + data.result + '</span>', {
                    icon: 1,
                    closeBtn: 1,
                    shade: [0.8, '#393D49'],
                    time: 10000
                });
            }
            else {
                layer.msg('<span style="font-size: 24px; font-weight: bold;">' + data.result + '</span>', {
                    icon: 1,
                    closeBtn: 1,
                    shade: [0.8, '#393D49'],
                    time: 10000
                }, function () {
                    window.location.reload();
                });
            }
        }
        else {
            layer.msg('<span style="font-size: 24px; font-weight: bold;">' + data.result + '</span>', {
                icon: 2,
                closeBtn: 1,
                shade: [0.8, '#393D49'],
                time: 10000
            });
        }
    });
}