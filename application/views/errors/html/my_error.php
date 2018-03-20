<style>
* { margin: 0px auto; padding: 0px; text-align: center; }
body { background-color: #D4D9ED; }
.cont_principal { position: absolute; width: 100%; height: 100%; overflow: hidden; }
.cont_error { position: absolute; width: 100%; height: 300px; top: 50%; margin-top: -150px; }
.cont_error>h1 { font-family: 'Lato', sans-serif; font-weight: 400; font-size: 100px; color: #fff; position: relative; left: -100%; transition: all 0.5s; }
.cont_error>p { font-family: 'Lato', sans-serif; font-weight: 300; font-size: 24px; letter-spacing: 5px; color: #9294AE; position: relative; left: 100%; transition: all 0.5s; transition-delay: 0.5s; -webkit-transition: all 0.5s; -webkit-transition-delay: 0.5s; }
.cont_aura_1 { position: absolute; width: 300px; height: 120%; top: 25px; right: -340px; background-color: #8A65DF; box-shadow: 0px 0px 60px 20px rgba(137, 100, 222, 0.5); -webkit-transition: all 0.5s; transition: all 0.5s; }
.cont_aura_2 { position: absolute; width: 100%; height: 300px; right: -10%; bottom: -301px; background-color: #8B65E4; box-shadow: 0px 0px 60px 10px rgba(131, 95, 214, 0.5), 0px 0px 20px 0px rgba(0, 0, 0, 0.1); z-index: 5; transition: all 0.5s; -webkit-transition: all 0.5s; }
.cont_error_active>.cont_error>h1 { left: 0%; }
.cont_error_active>.cont_error>p { left: 0%; }
.cont_error_active>.cont_aura_2 { animation-name: animation_error_2; animation-duration: 4s; animation-timing-function: linear; animation-iteration-count: infinite; animation-direction: alternate; transform: rotate(-20deg); }
.cont_error_active>.cont_aura_1 { transform: rotate(20deg); right: -170px; animation-name: animation_error_1; animation-duration: 4s; animation-timing-function: linear; animation-iteration-count: infinite; animation-direction: alternate; }
@-webkit-keyframes animation_error_1 {
    from { -webkit-transform: rotate(20deg); transform: rotate(20deg); }
    to { -webkit-transform: rotate(25deg); transform: rotate(25deg); }
}
@-o-keyframes animation_error_1 {
    from { -webkit-transform: rotate(20deg); transform: rotate(20deg); }
    to { -webkit-transform: rotate(25deg); transform: rotate(25deg); }
}
@-moz-keyframes animation_error_1 {
    from { -webkit-transform: rotate(20deg); transform: rotate(20deg); }
    to { -webkit-transform: rotate(25deg); transform: rotate(25deg); }
}
@keyframes animation_error_1 {
    from { -webkit-transform: rotate(20deg); transform: rotate(20deg); }
    to { -webkit-transform: rotate(25deg); transform: rotate(25deg); }
}
@-webkit-keyframes animation_error_2 {
    from { -webkit-transform: rotate(-15deg); transform: rotate(-15deg); }
    to { -webkit-transform: rotate(-20deg); transform: rotate(-20deg); }
}
@-o-keyframes animation_error_2 {
    from { -webkit-transform: rotate(-15deg); transform: rotate(-15deg); }
    to { -webkit-transform: rotate(-20deg); transform: rotate(-20deg); }
}
@-moz-keyframes animation_error_2 {
    from { -webkit-transform: rotate(-15deg); transform: rotate(-15deg); }
    to { -webkit-transform: rotate(-20deg); transform: rotate(-20deg); }
}
@keyframes animation_error_2 {
    from { -webkit-transform: rotate(-15deg); transform: rotate(-15deg); }
    to { -webkit-transform: rotate(-20deg); transform: rotate(-20deg); }
}
</style>
<span style="float: left; color: #D4D9ED;">文件：<?=$filepath?>，第<?=$line?>行，<?=$message?>。</span>
<div class="cont_principal">
    <div class="cont_error">
        <h1>系统提示</h1>
        <p>我们的程序貌似出了点小问题，若重试后还看到该页面，请联系客服：<br />0771-5788292<br />0771-5788527</p>
        <p><a href='<?=base_url()?>' class="back">返&nbsp;回&nbsp;首&nbsp;页</a></p>
    </div>
    <div class="cont_aura_1"></div>
    <div class="cont_aura_2"></div>
</div>
<script>
    window.onload = function () {
        document.querySelector('.cont_principal').className = "cont_principal cont_error_active";
    }
</script>