<iframe src="http://<?=$_SERVER['HTTP_HOST']?>/Schedule/index.php/scheduling/Scheduling_Index/my_schedule_detail/<?=$s_id?>/meeting_approval" id="iframepage" name="iframepage" frameborder="0" scrolling="auto" marginheight="0" marginwidth="0" width="100%" height="500px"></iframe>
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