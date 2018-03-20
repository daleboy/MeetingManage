$(function() {
    ChangeDivHeight();
    window.onresize = function(){
        ChangeDivHeight();
    }

    var year_tree_setting = {
		async: {
			enable: true,
			url: projectUrl + "MeetingReport/ShowTree",
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

    $('#search').click(function () {
        $('#keywords').data('searchContent', $('#keywords').val());
        if ($.trim($('#keywords').val()) == '') {
            layer.alert('请输入搜索内容！', { icon: 7 });
        }
        else {
            MyAjax.request({
                url: projectUrl + 'MeetingReport/CountReportSearch',
                data: {
                    'searchContent': $('#keywords').data('searchContent')
                }
            }, function (searchCount) {
                $('#tree_id').val(0);
                $('#report_list_count').val(searchCount);
                ShowReportList(1);
            });
        }
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
        $('#report_list_count').val(treeNode.reportNum);
        $('#keywords').val('');
        $('#keywords').data('searchContent', '');
        ShowReportList(1);
    }
}

function ShowReportList(currPage)
{
    MyAjax.request({
        url: projectUrl + 'MeetingReport/GetReportList',
        data: {
            'yearMonth': $('#tree_id').val(),
            'curr': currPage,
            'perPage': perPageNum,
            'searchContent': $('#keywords').data('searchContent')
        }
    }, function (data) {
        if (data.report_list.length > 0) {
            $('#report_list_tbody').empty();
            $.each(data.report_list, function (i, n) {
                var tr = '<tr>' + 
                    '<td style="vertical-align:middle;" class="text-more">' + n.report_name + '</td>' + 
                    '<td style="vertical-align:middle;">' + n.publish_user + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.meeting_name + '</td>' + 
                    '<td style="vertical-align:middle;">' + n.report_time + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.control + '</td>' +       
                '</tr >';
                $('#report_list_tbody').append(tr);
            });
        }
        else {
            $('#report_list_tbody').html('<tr><td colspan="5" align="center">暂无数据</td></tr>');
        }
        laypage({
            cont: 'report_list_page',
            curr: data.curr_page,
            pages: Math.ceil(parseInt($('#report_list_count').val()) / perPageNum),
            jump: function (obj, first) {
                if (!first) {
                    ShowReportList(obj.curr);
                }
            }
        });
    });
}

function View(id)
{
    MyAjax.request({
        url: projectUrl + 'MeetingReport/GetReportDetail',
        data: {
            'id': id
        }
    }, function (data) {
        $('#view_content').html(data.content);
        $('#files_content_box').html("");
        $.each(data.files,function(k,v){
            var name = v.file_path.substring(v.file_path.lastIndexOf('/')+1,v.file_path.length);
            $('#files_content_box').append("<div class='file' alt='"+name+"' onClick='download_file("+v.id+")'>"+name+"</div>");
        });
        layer.open({
            type: 1,
            title: '浏览总结',
            maxmin: true,
            area: ['800px', '600px'],
            content: $('#view_div')
        });
    });
}

//下载 
//参数1为文件id
function download_file(file_id)
{
    MyAjax.request({
        url: projectUrl + '/uploadfile/download',
        dataType:'text',
        data: {
            'id': file_id
        }
    }, function(result) {
        window.location.href = result;
    });
}