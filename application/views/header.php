<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="renderer" content="webkit">
  <title>会议管理</title>
  <link href="<?=base_url()?>css/pintuer.css" rel="stylesheet" type="text/css">
  <link href="<?=base_url()?>css/header.css" rel="stylesheet" type="text/css">
  <script src="<?=base_url()?>js/jquery-1.12.0.min.js" type="text/javascript"></script>
  <script src="<?=base_url()?>js/pintuer.js" type="text/javascript"></script>
  <script src="<?=base_url()?>js/respond.js" type="text/javascript"></script>
  <script src="<?=base_url()?>js/layer/layer.js" type="text/javascript"></script>
  <script src="<?=base_url()?>js/laypage/laypage.js" type="text/javascript"></script>
  <script src="<?=base_url()?>js/my-ajax.js" type="text/javascript"></script>
  <!--[if lt IE 9]>
    <script src="<?=base_url()?>js/html5shiv.js"></script>
<script src="<?=base_url()?>js/respond.js"></script>
<![endif]-->
</head>

<body class="body_bg">
  <div class="layout head_bg">
    <div class="line">
      <div class="x6 logo_bg">
        <div class="logo_img">
          <img src="<?=base_url()?>images/logo.png" class="ring-hover" />
        </div>
      </div>
      <div class="x6">
        <div class="line">
          <div class="x12 top_suv">
            <table width="300" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><a href="<?=base_url()?>会务管理系统操作手册.doc" target="_blank"><img src="<?=base_url()?>images/header_sc.png"/></a></td>
                <td><a href="<?=base_url()?>index.php/MeetingSetup" target="_blank"><img src="<?=base_url()?>images/header_sz.png"/></a></td>
                <td><a href="javascript:window.location='/MeetingManage/index.php?logout='"><img src="<?=base_url()?>images/header_tc.png"/></a></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="line">
          <div class="x12 top_name">
            <div class="left">
              <ul>
                <li>法院：
                  <?=$court_name?>
                </li>
                <li>部门：
                  <?=$department_name?>
                </li>
                <li>姓名：
                  <?=$user_name?>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>