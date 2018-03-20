<iframe src="http://147.1.4.90/Schedule/index.php/scheduling/scheduling_index/lawsuit_case_submit/<?=$id?>" id="iframepage" name="iframepage" frameborder="0" scrolling="auto" marginheight="0" marginwidth="0" width="100%" height="500px"></iframe>
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