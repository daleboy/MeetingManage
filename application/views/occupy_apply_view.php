<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th>场所</th>
            <th>会议主题</th>
            <th>使用时间</th>
            <th>申请占用人</th>
            <th>申请占用原因</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php if (sizeof($occupy_list) > 0): ?>
        <?php foreach ($occupy_list as $k => $z): ?>
        <tr>
            <td style="vertical-align:middle;"><?=$z['room_name']?></td>
            <td style="vertical-align:middle;"><?=$z['subject']?></td>
            <td style="vertical-align:middle;"><?=$z['now_use_time']?></td>
            <td style="vertical-align:middle;"><?=$z['apply_user']?></td>
            <td style="vertical-align:middle;"><?=mb_strlen($z['occupy_reason']) > 18 ? mb_substr($z['occupy_reason'], 0, 18) . '...' : $z['occupy_reason']?></td>
            <td>
                <a href="#" class="button" onclick="OccupyControl(<?=$z['id']?>)">处理</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td align="center" colspan="7">没有查到申请信息</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<div id="occupy-div" style="width: 500px; display: none; padding: 3px;">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <td align="right" width="28%">场所占用申请人：</td>
                <td align="left" width="72%" id="occupy-user" style="padding-left: 5px;"></td>
            </tr>
            <tr>
                <td align="right">占用原因：</td>
                <td align="left" id="occupy-reason" style="padding-left: 5px;"></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" align="left">
                    <i style="color: red;">说明：当您同意别人占用您的场所后，您需要手动删除您的排期申请才能释放对该场所的占用。</i>
                </td>
            </tr>
        </tfoot>
    </table>
    <p style="text-align: center; margin-top: 10px;">
        <input type="button" class="button margin-right" value="同意" onclick="AgreeOccupy()" />
        <input type="button" class="button" value="不同意" onclick="DisagreeOccupy()" />
    </p>
</div>

<script src="<?=base_url()?>js/occupy_apply.js"></script>