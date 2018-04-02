<link href="<?=base_url()?>css/pintuer.css" rel="stylesheet" type="text/css">
<script src="<?=base_url()?>js/jquery-1.12.0.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>js/printThis.js" type="text/javascript"></script>
<div id="safeguards-container" style="margin: 0 auto; width: 530px;">
    <table class="table">
        <caption class="margin-large-bottom"><h1>会议保障通知单</h1></caption>
        <tbody>
            <tr>
                <td>会议名称：</td>
                <td colspan="5" align="left">
                    <?=$meetingName?>
                </td>
            </tr>
            <tr>
                <td>会议类型：</td>
                <td colspan="5" align="left">
                    <?=$meetingType?>
                </td>
            </tr>
            <tr>
                <td>会议时间：</td>
                <td colspan="5" align="left">
                    <?=$meetingTime?>
                </td>
            </tr>
            <tr>
                <td>会议地点：</td>
                <td colspan="5">
                    <?=$meetingAddress?>
                </td>
            </tr>
            <tr>
                <td>承办部门：</td>
                <td>
                    <?=$meetingCbbm?>
                </td>
                <td>联系人：</td>
                <td>
                    <?=$meetingLxr?>
                </td>
                <td>联系电话：</td>
                <td>
                    <?=$meetingLxdh?>
                </td>
            </tr>
            <?php
            $technologyTypeNum = sizeof($technologyType);
            $trNum = ceil($technologyTypeNum / 2); //需要多少行
            for ($i = 0; $i < $trNum; $i++): ?>
            <tr>
                <td colspan="3">
                    <?=isset($technologyType[$i * 2]) ? $technologyType[$i * 2] : ''?>
                </td>
                <td colspan="3">
                    <?=isset($technologyType[$i * 2 + 1]) ? $technologyType[$i * 2 + 1] : ''?>
                </td>
            </tr>
            <?php endfor;?>
            <tr>
                <td colspan="6">
                    <?=$whetherSpeak1?>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <?=$whetherSpeak2?>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <?=$whetherSpeak3?>
                </td>
            </tr>
            <tr>
                <td>发言单位：</td>
                <td colspan="5">
                    <?=$speakDept?>
                </td>
            </tr>
            <tr>
                <td colspan="2">参加会议的范围：</td>
                <td colspan="4">
                    <?php foreach ($meetingRange as $mr): ?>
                    <label style="margin-right: 20px;">
                        <?=$mr?>
                    </label>
                    <?php endforeach;?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="text-center margin-large-top">
    <button class="button" id="print-safeguards">打印</button>
</div>
<script>
    $(function () {
        $('#print-safeguards').click(function () {
            $('#safeguards-container').printThis();
        });
    });
</script>