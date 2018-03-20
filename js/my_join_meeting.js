$(function () {
    GetMyJoinList(1);
});

function GetMyJoinList(currPage)
{
    MyAjax.request({
        url: projectUrl + 'MyJoinMeeting/GetJoinList',
        data: {
            'curr': currPage,
            'perPage': perPageNum
        }
    }, function (data) {
        if (data.join_list.length > 0) {
            $('#my_join_meeting_tbody').empty();
            var tr_class = '';
            $.each(data.join_list, function (i, n) {
                if (n.is_open == '0') {
                    tr_class = ' class="red"';
                }
                else {
                    tr_class = '';
                }
                var tr = '<tr' + tr_class + '>' + 
                    '<td style="vertical-align:middle;">' + n.meeting_name + '</td>' + 
                    '<td style="vertical-align:middle;">' + n.address + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.meeting_time + '</td>' +    
                    '<td style="vertical-align:middle;" class="left_time">' + n.status + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.control + '</td>' +       
                '</tr >';
                $('#my_join_meeting_tbody').append(tr);
            });
            $('.left_time').each(function () {
                $(this).data('leftTime', $(this).text());
                $(this).html('');
            });
            setInterval("LeftTimer()", 1000);
        }
        else {
            $('#my_join_meeting_tbody').html('<tr><td colspan="5" align="center">暂无数据</td></tr>');
        }
        laypage({
            cont: 'my_join_meeting_page',
            curr: data.curr_page,
            pages: Math.ceil(parseInt($('#my_join_meeting_count').val()) / perPageNum),
            jump: function (obj, first) {
                if (!first) {
                    GetMyJoinList(obj.curr);
                }
            }
        });
    });
}

function LeftTimer()
{
    $('.left_time').each(function () {
        var leftTime = parseInt($(this).data('leftTime')) - 1;
        if (leftTime <= 0) {
            $(this).html('会议已开始');
        }
        else {
            $(this).data('leftTime', leftTime);
            $(this).html(ComputeTime(leftTime));
        }    
    });
}

function ComputeTime(leftTime)
{
    var days = parseInt(leftTime / 60 / 60 / 24 , 10); //计算剩余的天数 
    var hours = parseInt(leftTime / 60 / 60 % 24 , 10); //计算剩余的小时 
    var minutes = parseInt(leftTime / 60 % 60, 10);//计算剩余的分钟 
    var seconds = parseInt(leftTime % 60, 10);//计算剩余的秒数
    return days + '天' + hours + '小时' + minutes + '分' + seconds + '秒';
}

function Detail(sId)
{
    window.open(projectUrl + 'MeetingDetail/index/' + sId, '_blank');
}