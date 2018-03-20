$(function() {
    ChangeDivHeight();
    window.onresize = function(){
        ChangeDivHeight();
    }

    var year_tree_setting = {
		async: {
			enable: true,
			url: projectUrl + "MeetingView/ShowTree",
			autoParam: ["id"]
		},
		callback: {
			onClick: TreeClick
		}
	};
    $.fn.zTree.init($("#year_tree"), year_tree_setting);
    
    $('#close_view').click(function () {
        layer.closeAll();
    });
});

function ChangeDivHeight()
{
    var mainheight = $(window).innerHeight() - 133;
    $('#divHeight').height(mainheight);
}

function TreeClick(event, treeId, treeNode)
{
    if (treeNode.id.length > 4)
    {
        $('#tree_id').val(treeNode.id);
        $('#meeting_list_count').val(treeNode.meetingNum);
        ShowMeetingList(1);
    }
}

function ShowMeetingList(currPage)
{
    MyAjax.request({
        url: projectUrl + 'MeetingView/GetMeetingList',
        data: {
            'yearMonth': $('#tree_id').val(),
            'curr': currPage,
            'perPage': perPageNum
        }
    }, function (data) {
        if (data.meeting_list.length > 0) {
            $('#meeting_list_tbody').empty();
            $.each(data.meeting_list, function (i, n) {
                var tr = '<tr>' + 
                    '<td style="vertical-align:middle;">' + n.meeting_name + '</td>' + 
                    '<td style="vertical-align:middle;">' + n.address + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.meeting_time + '</td>' + 
                    '<td style="vertical-align:middle;">' + n.status + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.control + '</td>' +       
                '</tr >';
                $('#meeting_list_tbody').append(tr);
            });
        }
        else {
            $('#meeting_list_tbody').html('<tr><td colspan="5" align="center">暂无数据</td></tr>');
        }
        laypage({
            cont: 'meeting_list_page',
            curr: data.curr_page,
            pages: Math.ceil(parseInt($('#meeting_list_count').val()) / perPageNum),
            jump: function (obj, first) {
                if (!first) {
                    ShowMeetingList(obj.curr);
                }
            }
        });
    });
}

function View(sId)
{
    window.open(projectUrl + 'MeetingDetail/index/' + sId, '_blank');
}