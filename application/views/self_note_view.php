<script type="text/javascript" src="<?=base_url()?>js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/jquery.fileupload.js"></script>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/uploadfile.js"></script>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/ueditor/ueditor.all.min.js"> </script>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/ueditor/lang/zh-cn/zh-cn.js"></script>
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
	<button class="button button-block button-large" id="submit">提交</button><input type="hidden" value="<?=$document_id?>" id="document_id" />
</div>
<div id="new_publish">
	<p>最近发布</p>
	<p id="more" style="float: right"></p>
	<table class="table table-condensed">
		<tr>
			<th width="70%" style="max-width: 800px">
				标题
			</th>
			<th>
				发布时间
			</th>
		</tr>
		<tbody id="list">
		<tr>
			<td>
				...
			</td>
			<td>
				...
			</td>
		</tr>
		</tbody>
	    <tfoot>
	        <input type="hidden" id="my_list_count" value="0" />
	        <input type="hidden" id="now_page" value="1" />
	        <tr>
	            <td colspan="6" align="center" id="my_list_page"></td>
	        </tr>
	    </tfoot>
	</table>
</div>
<script type="text/javascript" charset="utf-8" src="<?=base_url()?>/js/self_note.js"></script>



