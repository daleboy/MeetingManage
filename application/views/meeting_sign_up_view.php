<style>
.table tbody tr td .button { margin: 0 5px; }
#dept_tree_div { display: none; }
.tab-body { overflow-y: auto; }
.users_show_inline { display: inline; }
.del_chose_user { display: none; cursor: pointer; }
</style>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th>会议名称</th>
            <th>举办地点</th>
            <th>举办时间</th>
            <th>部门名额</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody id="sign_up_tbody">
        
    </tbody>
    <tfoot>
        <input type="hidden" id="sign_up_count" value="<?=$sign_up_count?>" />
        <tr>
            <td colspan="6" align="center" id="sign_up_page"></td> 
        </tr>
    </tfoot>
</table>
<div id="dept_tree_div">
    <div class="line">
        <div class="x5 padding">
            <div class="panel" style="height: 400px;">
                <div class="panel-head">本部门及子部门</div>
                <div class="panel-body">
                    <ul id="dept_tree" class="ztree"></ul>
                </div>
            </div>
        </div>
        <div class="x7 padding-top padding-right padding-bottom">
            <div class="tab">
                <div class="tab-head">
                    <ul class="tab-nav">
                        <li class="active"><a href="#select-user">用户</a></li>
                    </ul>
                </div>
                <div class="tab-body tab-body-bordered" style="height: 365px;">
                    <div class="tab-panel active" id="select-user">
                        <div class="list-link" id="select-user-list">
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="line">
        <div class="x12 padding-left padding-right">
            <span id="show_dept_name"></span>
            <span id="can_sign_up_num"></span>
        </div>
    </div>
    <div class="line">
        <div class="x12 margin-big-top text-center">
            <button class="button" id="clear_chose">取消</button>
            <button class="button bg-blue" id="save_chose">保存</button>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?=base_url()?>css/zTreeStyle/zTreeStyle.css" type="text/css" />
<script type="text/javascript" src="<?=base_url()?>js/jquery.ztree.core-3.5.min.js"></script>
<script src="<?=base_url()?>js/meeting_sign_up.js" type="text/javascript"></script>