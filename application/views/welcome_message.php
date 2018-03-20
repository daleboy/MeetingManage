<style>
    .show_num{width: 45px; height: 28px; color: white; position: absolute; background: url("<?=base_url()?>images/xx_bg.png") no-repeat;}
    .menu_name {font-size: 18px;}
</style>
<div class="container">
    <div class="line show_menu">
        <div class="xs4 xm3 xb3 text-center margin-large-top">
            <a href="http://147.1.4.53:8191" target="_blank"><img src="images/hysgl.png" width="107" height="107" /></a><br/><span class="menu_name">会议室管理</span>
        </div>
        <div class="xs4 xm3 xb3 text-center margin-large-top">
            <a href="<?=base_url()?>index.php/MeetingApply" target="_blank"><img src="images/hysq.png" width="107" height="107" /></a><br/><span class="menu_name">会议室申请</span>
        </div>
        <div class="xs4 xm3 xb3 text-center margin-large-top">
            <a href="<?=base_url()?>index.php/MeetingAudit" target="_blank" class="badge-corner"><img src="images/hysh.png" width="107" height="107" id="meeting_audit_img" /><span class="badge bg-red" id="meeting_audit_num"><?=$meeting_audit?></span></a><br/><span class="menu_name">会议室审核</span>
        </div>
        <?php if ($user_nq): ?>
        <div class="xs4 xm3 xb3 text-center margin-large-top">
            <a href="<?=base_url()?>index.php/MeetingSignUp" target="_blank" class="badge-corner"><img src="images/hybm.png" width="107" height="107" id="meeting_signup_img" /><span class="badge bg-red" id="meeting_signup_num"><?=$sign_up?></span></a><br/><span class="menu_name">会议报名</span>
        </div>
        <?php endif; ?>
        <div class="xs4 xm3 xb3 text-center margin-large-top">
            <a href="<?=base_url()?>index.php/MyMeetingApply" target="_blank"><img src="images/wdhysq.png" width="107" height="107" /></a><br/><span class="menu_name">我的会议申请</span>
        </div>
        <div class="xs4 xm3 xb3 text-center margin-large-top">
            <a href='<?=base_url()?>index.php/MyJoinMeeting' target="_blank" class="badge-corner"><img src="images/hycx.png" width='107' height="107" id="meeting_attend_img" /><span class="badge bg-red" id='meeting_attend_num'><?=$my_join_meeting_count?></span></a><br /><span class="menu_name">参加会议</span>
        </div>
        <?php if ($occupy_apply_count > 0): ?>
        <div class="xs4 xm3 xb3 text-center margin-large-top">
            <a href="<?=base_url()?>index.php/OccupyApply" target="_blank" class="badge-corner"><img src="images/hysq.png" width="107" height="107" /><span class="badge bg-red"><?=$occupy_apply_count?></span></a><br/><span class="menu_name">会议室占用申请</span>
        </div>
        <?php endif; ?>
        <div class="xs4 xm3 xb3 text-center margin-large-top">
            <a href="<?=base_url()?>index.php/SelfNote" target="_blank"><img src="images/grbj.png" width="107" height="107" /></a><br/><span class="menu_name">个人笔记</span>
        </div>
        <div class="xs4 xm3 xb3 text-center margin-large-top">
            <a href="<?=base_url()?>index.php/MeetingSummary" target="_blank"><img src="images/hyzjzl.png" width="107" height="107" /></a><br/><span class="menu_name">会议总结资料</span>
        </div>
        <div class="xs4 xm3 xb3 text-center margin-large-top">
            <a href="<?=base_url()?>index.php/MeetingReportAudit" target="_blank" class="badge-corner"><img src="images/zlsh.png" width="107" height="107" id="meeting_record_audit_img" /><span class="badge bg-red" id="meeting_record_audit_num"><?=$report_audit_count?></span></a><br/><span class="menu_name">会议总结资料审核</span>
        </div>
        <div class="xs4 xm3 xb3 text-center margin-large-top">
            <a href="<?=base_url()?>index.php/MeetingReport" target="_blank"><img src="images/gzcx.png" width="107" height="107" /></a><br/><span class="menu_name">会议总结资料查询</span>
        </div>
        <div class="xs4 xm3 xb3 text-center margin-large-top">
            <a href="<?=base_url()?>index.php/MeetingView" target="_blank"><img src="images/lsjl.png" width="107" height="107" /></a><br/><span class="menu_name">会议总览</span>
        </div>
    </div>
</div>
<script>
function RefreshPage()
{
    window.location.reload();
}
setTimeout('RefreshPage()', 300000);
</script>