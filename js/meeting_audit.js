$(function () {
    GetAuditList(1);

    $('#agree').click(function () {
        layer.confirm('确定审核通过？', { icon: 3 }, function () {
            DealWithAudit(2);
        });
    });

    $('#disagree').click(function () {
        layer.confirm('确定审核不通过？', { icon: 3 }, function () {
            DealWithAudit(3);
        });    
    });

    $.openSelect('#passon', {
        title: '转交审核人',
        user: {
            hideSelect: "#approval-select-person-youxiang",
            showSelect: "#approval-select-person-xm",
            hideType: "youxiang",
            showType: "xm",
            selectedMulti: false
        },
        onEnd: function (youxiang, xm) {
            layer.confirm('是否转交 ' + xm + ' 审核？', { icon: 3 }, function (confirmIndex) {
                MyAjax.request({
                    url: projectUrl + 'MeetingAudit/PassOn',
                    data: {
                        'sId': $('#audit_div').data('auditId'),
                        'pUserId': youxiang,
                        'suggestion': $('#suggestion').val()
                    }
                }, function (result) {
                    if (result == 1) {
                        layer.alert('转交审核成功！', { icon: 1 }, function () {
                            window.location.reload();
                        });
                    }
                    else {
                        layer.alert('转交审核失败！', { icon: 2 });
                    }
                });
            });
        }
    });
});

function GetAuditList(currPage)
{
    MyAjax.request({
        url: projectUrl + 'MeetingAudit/GetAuditList',
        data: {
            'curr': currPage,
            'perPage': perPageNum
        }
    }, function (data) {
        if (data.audit_list.length > 0) {
            $('#my_audit_tbody').empty();
            $.each(data.audit_list, function (i, n) {
                var join = n.join_person == '' ? '无需报名' : n.join_person;
                var tr = '<tr>' + 
                    '<td style="vertical-align:middle;">' + n.meeting_name + '</td>' + 
                    '<td style="vertical-align:middle;">' + n.address + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.meeting_time + '</td>' +   
                    '<td style="vertical-align:middle;">' + join + '</td>' +  
                    '<td style="vertical-align:middle;">' + n.apply_time + '</td>' +  
                    '<td style="vertical-align:middle;">' + n.control + '</td>' +       
                '</tr >';
                $('#my_audit_tbody').append(tr);
            });
        }
        else {
            $('#my_audit_tbody').html('<tr><td colspan="6" align="center">暂无数据</td></tr>');
        }
        laypage({
            cont: 'my_audit_page',
            curr: data.curr_page,
            pages: Math.ceil(parseInt($('#my_audit_count').val()) / perPageNum),
            jump: function (obj, first) {
                if (!first) {
                    GetAuditList(obj.curr);
                }
            }
        });
    });
}

function View(sId)
{
    window.open(projectUrl + 'MeetingDetail/index/' + sId, '_blank');
}

function Audit(sId)
{
    $('#audit_div').data('auditId', sId);
    $('#suggestion').val('');
    layer.open({
        type: 1,
        title: '审核会议',
        area: ['600px', '300px'],
        content: $('#audit_div')
    });
}

function DealWithAudit(status)
{
    MyAjax.request({
        url: projectUrl + 'MeetingAudit/Audit',
        data: {
            'sId': $('#audit_div').data('auditId'),
            'status': status,
            'suggestion': $('#suggestion').val()
        },
        async: false
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

/*function RegSelectUser()
{
    $.openSelect('.passon', {
        title: '转交审核人',
        user: {
            hideSelect: "#approval-select-person-youxiang",
            showSelect: "#approval-select-person-xm",
            hideType: "youxiang",
            showType: "xm",
            selectedMulti: false
        },
        onEnd: function (youxiang, xm) {
            MyAjax.request({
                url: projectUrl + 'MeetingAudit/PassOn',
                data: {
                    'sId': $('#approval-select-person-youxiang').data('approvalSid'),
                    'pUserId': youxiang
                }
            }, function (result) {
                if (result == 1) {
                    layer.alert('转交审核成功！', { icon: 1 });
                }
                else {
                    layer.alert('转交审核失败！', { icon: 2 });
                }
            });
        }
    });
}*/