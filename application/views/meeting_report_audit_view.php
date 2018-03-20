<style>
#view_div { display: none; overflow-y: auto; }
</style>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th>标题</th>
            <th>发表人</th>
            <th>会议名称</th>
            <th>发表日期</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody id="audit_tbody">
    
    </tbody>
    <tfoot>
        <input type="hidden" id="audit_count" value="<?=$audit_count?>" />
        <tr>
            <td colspan="5" align="center" id="audit_page"></td>
        </tr>
    </tfoot>
</table>
<div id="view_div">
    <div id="view_content" class="padding"></div>
    <div class="line padding">
        <hr />
        <div class="x2 text-right">审核意见：</div>
        <div class="x10">
            <textarea rows="5" class="input" id="suggestion"></textarea>
        </div>
    </div>
    <div class="text-center">
        <button class="button margin-right bg-main" id="agree">审核通过</button>
        <button class="button bg-dot" id="disagree">审核不通过</button>
    </div>
</div>
<script src="<?=base_url()?>js/meeting_report_audit.js" type="text/javascript"></script>