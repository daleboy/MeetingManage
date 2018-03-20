<style>
.table tbody tr td .button { margin: 0 5px; }
#remind_div { display: none; }
#remind_div table { margin: 10px; width: 97%; }
#remind_div table tr td { height: 30px; }
#notify_div { display: none; height:600px;}
#notify_div table { margin: 10px; width: 97%; }
#notify_div table tr td { height: 30px; }
#upload_div { display: none; }
#upload_div table { margin: 10px; width: 97%; }
#upload_div table tr td { height: 30px; }
</style>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th>会议名称</th>
            <th>举办地点</th>
            <th>举办时间</th>
            <th>指定参会人员或部门</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody id="my_list_tbody">
    
    </tbody>
    <tfoot>
        <input type="hidden" id="my_list_count" value="<?=$my_list_count?>" />
        <tr>
            <td colspan="6" align="center" id="my_list_page"></td>
        </tr>
    </tfoot>
</table>
<div id="remind_div">
    <table>
        <tbody>
            <tr>
                <td align="right" width="14%">会议名称：</td>
                <td id="remind_meeting_name" align="left"></td>
            </tr>
            <tr>
                <td align="right">举办地点：</td>
                <td id="remind_meeting_address" align="left"></td>
            </tr>
            <tr>
                <td align="right">举办日期：</td>
                <td id="remind_meeting_date" align="left"></td>
            </tr>
            <tr>
                <td colspan="2"><hr class="bg-blue" /></td>
            </tr>
            <tr>
                <td align="right">参会人员：</td>
                <td id="remind_join_person" align="left"></td>
            </tr>
            <tr>
                <td align="right">参会部门：</td>
                <td id="remind_join_dept" align="left"></td>
            </tr>
            <tr>
                <td colspan="2"><hr class="bg-blue" /></td>
            </tr>
            <tr>
                <td align="right">技术保障：</td>
                <td id="remind_technology" align="left"></td>
            </tr>
            <tr>
                <td align="right">后勤保障：</td>
                <td id="remind_tea" align="left"></td>
            </tr>
            <tr>
                <td colspan="2"><hr class="bg-blue" /></td>
            </tr>
            <tr>
                <td align="right">提醒历史：</td>
                <td id="remind_history" align="left"></td>
            </tr>
            <tr>
                <td colspan="2"><hr class="bg-blue" /></td>
            </tr>
            <tr>
                <td align="right">提醒设置：</td>
                <td align="left" id="remind_setup">
                    <label>
                        <input type="checkbox" name="email" value="1">邮件&nbsp;
                    </label>
                    <label>
                        <input type="checkbox" name="message" value="1" checked="checked">短消息&nbsp;
                    </label>
                    <label>
                        <input type="checkbox" name="sms" value="1">短信
                    </label>
                </td>
            </tr>
            <tr>
                <td align="right">提醒时间：</td>
                <td align="left">
                    <form class="form-auto">
                        <div class="form-group">
                            <div class="field field-icon">
                                <input type="text" class="input" placeholder="点击选择提醒时间" id="remind_time" size="30" value="<?=date('Y-m-d H:i:s')?>" onclick="laydate({istime: true, min: laydate.now(), format: 'YYYY-MM-DD hh:mm:ss'})" />
                                <span class="icon icon-calendar"></span>
                            </div>
                        </div>
                    </form>
                </td>
            </tr>
            <tr>
                <td align="right">提醒内容：</td>
                <td align="left">
                    <textarea rows="5" class="input" id="remind_content"></textarea>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" align="center">
                    <button class="button margin-top" id="remind_send">发送</button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<div id="notify_div">       
    <input type="hidden" id="hide_sid" value=""/>
    <table>
        <tbody>
            <tr>
                <td align="right" width="20%">通告标题：</td>
                <td id="notify_name_td" align="left">
                    <input type="text" value="" class="input" id="notify_name_input"/>
                </td>
            </tr>
            <tr>
                <td align="right" class="padding-top">发布范围（部门）：</td>
                <td id="publish_dept" align="left" class="padding-top">
                    <input type="text" value="" class="input"  id="publish_dept_input" readonly="readonly"/>
                    <input type="hidden" value="" id="hide_publish_dept_input"/>
                    <button class="button margin-top" id="select_self_court_button">本院各部门</button>
                    <button class="button margin-top" id="select_all_court_button">全区法院及部门</button>
                    <button class="button margin-top" id="add_dept">添加</button>
                    <button class="button margin-top" id="remove_dept">清除</button>
                </td>
            </tr>
            <tr>
                <td align="right" class="padding-top">发布范围（人员) ：</td>
                <td id="publish_user_td" align="left" class="padding-top">
                    <input type="text" value="" class="input" id="publish_user_input" readonly="readonly"/>
                    <input type="hidden" value="" id="hide_publish_user_input"/>
                    <button class="button margin-top" id="add_user">选择</button>
                    <button class="button margin-top" id="remove_user">清空</button>
                </td>
            </tr>
            <tr>
                <td colspan="2"><hr class="bg-blue" /></td>
            </tr>
            <tr>
                <td align="right">有效期（开始）：</td>
                <td id="startdate_td" align="left">
                    <input type="text" value="" class="input" id="startdate_input" readonly="readonly"/>
                </td>
            </tr>
            <tr>
                <td align="right">有效期（结束）：</td>
                <td id="enddate" align="left">
                    <input type="text" value="" class="input" id="enddate_input" readonly="readonly"/>
                </td>
            </tr>
            <tr>
                <td colspan="2"><hr class="bg-blue" /></td>
            </tr>
            <tr>
                <td align="right">上传附件：</td>
                <td id="document" align="left">
                    <p style="color: coral;margin-bottom: 0px;">只支持xls，xlsx，doc，docx，zip，图片类型上传</p>
                    <div class="bootstrap-filestyle input-group">
                        <div id="file-show" class="form-control " disabled="" type="text">
                        </div> 
                        <a class="button input-file" href="javascript:void(0);">+ 浏览文件<input id="fileupload" type="file" name="file[]"  multiple></a>
                    </div>

                    <div class="form-group" id="progress-box" style="display: none">
                        <div id="progress" class="progress progress-small">
                            <div class="progress-bar bg-yellow" style="width: 0%;"> </div> 
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2"><hr class="bg-blue" /></td>
            </tr>
            <tr>
                <td colspan='2' align="right">
                    <script id="editor" type="text/plain" style="width:100%;height:160px;"></script>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button class="button margin-top" id="send_notify">发送</button>
                    <button class="button margin-top close_upload" >关闭</button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<div id="upload_div">       
    <input type="hidden" id="hide_sid2" value=""/>
    <table>
        <tbody>
            <tr>
                <td align="right" style="vertical-align:middle;">上传附件：</td>
                <td id="document" align="left" style="vertical-align:middle;">
                    <div class="bootstrap-filestyle input-group">
                        <div id="file-show2" class="form-control " disabled="" type="text">
                        </div> 
                        <a class="button input-file" href="javascript:void(0);">+ 浏览文件<input id="fileupload2" class="file2" type="file" name="file[]"  multiple></a>
                    </div>

                    <div class="form-group" id="progress-box2" style="display: none">
                        <div id="progress2" class="progress progress-small">
                            <div class="progress-bar bg-yellow" style="width: 0%;"> </div> 
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2"><hr class="bg-blue" /></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button class="button margin-top close_upload" >关闭</button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<script src="<?=base_url()?>js/laydate/laydate.js" type="text/javascript"></script>
<script src="http://147.1.3.69:88/js/jquery/select/select.2.0.js"></script>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/ueditor/ueditor.all.min.js"> </script>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/ueditor/lang/zh-cn/zh-cn.js"></script>
<script src="<?=base_url()?>js/jquery.ui.widget.js"></script>
<script src="<?=base_url()?>js/jquery.iframe-transport.js"></script>
<script src="<?=base_url()?>js/jquery.fileupload.js"></script>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/uploadfile.js"></script>
<script type="text/javascript">
    var ue = UE.getEditor('editor');
</script>
<script src="<?=base_url()?>js/my_meeting_apply.js" type="text/javascript"></script>