<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:72:"D:\phpStudy\WWW\zcgj\public/../application/index\view\publics\login.html";i:1537007050;s:59:"D:\phpStudy\WWW\zcgj\application\index\view\common\top.html";i:1539402256;s:62:"D:\phpStudy\WWW\zcgj\application\index\view\common\bottom.html";i:1537496062;}*/ ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title><?php echo config('WEB_SITE_NAME'); ?></title>
		<link rel="stylesheet" href="/static/ace/css/font_795017_oc8qigb32w.css"/>
		<link rel="stylesheet" href="/static/ace/css/bootstrap.css"/>
		<link rel="stylesheet" href="/static/ace/css/block_online.css"/>
		<link rel="stylesheet" href="/static/ace/css/common.css"/>
		<link rel="stylesheet" href="/static/layui/css/layui.css"/>
		<link rel="stylesheet" href="/static/ace/css/index.css">
		<link rel="stylesheet" href="/static/ace/css/spop.min.css">
		<link rel="stylesheet" href="/static/ace/css/swiper.min.css">
		
		<script type="text/javascript" src="/static/ace/js/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="/static/ace/js/block_online.js"></script>
		<script type="text/javascript" src="/static/layui/layui.all.js"></script>
		<script type="text/javascript" src="/static/ace/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="/static/ace/js/spop.min.js"></script>
		<script type="text/javascript" src="/static/ace/js/clipboard.min.js"></script>
		<script type="text/javascript" src="/static/ace/js/zcity.js"></script>
		<script type="text/javascript" src="/static/ace/js/spop.min.js"></script>
		<script type="text/javascript" src="/static/ace/js/jquery.peity.min.js"></script>
		<script type="text/javascript" src="/static/ace/js/swiper.min.js"></script>
	</head>
	<body>
		<div <?php if($lang == '1'): ?>class="index-head2"<?php else: ?>class="index-head"<?php endif; ?>>
		    <div class="index-top">
		        <ul class="index-top-menu clear">
		        	<li style="width: 11%"><img src="<?php echo config('WEB_LOGO'); ?>" alt=""></li>
		        	<?php if(is_array($sidebar) || $sidebar instanceof \think\Collection || $sidebar instanceof \think\Paginator): $i = 0; $__LIST__ = $sidebar;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
			            <li class="wind-8 menu_list" data='<?php echo $vo['title']; ?>'><a href="/<?php echo $vo['name']; ?>" <?php if($lang == '1'): ?>style='line-height:30px;'<?php endif; ?>><?php if($lang == '1'): ?><?php echo $vo['en_title']; else: ?><?php echo $vo['title']; endif; ?></a></li>
		            <?php endforeach; endif; else: echo "" ;endif; ?>
		            <li class="wind-8 change-language">
		                <div class="language">
		                	<?php if($lang == '1'): ?>
		        				ENGLISH
		        			<?php else: ?>
		        				简体中文
		        			<?php endif; ?>
		        			<i class="iconfont icon-xiala"></i>
		                </div>
		                <div class="choose-language" style="width: 100%">
		                    <p data-lang="en">ENGLISH</p>
		                    <p data-lang="cn">简体中文</p>
		                </div>
		            </li>
		            <?php if(empty($account) || (($account instanceof \think\Collection || $account instanceof \think\Paginator ) && $account->isEmpty())): ?>
			            <li style="width: 15%">
			                <a href="<?php echo url('publics/login'); ?>" style='font-size:12px;'><span class="button"><?php echo lang('login'); ?></span></a>
			                <a href="<?php echo url('publics/userReg'); ?>" style='font-size:12px;'><span class="button"><?php echo lang('register'); ?></span></a>
			            </li>
		            <?php else: ?>
		            	<li class="login-success" style="width: 13%">
			                <span>
			                    <img src="/static/ace/img/gerenzhongxin/touxiang.png" alt="" style='width:50px;'/>
			                </span>
			                <span class="login-id"><?php echo $_SESSION['think']['account']; ?><i class="iconfont icon-xiala1"></i></span>
			            </li>
			            <li class='logout'><?php echo lang('sign_out'); ?></li>
		            <?php endif; ?>
		        </ul>
		    </div>
		</div>
<!--头部 end-->
<script type="text/javascript">
// 切换中英文
$('.choose-language > p').click(function() {
	var lang = $(this).data('lang');
	edit_lang(lang);
});
function edit_lang(lang){
    $.post("<?php echo url('Publics/lang'); ?>", {'lang':lang}).success(function(data) {
    	location.href = self.location;
    });
}
// 退出登陆
$('.logout').click(function(){
	location.href = '<?php echo url("Publics/logout"); ?>';
});
</script>
<style type='text/css'>
#login{
	text-align: center;
	background: #c2b2ff;
	color: #fff;
	line-height: 42px;
	height: 42px;
}
</style>
	<div class="login">
        <div class="login-box">
            <div class="log"><?php echo lang('login'); ?></div>
            <ul>
            	<form class='login_box'>
	                <li><input id="user" type="text" name='account' placeholder="<?php echo lang('account'); ?>"><span class="iconfont icon-yonghu"></span></li>
	                <li><input id="password" type="password" name='password' placeholder="<?php echo lang('pwd'); ?>"><span class="iconfont icon-mima"></span></li>
	                <li><input id="login" type="submit" value="<?php echo lang('login'); ?>"></li>
            	</form>
            </ul>
            <div class="login-tool">
                <a href="<?php echo url('publics/forgetPwd'); ?>"><span style="float: left;"><?php echo lang('forget'); ?></span></a>
                <a href="<?php echo url('publics/userReg'); ?>"><span style="float: right"><?php echo lang('register'); ?></span></a>
            </div>
        </div>
    </div>
</div>

		<div class="index-warning full-bg">
		    <div class="center-1200">
		        <ul class="warn-text">
		            <li class="warn-title"><?php echo lang('hints'); ?>：</li>
		            <li><?php echo lang('hints_content'); ?></li>
		        </ul>
		    </div>
		</div>
		<div class="full-bg footer">
		    <div class="center-1200 clear">
		        <div class="logo-link clear">
		            <div class="footer-logo">
		                <img src="/static/ace/img/logo/logo.png" alt="">
		            </div>
		            <div class="footer-link">
		                <ul>
		                    <li><a href="<?php echo url('News/luckyTeam'); ?>">LUCKY KEY<?php echo lang('team'); ?></a></li>
		                    <li><a href="<?php echo url('Market/index'); ?>"><?php echo lang('about_us'); ?></a></li>
		                    <li><?php echo lang('contact_us'); ?></li>
		                </ul>
		            </div>
		        </div>
		        <div class="link-web">
		            <?php if($is_img['qq_img'] == '1'): ?><img class='signal' src='<?php echo config("QQ_QRCODE"); ?>'/><?php endif; ?>
		            <span class='signal_text' <?php if($is_img['qq_img'] == '2'): ?>style='display:block;margin-top:90px;'<?php endif; ?>>QQ:<br /><?php echo config('QQ'); ?></span>
		        </div>
		        <div class="link-web">
		            <?php if($is_img['qq_group_img'] == '1'): ?><img class='signal' src='<?php echo config("QQ_GROUP_QRCODE"); ?>'/><?php endif; ?>
		            <span class='signal_text' <?php if($is_img['qq_group_img'] == '2'): ?>style='display:block;margin-top:90px;'<?php endif; ?>>QQ群:<br /><?php echo config('QQ_GROUP'); ?></span>
		        </div>
		        <div class="link-web">
		            <?php if($is_img['wechat_img'] == '1'): ?><img class='signal' src='<?php echo config("WECHAT_QRCODE"); ?>'/><?php endif; ?>
		            <span class='signal_text'  <?php if($is_img['wechat_img'] == '2'): ?>style='display:block;margin-top:90px;'<?php endif; ?>>微信:<br /><?php echo config('WECHAT'); ?></span>
		        </div>
		        <div class="link-web">
		            <?php if($is_img['wechat_group_img'] == '1'): ?><img class='signal' src='<?php echo config("WECHAT_GROUP_QRCODE"); ?>'/><?php endif; ?>
		            <span class='signal_text'  <?php if($is_img['wechat_group_img'] == '2'): ?>style='display:block;margin-top:90px;'<?php endif; ?>>微信群:<br /><?php echo config('WECHAT_GROUP'); ?></span>
		        </div>
		    </div>
		</div>
		<script src="/static/ace/js/jquery.peity.min.js"></script>
		<script>
		    $('.language').click(function () {
		        $('.choose-language').show(200)
		    })
		    $('.choose-language').mouseleave(function () {
		        $('.choose-language').hide(200)
		        return
		    }).click(function () {
		        $('.choose-language').hide(200)
		        return
		    })
		    $(".line").peity("line")
		</script>
	</body>
</html>
<script type='text/javascript'>
// 成功提交框
function shipSuc (text) {
    spop({
        template: text,
        position  : 'top-center',
        style: 'success',
        autoclose: 2000
    });
}
// 错误提示框
function shipWar (text) {
    spop({
        template: text,
        position  : 'top-center',
        style: 'error',
        autoclose: 2000
    });
}

// 提交登陆
$('.login_box').submit(function(){
	$.post('<?php echo url("login"); ?>',$('.login_box').serialize()).success(function(ret){
		if(ret.status === 0){
			shipWar(<?php if($lang == '1'): ?>ret.en_info<?php else: ?>ret.info<?php endif; ?>);
		}else{
			shipSuc(<?php if($lang == '1'): ?>ret.en_info<?php else: ?>ret.info<?php endif; ?>);
			setTimeout(function(){
				location.href = ret.url;
			},1000);
		}
	});
	return false;
});
</script>
