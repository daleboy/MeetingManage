<style>
.sign_in { cursor: pointer; }
</style>
<div class="layout margin-top">
    <div class="line">
        <div class="x8 x2-move text-center">
            <h1><?=$meeting_name?></h1>
            <small id="show_time">时间</small>
            <ul class="list-unstyle list-inline">
                <li>应到：<?=$sign_in_count?>人</li>
                <li>已到：<span id="already_sign_in">0</span>人</li>
            </ul>
            <hr />
            <input type="hidden" id="s_id" value="<?=$s_id?>" />
        </div>
        <div class="x2 text-right">
            <a href="<?=base_url()?>index.php/MyMeetingApply/LedShowSingIn/<?=$s_id?>" class="button" target="_blank">大屏展示</a>
        </div>
    </div>
    <div class="line">
        <div class="x12">
            <?php foreach ($sign_list as $list): ?>
            <div class="media">
                <div class="media media-x clearfix">
                    <a class="float-left" href="#">
                        <img src="<?=base_url()?>images/tongda.ico" class="radius">
                    </a>
                    <div class="media-body">
                        <strong><?=$list['court_name']?></strong>
                        <?php foreach ($list['dept'] as $d_list): ?>
                        <div class="panel">
                            <div class="panel-head"><?=$d_list['dept_name']?></div>
                            <div class="panel-body">
                                <ul class="list-unstyle list-inline">
                                <?php foreach ($d_list['person'] as $person): ?>
                                    <li><?=$person['user_name']?><span class="badge margin-left <?=$person['sign_in'] == 1 ? ' bg-red' : ' sign_in'?>" rel="<?=$person['user_name']?>" id="<?=$person['user_id']?>"><?=$person['sign_in'] == 1 ? '已到' : '未到'?></span></li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <hr />
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div id="scan_code_div" class="border border-small border-dashed radius-big" style="width: 200px; height: 200px; position: fixed; top: 200px; right: -180px;">
    <table width="100%" height="100%">
        <tr>
            <td id="show_code_div" width="20px" class="bg-yellow-light">
                扫码
            </td>
            <td width="180px" class="border-big border-left border-sub padding bg-blue-light text-center">
                <strong>扫描二维码</strong>
                <p><input type="text" class="input" id="code" /></p>
                <small>请保持输入框为焦点状态，否则无法获取扫描结果</small>
            </td>
        </tr>
    </table>
</div>
<script src="<?=base_url()?>js/sign_in.js" type="text/javascript"></script>