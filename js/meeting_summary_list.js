$(function(){
    showlist(1,1);
    laypage({
        cont: 'my_list_page',
         curr: parseInt($('#now_page').val()),
        pages: Math.ceil(parseInt($('#my_list_count').val()) / perPageNum),
        jump: function (obj, first) {
            if (!first) {
                showlist(obj.curr,1);
            }
        }
    });
});
function edit(id,s_id){
    window.open(projectUrl+"/MeetingSummary/edit_index?id="+id+"&s_id="+s_id);
}

function show(id){
    window.open(projectUrl+"/MeetingSummary/show_index?id="+id);
}


function showlist(page,more){
    more = (more == undefined)?0:more;
    MyAjax.request({
        url: projectUrl + "/MeetingSummary/showlist",
        data:{
            'page':page,
            'more':more
        },
        async:false
    }, function (result) {
        var list = "";
        if (result.list.length == 0) {
            list = "<tr><td colspan='5' style='text-align:center'>暂无数据</td></tr>";
        }
        $.each(result.list,function(k,v){
            i = parseInt(k)+1+(parseInt(page)-1)*perPageNum;
            if (v.summary_status == 1) {
                v.summary_status = "<img src='../images/yitijiao.png' title='已提交'>";
            }
            else{
                v.summary_status = "<img src='../images/weitijiao.png' title='未提交'>";
            }
            list += "<tr><td>"+i+"</td><td><p class='text-more'>"+v.title+"</p></td><td>"+v.start_time+"</td><td>"+v.summary_status+"</td><td>"+v.publish_strtime+"<button class='button bg-sub radius-rounded button-small' style='float:right' onclick='show(\""+v.id+"\")'>查看</button>";
            if (result.user_id == v.apply_id && (v.flow_status != 1 && v.flow_status != 2)) {
                list += "<button class='button bg-sub radius-rounded button-small' style='float:right' onclick='edit(\""+v.id+"\",\""+v.s_id+"\")'>编辑</button></td></tr>";
            }
            else{
                list += "</td></tr>";
            }
        });
        if (result.more == "yes") {
            $("#more").html("<a href='javascript:void(0);' onclick='more()'>查看更多</a>");
        }
        else{
            $("#more").html("");
        }
        $("#now_page").val(page);
        $("#my_list_count").val(result.count);
        $("#list").html(list);
    });
}