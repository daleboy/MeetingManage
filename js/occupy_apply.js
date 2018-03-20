//场所占用申请同意or不同意
function OccupyControl(occupyId) {
    var layerIndex = '';
    $.ajax({
        url: '/Schedule/index.php/pending/Pending_index/get_occupy_info',
        type: 'POST',
        data: {
            'occupy_id': occupyId
        },
        dataType: 'json',
        beforeSend: function () {
            layerIndex = layer.load(3);
        },
        success: function (data) {
            $('#now-use-user').html(data.apply_name);
            $('#now-meeting-title').html(data.title);
            $('#occupy-user').html(data.apply_user_name);
            $('#occupy-reason').html(data.occupy_reason);
            //如果是案件排期
            var link = '';
            if (parseInt(data.system_type) < 3) {
                link = {
                    'system_flag': data.system_type,
                    's_id': data.s_id,
                    'data': []
                };
                link = JSON.stringify(link);
                link = $.base64.encode(link);
            }
            $('#occupy-div').data('idAndType', {
                occupyId: occupyId,
                scheduleId: data.now_schedule_id,
                scheduleSid: data.s_id,
                systemType: data.system_type,
                caseScheduleData: link
            });
            layer.open({
                type: 1,
                area: '510px',
                content: $('#occupy-div')
            });
        },
        complete: function () {
            layer.close(layerIndex);
        }
    });
}

//同意场所占用
function AgreeOccupy() {
    var layerIndex = '';
    $.ajax({
        url: '/Schedule/index.php/pending/Pending_index/change_schedule_status',
        type: 'POST',
        data: {
            'occupy_id': $('#occupy-div').data('idAndType').occupyId,
            'schedule_id': $('#occupy-div').data('idAndType').scheduleId,
            'operation_type': 'agree'
        },
        beforeSend: function () {
            layerIndex = layer.load(3);
        },
        success: function (result) {
            if (result == 1) {
                //案件排期
                if (parseInt($('#occupy-div').data('idAndType').systemType) < 3) {
                    layer.alert('修改排期状态成功，确定后将跳转到案件排期页面，请点击二次排期按钮重新选择您的排期。', function (index) {
                        PostData('/Schedule/index.php/scheduling/scheduling_index/lawsuit_case_submit_start', $('#occupy-div').data('idAndType').caseScheduleData);
                    });
                }
                //会议排期
                if (parseInt($('#occupy-div').data('idAndType').systemType) == 3) {
                    layer.alert('修改排期状态成功，确定后将跳转到排期页面修改或删除您的排期。', function (index) {
                        window.open('/MeetingManage/index.php/MeetingApply/index/' + $('#occupy-div').data('idAndType').scheduleSid);
                        window.location.reload();
                    });
                }
            } else {
                layer.alert('修改排期状态失败！');
            }
        },
        complete: function () {
            layer.close(layerIndex);
        }
    });
}

function DisagreeOccupy() {
    var layerIndex = '';
    $.ajax({
        url: '/Schedule/index.php/pending/Pending_index/change_schedule_status',
        type: 'POST',
        data: {
            'occupy_id': $('#occupy-div').data('idAndType').occupyId,
            'schedule_id': $('#occupy-div').data('idAndType').scheduleId,
            'operation_type': 'disagree'
        },
        beforeSend: function () {
            layerIndex = layer.load(3);
        },
        success: function (result) {
            if (result == 1) {
                layer.alert('拒绝场所占用申请成功。', function (index) {
                    window.location.reload();
                });
            } else {
                layer.alert('拒绝场所占用申请失败。');
            }
        },
        complete: function () {
            layer.close(layerIndex);
        }
    });
}