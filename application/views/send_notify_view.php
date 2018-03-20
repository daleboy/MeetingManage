<iframe src="http://147.1.3.133/general/notify/manage/modify_interface.php?NOTIFY_ID=<?=$notify_id?>&CUR_PAGE=1" id="iframepage" name="iframepage" frameborder="0" scrolling="auto" marginheight="0" marginwidth="0" width="100%" height="500px"></iframe>
<script>
$(function() {
    $("#iframepage").load(function(){
        ChangeIframeHeight();
    }); 
    window.onresize = function(){
        ChangeIframeHeight();
    }
});
function ChangeIframeHeight()
{
    var mainheight = $(window).innerHeight() - 93;
    $("#iframepage").height(mainheight);
}
</script>