<div id="led_main" class="layout margin-top">
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
    </div>
    <div id="scroll_main" class="line">
        <div class="x6 border-right">
            <div class="line"><div class="x6 x2-move text-center fixed"><h1>未到</h1></div></div>
            <?php foreach ($not_sign_in as $list): ?>
            <div class="media">
                <div class="media media-x clearfix padding">
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
                                    <li><?=$person['user_name']?></li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="already_sign_in_div" class="x6">
            <div class="line"><div class="x6 x2-move text-center fixed"><h1>已到</h1></div></div>
            <?php foreach ($sign_in as $list): ?>
            <div class="media">
                <div class="media media-x clearfix padding">
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
                                    <li><?=$person['user_name']?></li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<script src="<?=base_url()?>js/led_sign_in.js" type="text/javascript"></script>