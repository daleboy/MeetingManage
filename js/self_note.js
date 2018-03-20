var ue = UE.getEditor('editor');
var filekey = 0;
var filejson = [];
$(function() {
    showlist(1);
    //获取个人笔记内容
    if ($("#document_id").val() != 0) {
        $("#submit").data("document_id", $("#document_id").val());
        MyAjax.request({
            url: projectUrl + '/SelfNote/show',
            data: {
                'id': $("#document_id").val()
            }
        }, function(result) {
            UE.getEditor('editor').execCommand('insertHtml', result.html_content);
            $.each(result.filejson,function(k,v){
                var filename = v.name.substr(v.name.lastIndexOf("/")+1, v.name.length);
                $("#upload_files").append("<div id='file"+filekey+"' class='files'><a href='javascript:void(0)' onclick='download_file("+v.file_id+")'>"+filename+"</a><a href='javascript:void(0)' onclick='deletefile("+filekey+","+v.file_id+",1)' ><img src='/MeetingManage/images/disagree_status.png'></a></div>");
                filekey++;
            });
            $("#file-show").data("file", result.filejson);
        });
    } else {
        $("#submit").data("document_id", 0);
    }

    //提交个人笔记
    $("#submit").click(function() {
        layer.confirm("确定要提交个人笔记？", function() {
            var htmlcontent = "";
            htmlcontent = UE.getEditor('editor').getContent();
            MyAjax.request({
                url: projectUrl + '/SelfNote/submit',
                data: {
                    'htmlcontent': htmlcontent,
                    'note_id': $("#submit").data("document_id"),
                    'filejson':$("#file-show").data("file") 
                }
            }, function(data) {
                if ($("#submit").data("document_id") == 0) {
                    if (data.result != 0) {
                        $("#submit").data("document_id", data.result);
                        layer.alert("提交个人笔记成功");
                    } else {
                        layer.alert("提交个人笔记失败");
                    }
                } else {
                    if (data.result != 0) {
                        layer.alert("提交个人笔记覆盖成功");
                    } else {
                        layer.alert("提交个人笔记覆盖失败");
                    }
                }
            });
        })
    });
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
});
//列表点击编辑个人笔记
//参数1为文档id
function edit(id) 
{
    window.location.href = projectUrl + "SelfNote/index?id=" + id;
}
//点击更多目录
function more() 
{
    $("#editbox").fadeOut("slow", function() {
        showlist(1, 1);
        $("#new_publish").stop().animate({ height: 800 }, 2000, function() {
            laypage({
                cont: 'my_list_page',
                curr: parseInt($('#now_page').val()),
                pages: Math.ceil(parseInt($('#my_list_count').val()) / perPageNum),
                jump: function(obj, first) {
                    if (!first) {
                        showlist(obj.curr, 1);
                    }
                }
            });
        });
    });

}
//个人笔记列表
//参数1为文件页码，参数2是否更多，
function showlist(page, more) {
    var more = (more == undefined) ? 0 : more;
    MyAjax.request({
        url: projectUrl + '/SelfNote/showlist',
        data: {
            'page': page,
            'more': more
        }
    }, function(result) {
        var list = "";
        if (result.list.length == 0) {
            list = "<tr><td colspan='2'>暂无数据</td></tr>";
        }
        $.each(result.list, function(k, v) {
            list += "<tr><td style='max-width: 800px'><p class='text-more'>" + v.html_content + "</p></td><td>" + v.publish_strtime + "<button class='button bg-red radius-rounded button-little' style='float:right' onclick='deleteDocument(\"" + v.id + "\",this)'>删除</button><button class='button bg-sub radius-rounded button-little' style='float:right' onclick='edit(\"" + v.id + "\")'>编辑</button></td></tr>";
        });
        if (result.more == "yes") {
            $("#more").html("<a href='javascript:void(0);' onclick='more()'>查看更多</a>");
        } else {
            $("#more").html("");
        }
        $("#now_page").val(page);
        $("#my_list_count").val(result.count);
        $("#list").html(list);
    });
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
//删除个人笔记文档
//参数1为文档id，参数2为按钮对象
function deleteDocument(document_id,index)
{
    MyAjax.request({
        url: projectUrl + '/SelfNote/deleteDocument',
        data: {
            'id': document_id
        }
    }, function(result) {
        if (result == 1) {
            layer.msg("删除成功",function(){
                MyAjax.request({
                    url: projectUrl + '/SelfNote/show',
                    data: {
                        'id': 0
                    }
                }, function(result) {
                    UE.getEditor('editor').setContent('', '');
                    $(index).parents("tr").remove();
                    $("#document_id").val(0);
                });
            });
        }
        else{
            layer.msg("删除失败");
        }
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