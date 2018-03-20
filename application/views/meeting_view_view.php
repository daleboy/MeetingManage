<div class="layout">
    <div class="line">
        <div id="divHeight" class="x2 border-right border-dashed padding-big">
            <ul id="year_tree" class="ztree"></ul>
        </div>
        <div class="x10 padding-big">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>会议名称</th>
                        <th>举办地点</th>
                        <th>举办时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="meeting_list_tbody">
                    <tr>
                        <td colspan="5" align="center">选择日期查看记录</td>
                    </tr>
                </tbody>
                <tfoot>
                    <input type="hidden" id="tree_id" />
                    <input type="hidden" id="meeting_list_count" />
                    <tr>
                        <td colspan="6" align="center" id="meeting_list_page"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?=base_url()?>css/zTreeStyle/zTreeStyle.css" type="text/css" />
<script type="text/javascript" src="<?=base_url()?>js/jquery.ztree.core-3.5.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/meeting_view.js"></script>