var choseUsers = [];
var chosedUsers = [];
var delChosedUsers = [];

$(function () {
    SignUpList(1);

    var dept_tree_setting = {
        async: {
            enable: true,
            url: projectUrl + 'MeetingSignUp/ShowDeptTree',
            autoParam: ['id']
        },
        callback: {
            onClick: TreeClick
        }
    };
    $.fn.zTree.init($('#dept_tree'), dept_tree_setting);

    $('#show_users').on('mouseover', '.users_show_inline', function () {
        $(this).find('span').show();
    });
    $('#show_users').on('mouseout', '.users_show_inline', function () {
        $(this).find('span').hide();
    });

    $('#show_users').on('click', '.del_chose_user', function () {
        var userId = $(this).parent().data('userData').userId;
        for (var i = 0; i < choseUsers.length; i++) {
            if (choseUsers[i].userId == userId) {
                choseUsers.splice(i, 1);
                break;
            }
        }
        $(this).parent().remove();
    });

    $('#clear_chose').click(function () {
        /*layer.confirm('确定要清空所选用户吗？', { icon: 3 }, function (index) {
            choseUsers = [];
            $('#show_users').empty();
            layer.close(index);
        });*/
        choseUsers = [];
        chosedUsers = [];
        delChosedUsers = [];
        layer.closeAll();
    });

    $('#save_chose').click(function () {
        if (choseUsers.length > 0) {
            MyAjax.request({
                url: projectUrl + 'MeetingSignUp/SaveSignUp',
                data: {
                    'sId': $('#dept_tree_div').data('signUpSid'),
                    'choseUsers': choseUsers,
                    'chosedUsers': chosedUsers,
                    'delChosedUsers': delChosedUsers
                }
            }, function (result) {
                if (result == 1) {
                    layer.alert('报名成功！', { icon: 1 }, function () {
                        layer.closeAll();
                        window.location.reload();
                    });
                }
                else {
                    layer.alert('报名失败！', { icon: 2 });
                }
            });
        }
        else {
            layer.alert('请选择用户后再提交！', { icon: 7 });
        }
    });
});

function TreeClick(event, treeId, treeNode)
{
    $('#show_dept_name').html(treeNode.name + '可报名名额：');
    SignUp(0, treeNode.id, false);
}

function SignUp(sId, deptId, openLayer)
{
    if (sId != 0) {
        $('#dept_tree_div').data('signUpSid', sId);
    }    
    $('#select-user-list').empty();
    choseUsers = [];
    delChosedUsers = [];
    MyAjax.request({
        url: projectUrl + 'MeetingSignUp/GetDeptUser',
        data: {
            'sId': $('#dept_tree_div').data('signUpSid'),
            'deptId': deptId
        }
    }, function (data) {
        if (data.user_list.length > 0) {
            $.each(data.user_list, function (i, n) {
                if (n.sign_up_status.have_log == 1) {
                    if (n.sign_up_status.is_appoint == 1) {
                        var signUpStatus = '<span class="float-right text-yellow" rel="canNotSignUp"><small>指定参会人员</small></span>';
                    }
                    else {
                        var signUpStatus = '<span class="float-right text-green"><small>已报名</small></span>';
                        choseUsers.push(n.user_id);
                    }
                }
                else {
                    if (n.sign_up_status.other_schedule == 1) {
                        var signUpStatus = '<span class="float-right text-dot" rel="otherSchedule"><small>该会议期间已有其他排期</small></span>';
                    }
                    else {
                        var signUpStatus = '';
                    }    
                }
                $('#select-user-list').append('<a href="javascript:" onClick="AddUser(\'' + n.user_id + '\')"><strong>' + n.user_name + '</strong>' + signUpStatus + '</a>');
            });
            if (choseUsers.length > 0) {
                chosedUsers = choseUsers.slice(0);
            }    
        }
        else {
            $('#select-user-list').append('<strong>该部门无用户</strong>');
        }
        if (typeof (data.space) != 'undefined' && data.space != '') {
            var canSignUpNum = data.space - data.sign_up_num;
            $('#show_dept_name').data('can_sign_up_num', { space: data.space, canSignUpNum: data.sign_up_num });
            $('#can_sign_up_num').html(canSignUpNum);
        }
        else {
            $('#show_dept_name').data('can_sign_up_num', { space: 0, canSignUpNum: 0 });
            $('#can_sign_up_num').html(0);
        }
        
        if (typeof (data.dept_name) != 'undefined') {
            $('#show_dept_name').html(data.dept_name + '可报名名额：');
        }
        
    });
    if (openLayer) {
        layer.open({
            type: 1,
            title: false,
            area: ['700px', '500px'],
            content: $('#dept_tree_div')
        });
    }    
};

function AddUser(userId)
{
    if ($(event.target).is('a')) {
        target = $(event.target);
    }
    else {
        target = $(event.target).closest('a');
    }
    if (target.find('span').attr('rel') == 'canNotSignUp') {
        layer.alert('该用户是指定参会人员，不能报名！', { icon: 7 });
    }
    else if (target.find('span').attr('rel') == 'otherSchedule') {
        layer.alert('此用户在该会议举行时间段内已有其他排期，不能报名！', { icon: 7 });
    }
    else {
        //如果已选用户数为0，并且剩余报名数大于0，该用户可报名
        if (choseUsers.length == 0 && $('#show_dept_name').data('can_sign_up_num').space > 0) {
            var canAdd = true;
        }
        else {
            var canContinue = false,
                canAdd = false,
                maxSignUp = false;
            //遍历已选用户数组
            for (var i = 0; i < choseUsers.length; i++) {
                //如果当前点击的用户在已选用户中存在，将其在已选用户中删除（取消报名）
                if (choseUsers[i] == userId) {
                    choseUsers.splice(i, 1);
                    canAdd = false;
                    maxSignUp = false;
                    canContinue = false;
                    target.find('span').remove();
                    if ($.inArray(userId, chosedUsers) >= 0) {
                        delChosedUsers.push(userId);
                    }
                    break;
                }
                //如果当前点击的用户不在已选用户中
                else {
                    canContinue = true;
                }
            }
            if (canContinue) {
                //如果剩余报名数等于已选用户数
                if ($('#show_dept_name').data('can_sign_up_num').space == choseUsers.length) {
                    canAdd = false;
                    maxSignUp = true;
                }
                else {
                    canAdd = true;
                    for (var i = 0, len = delChosedUsers.length; i < len; i++) {
                        if (delChosedUsers[i] == userId) {
                            delChosedUsers.splice(i, 1);
                        }
                    }
                }
            }
        }    
        if (canAdd) {
            choseUsers.push(userId);
            target.append('<span class="float-right text-green"><small>报名</small></span>');
        }
        else {
            if (maxSignUp) {
                layer.alert('已达到部门最大报名人数，不能再报名！', { icon: 7 });
            }
        }
        $('#can_sign_up_num').html($('#show_dept_name').data('can_sign_up_num').space - choseUsers.length);
    }
}

function SignUpList(currPage)
{
    MyAjax.request({
        url: projectUrl + 'MeetingSignUp/GetSignUpList',
        data: {
            'curr': currPage,
            'perPage': perPageNum
        }
    }, function (data) {
        if (data.sign_up_list.length > 0) {
            $('#sign_up_tbody').empty();
            $.each(data.sign_up_list, function (i, n) {
                var sign_up = '';
                for (var i = 0; i < n.sign_up_number.length; i++) {
                    sign_up += n.sign_up_number[i].dept_name + '（名额：' + n.sign_up_number[i].space + '，剩余名额：' + (n.sign_up_number[i].space - n.sign_up_number[i].sign_up_num) + '）';
                    if (i >= 0 && n.sign_up_number.length > 1) {
                        sign_up += '<br />';
                    }
                }
                var tr = '<tr>' + 
                    '<td style="vertical-align:middle;">' + n.meeting_name + '</td>' + 
                    '<td style="vertical-align:middle;">' + n.address + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.meeting_time + '</td>' +   
                    '<td style="vertical-align:middle;">' + sign_up + '</td>' +  
                    '<td style="vertical-align:middle;">' + n.control + '</td>' +       
                '</tr >';
                $('#sign_up_tbody').append(tr);
            });
        }
        else {
            $('#sign_up_tbody').html('<tr><td colspan="5" align="center">暂无数据</td></tr>');
        }
        laypage({
            cont: 'sign_up_page',
            curr: data.curr_page,
            pages: Math.ceil(parseInt($('#sign_up_count').val()) / perPageNum),
            jump: function (obj, first) {
                if (!first) {
                    SignUpList(obj.curr);
                }
            }
        });
    });
}

function View(sId)
{
    window.open(projectUrl + 'MeetingDetail/index/' + sId, '_blank');
}