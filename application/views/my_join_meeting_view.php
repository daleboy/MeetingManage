<style>
.table tbody tr td .button { margin: 0 5px; }
#remind_div { display: none; }
#remind_div table { margin: 10px; width: 97%; }
#remind_div table tr td { height: 30px; }
</style>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>会议名称</th>
            <th>举办地点</th>
            <th>举办时间</th>
            <th>距离会议开始还有</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody id="my_join_meeting_tbody">
    
    </tbody>
    <tfoot>
        <input type="hidden" id="my_join_meeting_count" value="<?=$all_can_join_meeting_count?>" />
        <tr>
            <td colspan="5" align="center" id="my_join_meeting_page"></td>
        </tr>
    </tfoot>
</table>
<script src="<?=base_url()?>js/my_join_meeting.js" type="text/javascript"></script>