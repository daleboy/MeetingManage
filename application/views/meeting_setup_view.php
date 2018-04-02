<style>
#meeting-type-current-setup li span, #technology-type-current-setup li span { cursor: pointer; }
</style>
<div class="tab padding" data-toggle="hover">
	<div class="tab-head">
		<ul class="tab-nav">
			<li class="active"><a href="#tab-meeting-type" style="cursor: pointer;">会议类型设置</a></li>
			<li><a href="#tab-technology-type" style="cursor: pointer;">技术保障类型设置</a></li>
		</ul>
	</div>
	<div class="tab-body tab-body-bordered border-dotted">
		<div class="tab-panel active" id="tab-meeting-type">
            <div class="line">
                <div class="x4 padding-right border-right">
                    <div class="panel">
                        <div class="panel-head">已有设置</div>
                        <div class="panel-body">
                            <ul class="list-group list-striped" id="meeting-type-current-setup">
                                
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="x8 padding-left">
                    <div class="panel">
                        <div class="panel-head"><a href="javascript:AddNewMeetingType()" id="add-meeting-type-link">新增</a>/修改设置</div>
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <th width="90%">会议类型名称</th>
                                    <th width="10%">操作</th>
                                </tr>
                                <tr id="add-modify-meeting-type">
                                    
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="tab-panel" id="tab-technology-type">
            <div class="line">
                <div class="x4 padding-right border-right">
                    <div class="panel">
                        <div class="panel-head">已有设置</div>
                        <div class="panel-body">
                            <ul class="list-group list-striped" id="technology-type-current-setup">
                                
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="x8 padding-left">
                    <div class="panel">
                        <div class="panel-head"><a href="javascript:AddNewTechnologyType()" id="add-technology-type-link">新增</a>/修改设置</div>
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <th width="80%">技术保障类型名称</th>
                                    <th width="10%">样式</th>
                                    <th width="10%">操作</th>
                                </tr>
                                <tr id="add-modify-technology-type">
                                    
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>

<script src="<?=base_url()?>js/meeting_setup.js"></script>