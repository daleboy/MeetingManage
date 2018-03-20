var filejson = [];
var filejson2 = [];
var filekey = 0;
$(function () {
    GetMyList(1);

    laydate({
        elem: '#startdate_input',
        format: 'YYYY-MM-DD', // 分隔符可以任意定义，该例子表示只显示年月
        min: laydate.now(), 
        festival: true,
        istoday: true,
        start: laydate.now(0, "YYYY-MM-DD"),
        istime: true       
    });

    laydate({
        elem: '#enddate_input',
        format: 'YYYY-MM-DD', // 分隔符可以任意定义，该例子表示只显示年月
        min: laydate.now(), 
        festival: true,
        istoday: true,
        start: laydate.now(0, "YYYY-MM-DD"),
        istime: true       
    });

    $.openSelect('#add_dept', {
        title: '选择发布范围（部门）',
        area: ['666px', '513px'],
        dept: {
            hideSelect: "#hide_publish_dept_input",
            showSelect: "#publish_dept_input",
            hideType: "id",
            showType: "name",
            selectedMulti: true,
            onEnd: function(id,name) {
                $("#publish_dept_input").val(name);
                $("#hide_publish_dept_input").val(id);
            }
        },
        closeDialog: function () {
/*            $("#case_id").focus();*/
        },
    });

    $.openSelect('#add_user', {
        title: '选择发布范围（人员）',
        area: ['666px', '513px'],
        user: {
            hideSelect: "#hide_publish_user_input",
            showSelect: "#publish_user_input",
            hideType: "youxiang",
            showType: "xm",
            selectedMulti: true
        },
        onEnd: function(youxiang, xm) {
            $("#publish_user_input").val(xm);
            $("#hide_publish_user_input").val(youxiang);       
        },
        closeDialog: function () {
/*            $("#case_id").focus();*/
        },
    });

    $("#select_self_court_button").click(function(){
        $("#publish_dept_input").val("");
        $("#hide_publish_dept_input").val("");
        MyAjax.request({
            url: projectUrl + '/MyMeetingApply/GetSelfCourtDept'
        }, function (data) {
            $.each(data,function(k,v){
                $("#publish_dept_input").val($("#publish_dept_input").val()+v.dept_name+",");
                $("#hide_publish_dept_input").val($("#hide_publish_dept_input").val()+v.dept_id+",");
            });
        });        
    });


    $(".close_upload").click(function(){
        layer.closeAll();
    });

    $("#send_notify").click(function(){
        if ($("#hide_publish_dept_input").val() == "" && $("#hide_publish_user_input").val() == "") 
        {
            layer.alert("请选择发布范围！");
        }
        else if(tab($("#startdate_input").val(), $("#enddate_input").val()) === 0)
        {
            layer.alert("发布时间不能大于结束时间");
        }
        else
        {
            var file_name = "";
            var file_dir = "";
            $.each($("#file-show").data("file"),function(k,v){
                file_dir += v.dir+",";
                file_name += v.name+"*";
            });
            var content = ue.getPlainTxt();//s就是编辑器的带格式的内容
            MyAjax.request({
                url: projectUrl + 'MyMeetingApply/NotifyGo',
                data: {
                    'notify_title': $("#notify_name_input").val(),
                    'dept_id': $("#hide_publish_dept_input").val(),
                    "user_id":$("#hide_publish_user_input").val(),
                    'start_time':$("#startdate_input").val(),
                    'end_time':$("#enddate_input").val(),
                    'file_dir':file_dir,
                    'file_name':file_name,
                    'content':content
                }
            }, function (data) {
                if (data == 1) {
                    layer.alert("通知发布成功",function(){
                        layer.closeAll();
                    });
                }
                else{
                    layer.alert("通知发布失败");
                }
            });  
        }      
    });

    $("#select_all_court_button").click(function(){
        $("#publish_dept_input").val("");
        $("#hide_publish_dept_input").val("");
        MyAjax.request({
            url: projectUrl + '/MyMeetingApply/GetAllCourtDept'
        }, function (data) {
            $.each(data,function(k,v){
                $("#publish_dept_input").val($("#publish_dept_input").val()+v.dept_name+",");
                $("#hide_publish_dept_input").val($("#hide_publish_dept_input").val()+v.dept_id+",");
            });
            $('#add_dept').attr("disabled",true);
        });        
    });

    $("#remove_dept").click(function(){
        $("#publish_dept_input").val("");
        $("#hide_publish_dept_input").val("");
        $('#add_dept').attr("disabled",false);
    });

    $("#remove_user").click(function(){
        $("#publish_user_input").val("");
        $("#hide_publish_user_input").val("");
    });

    $('#remind_send').click(function () {
        if ($('#remind_setup input[type="checkbox"]:checked').length > 0) {
            layer.confirm('确定发送提醒吗？', { icon: 3 }, function (index) {
                var meetingPerson = typeof($('#remind_join_person').data('remindJoinPerson')) == 'undefined' ? '' : $('#remind_join_person').data('remindJoinPerson');
                var signUpPerson = typeof ($('#remind_join_dept').data('remindJoinDept')) == 'undefined' ? '' : $('#remind_join_dept').data('remindJoinDept');
                var tea = typeof ($('#remind_tea').data('remindTea')) == 'undefined' ? '' : $('#remind_tea').data('remindTea');
                var technology = typeof($('#remind_technology').data('remindTechnology')) == 'undefined' ? '' : $('#remind_technology').data('remindTechnology');
                layer.close(index);
                if (meetingPerson != '' || signUpPerson != '' || tea != '' || technology != '') {
                    var sendEmail = 0, sendMessage = 0, sendSms = 0;
                    $('#remind_setup input[type="checkbox"]:checked').each(function () {
                        if ($(this).prop('name') == 'email') {
                            if ($(this).prop('checked')) {
                                sendEmail = 1;
                            }
                        }
                        if ($(this).prop('name') == 'message') {
                            if ($(this).prop('checked')) {
                                sendMessage = 1;
                            }
                        }
                        if ($(this).prop('name') == 'sms') {
                            if ($(this).prop('checked')) {
                                sendSms = 1;
                            }
                        }
                    });
                    MyAjax.request({
                        url: projectUrl + '/MyMeetingApply/SendRemind',
                        data: {
                            'sId': $('#remind_meeting_name').data('sId'),
                            'sendEmail': sendEmail,
                            'sendMessage': sendMessage,
                            'sendSms': sendSms,
                            'meetingPerson': meetingPerson,
                            'signUpPerson': signUpPerson,
                            'tea': tea,
                            'technology': technology,
                            'remindTime': $('#remind_time').val(),
                            'remindContent': $('#remind_content').val()
                        }
                    }, function (result) {
                        if (result == 1) {
                            layer.alert('发送提醒成功！', { icon: 1 });
                            layer.closeAll();
                        }
                        else if (result == 0) {
                            layer.alert('发送提醒失败！', { icon: 2 });
                        }
                        else {
                            layer.alert('数据错误，发送提醒失败！', { icon: 2 });
                        }
                    });
                }
                else {
                    layer.alert('没有需要发送提醒的用户！', { icon: 7 });
                }
            });
        }
        else {
            layer.alert('请先选择提醒方式后', { icon: 7 });
        }
    });
});

function GetMyList(currPage)
{
    MyAjax.request({
        url: projectUrl + 'MyMeetingApply/GetMyList',
        data: {
            'curr': currPage,
            'perPage': perPageNum
        }
    }, function (data) {
        if (data.meeting_list.length > 0) {
            $('#my_list_tbody').empty();
            $.each(data.meeting_list, function (i, n) {
                if (n.join_person == '' && n.join_dept == '') {
                    var join = '无需报名';
                }
                else {
                    var join = '<p>' + CutStr(n.join_person, 50) + '</p><p>' + CutStr(n.join_dept, 50) + '</p>';
                }
                var tr = '<tr>' + 
                    '<td style="vertical-align:middle;">' + n.meeting_name + '</td>' + 
                    '<td style="vertical-align:middle;">' + n.address + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.meeting_time + '</td>' +   
                    '<td style="vertical-align:middle;">' + join + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.status + '</td>' +   
                    '<td style="vertical-align:middle;">' + n.control + '</td>' +       
                '</tr >';
                $('#my_list_tbody').append(tr);
            });
        }
        else {
            $('#my_list_tbody').html('<tr><td colspan="6" align="center">暂无数据</td></tr>');
        }
        laypage({
            cont: 'my_list_page',
            curr: data.curr_page,
            pages: Math.ceil(parseInt($('#my_list_count').val()) / perPageNum),
            jump: function (obj, first) {
                if (!first) {
                    GetMyList(obj.curr);
                }
            }
        });
    });
}

function DelMyApply(sId, meetingName)
{
    layer.confirm('确认删除"' + meetingName + '"吗？', { icon: 3 }, function () {
        MyAjax.request({
            url: projectUrl + 'MyMeetingApply/DeleteMyApply',
            data: {
                'sId': sId
            }
        }, function (result) {
            if (result == 1) {
                layer.alert(meetingName + '删除成功！', { icon: 1 }, function () {
                    window.location.reload();
                });
            }
            else {
                layer.alert(meetingName + '会议删除失败！', { icon: 2 });
            }    
        });
    });
}


function SendNotify(sId)
{
    ResetNotifyDiv();
    $("#hide_sid").val(sId);
/*    $("#notify_div").show();*/
    MyAjax.request({
        url: projectUrl + 'MyMeetingApply/GetMeetingNotify',
        data: {
            'sId': sId
        }
    }, function (data) {
        $("#notify_name_input").val(data.meeting_title);
        $("#startdate_input").val(data.start_time);
        $.each(data.notify_title,function(k,v){
            filekey = k;
            var index = v .lastIndexOf("\/");  
            var filename = v .substring(index + 1, v .length);
            $("#file-show").append("<div id='file"+filekey+"'>"+filename+"<a href='javascript:void(0)' onclick='deletefile("+filekey+","+data.file_id[k]+",1)' ><img src='/LettersFromTheMasses/images/delete_file.png'></a></div>");
            filearr = { name: filename, dir: v , file_id:data.file_id[k]};
            if ($("#file-show").data("file") != undefined) {
                filejson = $("#file-show").data("file");
            }
            filejson.push(filearr);
        });
        $("#file-show").data("file", filejson);
    });
    MyUpload.request({
        singleFileUploads: true,
        postfix: 'doc,docx,xlsx,xls,png,jpg,jpeg,gif',
        myData: { folder: 'RegisterAttachment','s_id':$("#hide_sid").val() }
    }, function (data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress-box').show();
        $('#progress .progress-bar').css('width', progress + '%');
        $('#progress-box').fadeOut(2000);
    }, function (data) {
        if (data.result == "1") {
/*            if (filekey == 0) {*/
                $("#file-show").append("<div id='file"+filekey+"'>"+data.filename+"<a href='javascript:void(0)' onclick='deletefile("+filekey+","+data.file_id+",1)' ><img src='/LettersFromTheMasses/images/delete_file.png'></a></div>");
/*            }
            else {
                $("#file-show").append("<div id='file"+filekey+"'>" + data.filename+"<a href='javascript:void(0)' onclick='deletefile("+filekey+","+data.file_id+")'><img src='/LettersFromTheMasses/images/delete_file.png'></a></div>");
            }*/
            filekey++;
        }
        else {
            layer.alert("上传失败");
        }
        filearr = { name: data.filename, dir: data.file_dir, file_id: data.file_id};
        if ($("#file-show").data("file") != undefined) {
            filejson = $("#file-show").data("file");
        }
        filejson.push(filearr);
        $("#file-show").data("file", filejson);
    });
    layer.open({
        type: 1,
        title: '发布通告',
        area: ['800px', '600px'],
        content: $('#notify_div')
    });
}

function Remind(sId)
{
    ResetRemindDiv();
    var targetTr = $(event.target).closest('tr');
    MyAjax.request({
        url: projectUrl + 'MyMeetingApply/GetMeetingRemind',
        data: {
            'sId': sId
        }
    }, function (data) {
        $('#remind_meeting_name').data('sId', sId);
        $('#remind_meeting_name').html($(targetTr).children().eq(0).text());
        $('#remind_meeting_address').html($(targetTr).children().eq(1).text());
        $('#remind_meeting_date').html($(targetTr).children().eq(2).text());
        $('#remind_content').html($(targetTr).children().eq(0).text() + '于' + $(targetTr).children().eq(2).text() + '在' + $(targetTr).children().eq(1).text() + '举行');
        //参会人员
        if (data.person.length > 0) {
            $.each(data.person, function (i, n) {
                $('#remind_join_person').append(n.name);
                if (i < data.person.length - 1) {
                    $('#remind_join_person').append('，');
                }
            });
            $('#remind_join_person').data('remindJoinPerson', data.person);
        }
        else {
            $('#remind_join_person').html('无指定参会人员');
        }
        //参会部门
        if (data.dept.length > 0) {
            $.each(data.dept, function (i, n) {
                $('#remind_join_dept').append(n.name + '(名额：' + n.num);
                if (typeof (data.sign_up[n.id]) != 'undefined') {
                    var sign_up = '';
                    $.each(data.sign_up[n.id], function (si, sn) {
                        sign_up += sn.name;
                        if (si < data.sign_up[n.id].length - 1) {
                            sign_up += '，';
                        }
                    });
                    $('#remind_join_dept').append('<i>[已报名：' + sign_up + ']</i>');
                }
                $('#remind_join_dept').append(')');
                if (i < data.dept.length - 1) {
                    $('#remind_join_dept').append('<br />');
                }
            });
            if (typeof (data.sign_up) != 'undefined') {
                $('#remind_join_dept').data('remindJoinDept', data.sign_up);
            }
        }
        else {
            $('#remind_join_dept').html('无指定参会部门');
        }
        //后勤保障
        if (data.tea.length > 0) {
            $.each(data.tea, function (i, n) {
                $('#remind_tea').append(n.name);
                if (i < data.tea.length - 1) {
                    $('#remind_tea').append('，');
                }
            });
            $('#remind_tea').data('remindTea', data.tea);
        }
        else {
            $('#remind_tea').html('无后勤保障人员');
        }
        //技术保障
        if (data.technology.length > 0) {
            $.each(data.technology, function (i, n) {
                $('#remind_technology').append(n.name);
                if (i < remind_technology.length - 1) {
                    $('#remind_technology').append('，');
                }
            });
            $('#remind_technology').data('remindTechnology', data.technology);
        }
        else {
            $('#remind_technology').html('无技术保障人员');
        }
        //提醒历史
        if (data.remind_history.length > 0) {
            $.each(data.remind_history, function (i, n) {
                $('#remind_history').append('<span class="text-dot">' + (i + 1) + '.</span>&nbsp;' + n + '<br /><hr />');
            });
        }
        else {
            $('#remind_history').html('无提醒历史记录');
        }
        layer.open({
            type: 1,
            title: '会议提醒',
            area: ['800px', '600px'],
            content: $('#remind_div')
        });
    });
}

function UploadSignInForm(sId)
{
     $("#file-show2").html("");
    $("#hide_sid2").val(sId);
/*    $("#notify_div").show();*/
    MyAjax.request({
        url: projectUrl + 'MyMeetingApply/GetSign',
        data: {
            'sId': sId
        }
    }, function (data) {
        $.each(data.notify_title,function(k,v){
            filekey = k;
            var index = v .lastIndexOf("\/");  
            var filename = v .substring(index + 1, v .length);
            $("#file-show2").append("<div id='filex"+filekey+"'>"+filename+"<a href='javascript:void(0)' onclick='deletefile("+filekey+","+data.file_id[k]+",2)' ><img src='/LettersFromTheMasses/images/delete_file.png'></a></div>");
            filearr = { name: filename, dir: v , file_id:data.file_id[k]};
            if ($("#file-show2").data("file") != undefined) {
                filejson2 = $("#file-show2").data("file");
            }
            filejson2.push(filearr);
        });
        $("#file-show2").data("file", filejson2);
    });
    MyUpload2.request({
        singleFileUploads: true,
        postfix: 'doc,docx,xlsx,xls,png,jpg,jpeg,gif,pdf,ppt,pptx,aip',
        myData: {'s_id':$("#hide_sid2").val() }
    }, function (data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress-box2').show();
        $('#progress2 .progress-bar').css('width', progress + '%');
        $('#progress-box2').fadeOut(2000);
    }, function (data) {
        if (data.result == "1") {
/*            if (filekey == 0) {*/
                $("#file-show2").append("<div id='filex"+filekey+"'>"+data.filename+"<a href='javascript:void(0)' onclick='deletefile("+filekey+","+data.file_id+",2)' ><img src='/LettersFromTheMasses/images/delete_file.png'></a></div>");
/*            }
            else {
                $("#file-show").append("<div id='file"+filekey+"'>" + data.filename+"<a href='javascript:void(0)' onclick='deletefile("+filekey+","+data.file_id+")'><img src='/LettersFromTheMasses/images/delete_file.png'></a></div>");
            }*/
            filekey++;
        }
        else {
            layer.alert("上传失败");
        }
        filearr = { name: data.filename, dir: data.file_dir, file_id: data.file_id};
        if ($("#file-show2").data("file") != undefined) {
            filejson2 = $("#file-show2").data("file");
        }
        filejson.push(filearr);
        $("#file-show2").data("file", filejson);
    });
    layer.open({
        type: 1,
        title: '上传签到表',
        area: ['500px', '250px'],
        content: $('#upload_div')
    });
}

function ResetRemindDiv()
{
    $('#remind_meeting_name').empty();
    $('#remind_meeting_address').empty();
    $('#remind_meeting_date').empty();
    $('#remind_join_person').empty();
    $('#remind_join_dept').empty();
    $('#remind_technology').empty();
    $('#remind_tea').empty();
    $('#remind_history').empty();
    $('#remind_content').empty();
}

function ResetNotifyDiv()
{
    $("#hide_sid").val("");
    $('#notify_name_input').empty();
    $('#publish_dept_input').empty();
    $('#publish_user_input').empty();
    $('#startdate_input').empty();
    $('#enddate_input').empty();
    $('#file-show').empty();
}

//删除上传文件
//cishu代表引用多少次upload插件
function deletefile(filekey,file_id,cishu)
{
    MyAjax.request({
        url: projectUrl + 'uploadfile/deletefile',
        data: {
            'file_id': file_id
        }
    }, function (data) {
        if (data == 1 && cishu == 1) 
        {
            $("#file-show").data("file").splice($("#file-show div").index($("#file"+filekey)),1);
            $("#file"+filekey).remove();
        }
        else if(data == 1 && cishu != 1){
            $("#file-show2").data("file").splice($("#file-show2 div").index($("#filex"+filekey)),1);
            $("#filex"+filekey).remove();
        }
        else
        {
            layer.alert("数据删除错误！");
        }
    });
}

function View(sId)
{
    window.open(projectUrl + 'MeetingDetail/index/' + sId, '_blank');
}

function tab(date1,date2){
    var oDate1 = new Date(date1);
    var oDate2 = new Date(date2);
    if(oDate1.getTime() > oDate2.getTime()){
        return 0;
    } else {
        return 1;
    }
}


/*function SendNotify(sId)
{
    MyAjax.request({
        url: projectUrl + 'MyMeetingApply/Notify_Go',
        data: {
            'sId': sId
        }
    }, function (data) {
        if (data.result == 1) {
            console.log(data.notify);
            //window.open(projectUrl + 'MeetingDetail/JumpToNotify/' + data.notify, '_blank');
            window.location.href = projectUrl + 'MeetingDetail/JumpToNotify/' + data.notify;
            //window.location.href = "http://147.1.3.133/general/notify/manage/modify_interface.php?NOTIFY_ID="+data.notify+"&CUR_PAGE=1"; 
        }
        else if(data.result == 2){
            layer.alert("插入通知表失败");
        }
        else if(data.result == 3){
            layer.alert("更新排期系统表标示失败");
        }
        else{
            layer.alert("s_id无效");
        }
    });
}*/