<style>
.table tbody tr td .button { margin: 0 5px; }
#audit_div { display: none; }
</style>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th>会议名称</th>
            <th>举办地点</th>
            <th>举办时间</th>
            <th>指定参会人员或部门</th>
            <th>申请时间</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody id="my_audit_tbody">
    
    </tbody>
    <tfoot>
        <input type="hidden" id="my_audit_count" value="<?=$my_audit_count?>" />
        <input type="hidden" id="approval-select-person-youxiang" />
        <input type="hidden" id="approval-select-person-xm" />
        <tr>
            <td colspan="6" align="center" id="my_audit_page"></td>
        </tr>
    </tfoot>
</table>
<div id="audit_div">
    <div class="line padding">
        <div class="x2 text-right">审核意见：</div>
        <div class="x10">
            <textarea rows="5" class="input" id="suggestion"></textarea>
        </div>
    </div>
    <div class="text-center">
        <button class="button margin-right bg-main" id="agree">审核通过</button>
        <button class="button margin-right bg-dot" id="disagree">审核不通过</button>
        <button class="button bg-blue" id="passon">转交审核</button>
    </div>
</div>
<script src="<?=base_url()?>/js/select_user.js"></script>
<script src="<?=base_url()?>js/meeting_audit.js" type="text/javascript"></script>