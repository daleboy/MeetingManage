<style>
#files_box { width: 800px;height: 50px;overflow-x: auto;overflow-y:visible;position: absolute;bottom: 0px;border: 1px solid #999;background-color: #F2F2F2;}
#view_div { display: none; overflow-y: auto; }
#files_box_name {width: 50px;height: 50px;float: left;font-size: 18px;background-color: #6495ED;color: #FFFFFF;padding-left: 6px;}
#files_content_box {width: 90%;float: right;}
.file {width: 130px;height: 48px;text-overflow: ellipsis;float: left;white-space: nowrap;overflow: hidden;padding: 5px;border: 2px dashed #555;margin-right: 5px;background-color: #fff;cursor:pointer;}
#view_content{max-height: 550px;overflow-y:auto;}
</style>
<div class="layout">
    <div class="line">
        <div id="divHeight" class="x2 border-right border-dashed padding-big">
            <ul id="year_tree" class="ztree"></ul>
        </div>
        <div class="x10 padding-big">
            <div class="line margin-bottom">
                <div class="x4 x8-move">
                    <form>
                        <div class="input-group padding-little-top">
                            <input type="text" class="input border-blue" id="keywords" size="30" placeholder="会议总结资料关键词" />
                            <span class="addbtn">
                                <button type="button" class="button bg-blue" id="search">搜索</button></span>
                        </div>
                    </form>
                </div>
            </div>
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
                <tbody id="report_list_tbody">
                    <tr>
                        <td colspan="5" align="center">选择日期查看会议总结</td>
                    </tr>
                </tbody>
                <tfoot>
                    <input type="hidden" id="tree_id" />
                    <input type="hidden" id="report_list_count" />
                    <tr>
                        <td colspan="6" align="center" id="report_list_page"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<div id="view_div">
    <div id="view_content" class="padding">
    </div>
    <div id="files_box">
        <div id="files_box_name">总结附件</div>
        <div id="files_content_box"></div>
    </div>
</div>
<link rel="stylesheet" href="<?=base_url()?>css/zTreeStyle/zTreeStyle.css" type="text/css" />
<script type="text/javascript" src="<?=base_url()?>js/jquery.ztree.core-3.5.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/meeting_report.js"></script>