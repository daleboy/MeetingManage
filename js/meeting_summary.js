var ueditor=UE.getEditor('editor');
var filekey = 0;
var filejson = [];
$(function(){
    if ($("#document_id").val() != 0) {
        //获取会议总结内容
        $("#submit2").data("document_id",$("#document_id").val());
        MyAjax.request({
            url: projectUrl + '/MeetingSummary/show',
            async:false,
            data:{
                'id':$("#document_id").val()
            },
        }, function (result) {
            //UE.getEditor('editor').execCommand('insertHtml', result.html_content)
            ueditor.addListener("ready", function () {
                    // editor准备好之后才可以使用
                    ueditor.setContent(result.html_content);
            });
            $.each(result.filejson,function(k,v){
                var filename = v.name.substr(v.name.lastIndexOf("/")+1, v.name.length);
                $("#upload_files").append("<div id='file"+filekey+"' class='files'><a href='javascript:void(0)' onclick='download_file("+data.file_id+")'>"+filename+"</a><a href='javascript:void(0)' onclick='deletefile("+filekey+","+v.file_id+",1)' ><img src='/MeetingManage/images/disagree_status.png'></a></div>");
                filekey++;
            });
            $("#file-show").data("file", result.filejson);
        });        
    }
    else{
        $("#submit2").data("document_id",0);
    }
    //上传文件
    $("#upload_file_button").click(function(){
        MyUpload.request({
            singleFileUploads: true,
            postfix: 'doc,docx,xlsx,xls,png,jpg,jpeg,gif',
            myData: { folder: 'project','s_id':$("#hide_sid").val() }
        }, function (data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress-box').show();
            $('#progress .progress-bar').css('width', progress + '%');
            $('#progress-box').fadeOut(2000);
        }, function (data) {
            if (data.result == "1") {
    /*            if (filekey == 0) {*/
                    $("#upload_files").append("<div id='file"+filekey+"' class='files'><a href='javascript:void(0)' onclick='download_file("+data.file_id+")'>"+data.filename+"</a><a href='javascript:void(0)' onclick='deletefile("+filekey+","+data.file_id+",1)' ><img src='/MeetingManage/images/disagree_status.png'></a></div>");
    /*            }
                else {
                    $("#file-show").append("<div id='file"+filekey+"'>" + data.filename+"<a href='javascript:void(0)' onclick='deletefile("+filekey+","+data.file_id+")'><img src='/LettersFromTheMasses/images/delete_file.png'></a></div>");
                }*/
                filekey++;
            }
            else {
                layer.alert("上传失败");
            }
            filearr = { name: data.filename, dir: data.filedir, file_id: data.file_id};
            if ($("#file-show").data("file") != undefined) {
                filejson = $("#file-show").data("file");
            }
            filejson.push(filearr);
            $("#file-show").data("file", filejson);
        });
    });
    //下一步按钮
    $("#next").click(function(){
        $("#editbox,#upload_file_box").fadeOut(800,function(){
            $("#submit2").hide();
            $("#yes_or_no_audit").fadeIn(800);
        });
        switch($("#submit2").attr("audit"))
        {
            case "yes":
            console.log($("#choose_audit_xm").val());
            if ($("#choose_audit_xm").val() != "") {
                //$("#submit2").show();
                $("#submit2").css("display","");
                console.log($("#submit2").val());
            }
            else{
                console.log(1111);
                $("#submit2").hide();
            }
            break;
            case "no":
            $("#submit2").show();
            break;
            case 0:
            $("#submit2").hide();
            break;
            default:
            $("#submit2").hide();
        }
    });    
    //上一步按钮
    $("#prev").click(function(){
        $("#yes_or_no_audit").fadeOut(800,function(){
            $("#editbox").fadeIn(800);
            $("#upload_file_box").fadeIn(800);
        });
    }); 
    //选择人员
    $.openSelect('#choose', {
        user: {
            hideSelect: "#choose_audit_youxiang",
            showSelect: "#choose_audit_xm",
            hideType: "youxiang",
            showType: "xm",
            selectedMulti: false
        },
        onEnd: function(youxiang, xm) { 
            $("#audit_xm").html(xm); 
            $("#submit2").show();     
        },
        closeDialog: function () {
        },
    });
    //会议总结提交
    $("#submit2").click(function(){
        var htmlcontent = ""; 
        htmlcontent = ueditor.getContent();
        if (htmlcontent == "") {
            layer.alert("不能提交空总结");
        }
        else{
            if ($("#submit2").attr("audit") != "0") {
                var audit_man = ($("#submit2").attr("audit") == "yes")?$("#choose_audit_youxiang").val():"no";
                layer.confirm("确定要提交会议总结？",function(){
                    MyAjax.request({
                        url: projectUrl + '/MeetingSummary/submit',
                        data: {
                            'htmlcontent': htmlcontent,
                            'note_id':$("#submit2").data("document_id"),
                            's_id':$("#s_id").val(),
                            'audit_man':audit_man,
                            'filejson':$("#file-show").data("file") 
                        }
                    }, function (data) {
                        console.log($("#submit2").data("document_id"));
                        if ($("#submit2").data("document_id") == 0) {
                            if (data.result != 0) {
                                $("#submit2").data("document_id",data.result);
                                layer.alert("提交会议总结成功", {
                                        skin: 'llayui-layer-lan', //样式类名
                                        closeBtn: 0,
                                        shade:0.3
                                    },function(){
                                        window.close();
                                });
                            }
                            else{
                                layer.alert("提交会议总结失败",{
                                        skin: 'llayui-layer-lan', //样式类名
                                        closeBtn: 0,
                                        shade:0.3
                                    },function(){
                                        window.close();
                                });
                            }
                        }
                        else{
                            if (data.result != 0) {
                                layer.alert("提交会议总结覆盖成功",{
                                        skin: 'llayui-layer-lan', //样式类名
                                        closeBtn: 0,
                                        shade:0.3
                                    },function(){
                                        window.close();
                                });
                            }
                            else{
                                layer.alert("提交会议总结覆盖失败",{
                                        skin: 'llayui-layer-lan', //样式类名
                                        closeBtn: 0,
                                        shade:0.3
                                    },function(){
                                        window.close();
                                });
                            }                    
                        }
                    });           
                })
            }
            else{
                layer.alert("提交失败，未选择是否审核！",{
                    skin: 'llayui-layer-lan', //样式类名
                    closeBtn: 0                
                });
            }
        }
    });
    //选择是否审批按钮
    $('#solutions li').click(function(){
        if ($(this).attr("status") == "yes_audit")
        {
            $('#solutions .solutit2').stop().animate({
                height:'0'
            },600);
            $(this).find('.solutit2').stop().animate({
                height:'300'

            },600);
            $(this).find(".solutit").find("p").css("color","#ec8000");
            $(this).find(".solutit").find("a").css("background","#ec8000");
            $(this).find(".solutit").find("img").css("background","#ec8000");
            $(this).find(".solutit2").css("border-color","#ec8000");
            $(this).find(".solutit2").find("h5").css("color","#ec8000");
            $(this).prev().find(".solutit").find("p").css("color","#72ac2d");
            $(this).prev().find(".solutit").find("a").css("background","#72ac2d");
            $(this).prev().find(".solutit").find("img").css("background","#72ac2d");
            $(this).prev().find(".solutit2").css("border-color","#72ac2d");
            $(this).prev().find(".solutit2").find("h5").css("color","#72ac2d");
            $("#submit2").attr("audit","yes");
            show_submit2();
        }
        else
        {
            $(this).find(".solutit").find("p").css("color","#ec8000");
            $(this).find(".solutit").find("a").css("background","#ec8000");
            $(this).find(".solutit").find("img").css("background","#ec8000");
            $(this).find(".solutit2").css("border-color","#ec8000");
            $(this).find(".solutit2").find("h5").css("color","#ec8000");
            $(this).next().find(".solutit").find("p").css("color","#72ac2d");
            $(this).next().find(".solutit").find("a").css("background","#72ac2d");
            $(this).next().find(".solutit").find("img").css("background","#72ac2d");
            $(this).next().find(".solutit2").css("border-color","#72ac2d");
            $(this).next().find(".solutit2").find("h5").css("color","#72ac2d");
            $("#submit2").attr("audit","no");
            show_submit2();
            $('#solutions .solutit2').stop().animate({
                height:'0'
            },600);     
        }
    });
});
//下一步按钮是否显示按钮
function show_submit2(){
    switch($("#submit2").attr("audit"))
    {
        case "yes":
        console.log($("#choose_audit_xm").val());
        if ($("#choose_audit_xm").val() != "") {
            $("#submit2").show();
        }
        else{
            $("#submit2").hide();
        }
        break;
        case "no":
        $("#submit2").show();
        break;
        case 0:
        $("#submit2").hide();
        break;
        default:
        $("#submit2").hide();
    } 
}
//删除上传文件
//参数1为文件的个数，参数2为文件id，参数引用多少次upload插件
function deletefile(filekey,file_id,cishu)
{
    if ($("#document_id").val() != 0) {
        MyAjax.request({
            url: projectUrl + '/SelfNote/deletefile',
            data: {
                'id': $("#document_id").val(),
                'file_id':file_id
            }
        }, function(result) {
            if (result == 1) {
                $("#file-show").data("file").splice($("#file-show div").index($("#file"+filekey)),1);
                $("#file"+filekey).remove();
            }
            else{
                layer.msg("删除失败");
            }
        });
    }
    else{
        $("#file-show").data("file").splice($("#file-show div").index($("#file"+filekey)),1);
        $("#file"+filekey).remove();
    }
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


