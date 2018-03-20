$(function () {
    GetAuditList(1);

    $('#agree').click(function () {
        DealWithAudit(1);
    });

    $('#disagree').click(function () {
        DealWithAudit(0);
    });
});

function GetAuditList(currPage)
{
    MyAjax.request({
        url: projectUrl + 'MeetingReportAudit/GetAuditList',
        data: {
            'curr': currPage,
            'perPage': perPageNum
        }
    }, function (data) {
        if (data.audit_list.length > 0) {
            $('#audit_tbody').empty();
            $.each(data.audit_list, function (i, n) {
                var tr = '<tr>' + 
                    '<td style="vertical-align:middle;">' + n.subject + '</td>' + 
                    '<td style="vertical-align:middle;">' + n.publisher + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.meeting_name + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.publish_time + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.control + '</td>' +       
                '</tr >';
                $('#audit_tbody').append(tr);
            });
        }
        else {
            $('#audit_tbody').html('<tr><td colspan="5" align="center">暂无数据</td></tr>');
        }
        laypage({
            cont: 'audit_page',
            curr: data.curr_page,
            pages: Math.ceil(parseInt($('#audit_count').val()) / perPageNum),
            jump: function (obj, first) {
                if (!first) {
                    GetAuditList(obj.curr);
                }
            }
        });
    });
}

function Audit(id)
{
    MyAjax.request({
        url: projectUrl + 'MeetingReportAudit/GetAuditDetail',
        data: {
            'id': id
        }
    }, function (data) {
        $('#view_div').data('auditId', id);
        $('#view_content').html(data.content);
        layer.open({
            type: 1,
            title: '审核会议总结资料',
            area: ['800px', '600px'],
            content: $('#view_div')
        });
    });
}

function DealWithAudit(status)
{
    MyAjax.request({
        url: projectUrl + 'MeetingReportAudit/audit',
        data: {
            'id': $('#view_div').data('auditId'),
            'status': status,
            'suggestion': $('#suggestion').val()
        }
    }, function (result) {
        if (result == 1) {
            layer.alert('审核完成！', { icon: 1 }, function () {
                layer.closeAll();
                window.location.reload();
            });
        }
        else {
            layer.alert('审核发生错误！', { icon: 2 });
        }
    });
}