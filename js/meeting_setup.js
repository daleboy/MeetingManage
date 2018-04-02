$(function () {
    GetMeetingType();
    AddNewMeetingType();
    GetTechnologyType();
    AddNewTechnologyType();
});

/*************************************************************************************
 * 
 * 会议类型设置开始
 *
 **************************************************************************************/
//获取已设置的会议类型
function GetMeetingType() {
    MyAjax.request({
        url: projectUrl + 'MeetingSetup/GetMeetingType'
    }, function (data) {
        $('#meeting-type-current-setup').empty();
        $.each(data, function (i, n) {
            if (n.fjm != null) {
                var delButton = '<span class="float-right tag bg-dot" onClick="DelMeetingType(' + n.type_id + ', \'' + n.type_name + '\')">删</span>' +
                    '<span class="float-right tag bg-yellow margin-right" onClick="ModifyMeetingType(' + n.type_id + ', \'' + n.type_name + '\')">改</span>';
            } else {
                var delButton = '';
            }
            var li = '<li>' + delButton + (i + 1) + '. ' + n.type_name + '</li>';
            $('#meeting-type-current-setup').append(li);
        });
    });
}

//更改新增/修改会议类型窗口内容为添加会议类型
function AddNewMeetingType() {
    var tr = '<td><input type="text" class="input" /></td>' +
        '<td><button class="button" onClick="AddMeetingTypeConfirm()">添加</button></td>';
    $('#add-modify-meeting-type').html(tr);
}

//点击新增/修改会议类型窗口内的新增按钮
function AddMeetingTypeConfirm() {
    var typeName = $('#add-modify-meeting-type').find('td').eq(0).find('input').val();
    if ($.trim(typeName) == '') {
        layer.alert('会议类型名称不能为空', {
            icon: 2
        }, function (index) {
            layer.close(index);
            $('#add-modify-meeting-type').find('td').eq(0).find('input').focus();
        });
    } else {
        MyAjax.request({
            url: projectUrl + 'MeetingSetup/AddMeetingType',
            data: {
                'typeName': typeName
            }
        }, function (result) {
            if (result == 1) {
                layer.msg('会议类型添加成功', {
                    icon: 1,
                    time: 2000
                }, function () {
                    $('#add-modify-meeting-type').find('td').eq(0).find('input').val('');
                    GetMeetingType();
                });
            } else {
                layer.alert('会议类型添加失败', {
                    icon: 7
                });
            }
        });
    }
}

//点击修改会议类型后更改新增/修改会议类型窗口内容为修改会议类型
function ModifyMeetingType(typeId, typeName) {
    var tr = '<td><input type="text" class="input" value="' + typeName + '" /></td>' +
        '<td><button class="button" onClick="ModifyMeetingTypeConfirm(' + typeId + ')">修改</button></td>';
    $('#add-modify-meeting-type').html(tr);
    layer.tips('点此可返回添加模式', '#add-meeting-type-link', {
        tips: [1, '#3595CC'],
        time: 5000
    });
}

//点击新增/修改会议类型窗口内的修改按钮
function ModifyMeetingTypeConfirm(typeId) {
    var typeName = $('#add-modify-meeting-type').find('td').eq(0).find('input').val();
    if ($.trim(typeName) == '') {
        layer.alert('会议类型名称不能为空', {
            icon: 2
        }, function (index) {
            layer.close(index);
            $('#add-modify-meeting-type').find('td').eq(0).find('input').focus();
        });
    } else {
        MyAjax.request({
            url: projectUrl + 'MeetingSetup/ModifyMeetingType',
            data: {
                'typeName': typeName,
                'typeId': typeId
            }
        }, function (result) {
            if (result == 1) {
                layer.msg('会议类型修改成功', {
                    icon: 1,
                    time: 2000
                }, function () {
                    $('#add-modify-meeting-type').find('td').eq(0).find('input').val('');
                    GetMeetingType();
                });
            } else {
                layer.alert('会议类型修改失败', {
                    icon: 7
                });
            }
        });
    }
}

//删除会议类型
function DelMeetingType(typeId, typeName) {
    layer.confirm('确定删除' + typeName + '吗？', {
        icon: 3
    }, function (index) {
        MyAjax.request({
            url: projectUrl + 'MeetingSetup/DelMeetingType',
            data: {
                'typeId': typeId
            }
        }, function (result) {
            if (result == 1) {
                layer.msg('会议类型删除成功', {
                    icon: 1,
                    time: 2000
                }, function () {
                    GetMeetingType();
                });
            } else {
                layer.alert('会议类型删除失败', {
                    icon: 7
                });
            }
        });
    });
}

/*************************************************************************************
 * 
 * 会议类型设置结束
 *
 **************************************************************************************/



/*************************************************************************************
 * 
 * 技术保障类型设置开始
 *
 **************************************************************************************/
//获取已设置的技术保障类型
function GetTechnologyType() {
    MyAjax.request({
        url: projectUrl + 'MeetingSetup/GetTechnologyType'
    }, function (data) {
        $('#technology-type-current-setup').empty();
        if (data.length) {
            $.each(data, function (i, n) {
                if (n.fjm != null) {
                    var delButton = '<span class="float-right tag bg-dot" onClick="DelTechnologyType(' + n.type_id + ', \'' + n.type_name + '\')">删</span>' +
                        '<span class="float-right tag bg-yellow margin-right" onClick="ModifyTechnologyType(' + n.type_id + ', \'' + n.type_name + '\', \'' + n.style + '\')">改</span>';
                } else {
                    var delButton = '';
                }
                var li = '<li>' + delButton + (i + 1) + '. ' + n.type_name + '</li>';
                $('#technology-type-current-setup').append(li);
            });
        } else {
            $('#technology-type-current-setup').append('未有技术保障类型设置');
        }
    });
}

//更改新增/修改技术保障类型窗口内容为添加技术保障类型
function AddNewTechnologyType() {
    var tr = '<td><input type="text" class="input" /></td>' +
        '<td><label><input type="radio" name="technology-type-style" value="checkbox" checked />复选框</label><br />' +
        '<label><input type="radio" name="technology-type-style" value="text" />输入框</label></td>' +    
        '<td><button class="button" onClick="AddTechnologyTypeConfirm()">添加</button></td>';
    $('#add-modify-technology-type').html(tr);
}

//点击新增/修改技术保障类型窗口内的新增按钮
function AddTechnologyTypeConfirm() {
    var typeName = $('#add-modify-technology-type').find('td').eq(0).find('input').val();
    var style = $('#add-modify-technology-type').find('td').eq(1).find('input[type="radio"]:checked').val();
    if ($.trim(typeName) == '') {
        layer.alert('技术保障类型名称不能为空', {
            icon: 2
        }, function (index) {
            layer.close(index);
            $('#add-modify-technology-type').find('td').eq(0).find('input').focus();
        });
    } else {
        MyAjax.request({
            url: projectUrl + 'MeetingSetup/AddTechnologyType',
            data: {
                'typeName': typeName,
                'style': style
            }
        }, function (result) {
            if (result == 1) {
                layer.msg('技术保障类型添加成功', {
                    icon: 1,
                    time: 2000
                }, function () {
                    $('#add-modify-technology-type').find('td').eq(0).find('input').val('');
                    GetTechnologyType();
                });
            } else {
                layer.alert('技术保障类型添加失败', {
                    icon: 7
                });
            }
        });
    }
}

//点击修改技术保障类型后更改新增/修改技术保障类型窗口内容为修改技术保障类型
function ModifyTechnologyType(typeId, typeName, style) {
    var tr = '<td><input type="text" class="input" value="' + typeName + '" /></td>' +
        '<td>' + 
        '<label><input type="radio" name="technology-type-style" value="checkbox"' + (style == 'checkbox' ? 'checked' : '') + ' />复选框</label><br />' +
        '<label><input type="radio" name="technology-type-style" value="text"' + (style == 'text' ? 'checked' : '') + ' />输入框</label>' +
        '</td>' +
        '<td><button class="button" onClick="ModifyTechnologyTypeConfirm(' + typeId + ')">修改</button></td>';
    $('#add-modify-technology-type').html(tr);
    layer.tips('点此可返回添加模式', '#add-technology-type-link', {
        tips: [1, '#3595CC'],
        time: 5000
    });
}

//点击新增/修改技术保障类型窗口内的修改按钮
function ModifyTechnologyTypeConfirm(typeId) {
    var typeName = $('#add-modify-technology-type').find('td').eq(0).find('input').val();
    var style = $('#add-modify-technology-type').find('td').eq(1).find('input[type="radio"]:checked').val();
    if ($.trim(typeName) == '') {
        layer.alert('技术保障类型名称不能为空', {
            icon: 2
        }, function (index) {
            layer.close(index);
            $('#add-modify-technology-type').find('td').eq(0).find('input').focus();
        });
    } else {
        MyAjax.request({
            url: projectUrl + 'MeetingSetup/ModifyTechnologyType',
            data: {
                'typeName': typeName,
                'typeId': typeId,
                'style': style
            }
        }, function (result) {
            if (result == 1) {
                layer.msg('技术保障类型修改成功', {
                    icon: 1,
                    time: 2000
                }, function () {
                    $('#add-modify-technology-type').find('td').eq(0).find('input').val('');
                    GetTechnologyType();
                });
            } else {
                layer.alert('技术保障类型修改失败', {
                    icon: 7
                });
            }
        });
    }
}

//删除技术保障类型
function DelTechnologyType(typeId, typeName) {
    layer.confirm('确定删除' + typeName + '吗？', {
        icon: 3
    }, function (index) {
        MyAjax.request({
            url: projectUrl + 'MeetingSetup/DelTechnologyType',
            data: {
                'typeId': typeId
            }
        }, function (result) {
            if (result == 1) {
                layer.msg('技术保障类型删除成功', {
                    icon: 1,
                    time: 2000
                }, function () {
                    GetTechnologyType();
                });
            } else {
                layer.alert('技术保障类型删除失败', {
                    icon: 7
                });
            }
        });
    });
}
/*************************************************************************************
 * 
 * 技术保障类型设置结束
 *
 **************************************************************************************/