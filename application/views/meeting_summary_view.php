<script type="text/javascript" src="<?=base_url()?>js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/jquery.fileupload.js"></script>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/uploadfile.js"></script>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/ueditor/ueditor.all.min.js"> </script>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/ueditor/lang/zh-cn/zh-cn.js"></script>
<script src="http://147.1.4.52:5200/js/select/selectuser.js"></script>
<style>
*{ margin:0; padding:0; list-style:none;}
body{ font-family:"微软雅黑";}
img{ border:0;}
.wrap{ width:1120px; margin:0 auto;}
.solutions{width:920px;overflow: hidden;margin: 100px auto}
.solutions ul{width:900px}
.solutions li{height:300px;background: #fff; width:263px;border:1px solid #e5e5e5;border-bottom: 5px solid #efefef;float: left; margin-right:20px; position:relative;}
.solutit{display: block;width:100%;}
.solutit img{ margin:30px auto;text-align: center;display: block;}
.solutit h4{color: #333;font-size: 16px;text-align: center;font-weight: bold;line-height: 30px;}
.solutit p{color: #72ac2d;line-height: 20px;font-size: 14px;text-align: center;}
.solutit a{line-height: 30px;height:30px;width: 100px;background: #72ac2d;color: #fff;font-size: 14px;font-weight: bold;text-align: center;display: block;margin:20px auto 0;border-radius: 5px;}
.solutit:hover a{background: #ec8000;}
.solutit2{width:100%;padding:0 20px;position:absolute;left:0px;top:0px;overflow: hidden;height:0px;background: #fff;z-index: 99;display: block;border-bottom:5px solid #72ac2d;}
.solutit2 h4{color: #333;font-weight: bold;font-size: 16px;line-height: 16px;margin-bottom: 10px;text-align: center;margin-top:40px;}
.solutit2 h5{text-align: center;color: #72ac2d;display: block;}
.solutit2 span{display: block;background: #bbbbbb;height:2px;width:50px;margin:10px auto;}
.solutit2 p{line-height: 24px;color: #666666;height:100px;display: block;overflow: hidden;line-height:100px;text-align:center}
.solutit2 a{line-height: 30px;height:30px;width: 100px;background: #ec8000;color: #fff;font-size: 14px;font-weight: bold;text-align: center;display: block;margin:20px auto 0;border-radius: 5px;}
.solutit2:hover a{background: #ec8000;}
#no_audit {float: left;margin: 0px}
#yes_audit {float: right;margin: 0px}
#submit {margin:50px auto;}
</style>
<div id="upload_file_box">
	<div id="upload_files">
		<form id="upload_form">
			<a id="upload_file_button" class="button input-file bg-blue button-small" href="javascript:void(0);">+ 上传<input id="fileupload" type="file" name="file[]"  multiple></a>
		</form>
	</div>
	<div id="file-show" class="form-control " disabled="" type="text"></div>
</div>
<div id="editbox">
	<script id="editor" type="text/plain" style="width:100%;height:500px;"></script>
	<div class="text-center" style="margin-top: 20px"><button class="button button-large bg-sub radius-rounded" id="next">下一步</button></div>
</div>
<div id="yes_or_no_audit" style="display: none">
	<div class='wrap'>
		<div class='solutions' id="solutions">
			<ul>
				 <li id="no_audit" status='no_audit'>
					<div class="solutit">
						<img src="<?=base_url()?>/images/no_audit.png" style="background:#72ac2d;">
						<h4>无需审核</h4>
						<p>NO NEED AUDIT</p>
						<a class="aaa" href="javascript:void(0)">无需审核</a>
					</div>
					
					<div class="solutit2">
						<h4>品牌优势</h4>
						<h5>BRAND ADVANTAGE</h5>
						<span></span>
						<p>内容信息内容信息内容信息内容信息内容信息内容信息内容信息内容信息内容信息</p>
					</div>
				 </li>
				 <li id="yes_audit" status='yes_audit'>
					<div class="solutit">
						<img src="<?=base_url()?>/images/yes_audit.png" style="background:#72ac2d;">
						<h4>需要审核</h4>
						<p>NEED AUDIT</p>
						<a href="javascript:void(0)">需要审核</a>
					</div>
					
					<div class="solutit2">
						<h4>选择审核人</h4>
						<h5>CHOOSE AUDIT MAN</h5>
						<p id="audit_xm">暂无审核人</p>
						<input type="hidden" id="choose_audit_youxiang" />
						<input type="hidden" id="choose_audit_xm" />
						<a id="choose" class="aaa" href="javascript:void(0)">选择审核人</a>
					</div>
				 </li>
			</ul>
		</div>
	</div>
	<div class="text-center" style="margin-top: 20px"><button class="button button-large bg-sub" id="prev" style="margin: 0 10px 0 10px">上一步</button><button class="button button-large bg-sub" style="margin: 0 10px 0 10px" id="submit2" audit="0">提交</button><input type="hidden" value="<?=$document_id?>" id="document_id" /><input type="hidden" value="<?=$s_id?>" id="s_id" /></div>
</div>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/meeting_summary.js"></script>


