<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:81:"D:\phpStudy\WWW\zcgj\public/../application/admin\view\shop\add_feature_goods.html";i:1540957354;s:59:"D:\phpStudy\WWW\zcgj\application\admin\view\common\top.html";i:1522230592;s:62:"D:\phpStudy\WWW\zcgj\application\admin\view\common\header.html";i:1530500030;s:63:"D:\phpStudy\WWW\zcgj\application\admin\view\common\sidebar.html";i:1532051872;s:62:"D:\phpStudy\WWW\zcgj\application\admin\view\common\bottom.html";i:1490663526;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta charset="utf-8" />
<title><?php if(isset($info['name'])): ?><?php echo $info['name']; ?> - <?php endif; ?> <?php echo $pagename; ?> -  <?php echo config('WEB_SITE_NAME'); ?>管理后台</title>
<meta name="description" content=" <?php echo config('WEB_SITE_DESCRIPTION'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
<link rel="stylesheet" href="/static/ace/css/bootstrap.css" />
<link rel="stylesheet" href="/static/ace/css/font-awesome.css" />
<link rel="stylesheet" href="/static/ace/css/ace-fonts.css" />
<link rel="stylesheet" href="/static/ace/css/ace.css" class="ace-main-stylesheet" id="main-ace-style" />
<script src="/static/ace/js/ace-extra.js"></script>
<script type="text/javascript">window.jQuery || document.write("<script src='/static/ace/js/jquery.js'>"+"<"+"/script>");</script>
<script type="text/javascript">if('ontouchstart' in document.documentElement) document.write("<script src='/static/ace/js/jquery.mobile.custom.js'>"+"<"+"/script>");</script>
<style type="text/css">
input[type="date"], input[type="time"], input[type="datetime-local"], input[type="month"] {
  line-height: inherit;
}
.help-inline{
  line-height: 32px;
}
select{
	height: 34px;
}
.main-container .table tr td {
	vertical-align: middle;
}
.main-container .table tr td a{
	margin-right:10px;
}
#preview{
  height: 120px;
  width: auto;
}
</style>
<style type='text/css'>
.display_box {height:32px;line-height:32px;}
</style>
</head><body class="no-skin">
<div id="navbar" class="navbar navbar-default">
  <div class="navbar-container" id="navbar-container">
  <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar"> <span class="sr-only">Toggle sidebar</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
    <div class="navbar-header pull-left"> <a href="<?php echo url('Index/index'); ?>" class="navbar-brand"> <small><?php echo \think\Config::get('WEB_SITE_NAME'); ?>网站管理后台 </small> </a> </div>
    <div class="navbar-buttons navbar-header pull-right" role="navigation">
      <ul class="nav ace-nav">
        <li class="light-blue"> <a data-toggle="dropdown" href="#" class="dropdown-toggle"> <img class="nav-user-photo" src="/static/ace/avatars/user.png" />
        <span class="user-info"><small>欢迎你</small><?php echo $_SESSION['think']['username']; ?></span> <i class="ace-icon fa fa-caret-down"></i> </a>
          <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
            <li> <a href="<?php echo url('Index/repwd'); ?>"> <i class="ace-icon fa fa-cog"></i> 修改密码 </a></li>
            <li> <a href="<?php echo url('Publics/logout'); ?>"> <i class="ace-icon fa fa-power-off"></i> 退出后台 </a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="main-container" id="main-container"> 
  <!-- #section:basics/sidebar --> 
  <div id="sidebar" class="sidebar ">
  <div class="sidebar-shortcuts" id="sidebar-shortcuts">
    <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
      <button class="btn btn-success">
      <i class="ace-icon fa fa-group"></i>
      </button>
      <button class="btn btn-info">
      <i class="ace-icon fa fa-list"></i>
      </button>
      <button class="btn btn-warning">
      <i class="ace-icon fa fa-arrow-circle-up"></i>
      </button>
      <button class="btn btn-danger">
      <i class="ace-icon fa fa-cog"></i>
      </button>
    </div>
    <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
      <span class="btn btn-success"></span>
      <span class="btn btn-info"></span>
      <span class="btn btn-warning"></span>
      <span class="btn btn-danger"></span>
    </div>
  </div>
  <ul class="nav nav-list">
    <?php if(is_array($sidebar) || $sidebar instanceof \think\Collection || $sidebar instanceof \think\Paginator): $i = 0; $__LIST__ = $sidebar;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
      <li <?php if($vo['name'] == $rule2): ?>class="open active"<?php endif; ?>><a href="<?php echo url('/'.$vo['name']); ?>" <?php if($vo['count'] != 0): ?>class="dropdown-toggle"<?php endif; ?>><i class="menu-icon fa fa-<?php echo $vo['icon']; ?>"></i><span class="menu-text"> <?php echo $vo['title']; ?> </span><b class="arrow <?php if($vo['count'] != 0): ?>fa fa-angle-down<?php endif; ?>"></b></a>
        <b class="arrow"></b>
        <ul class="submenu">
          <?php if(is_array($vo['child']) || $vo['child'] instanceof \think\Collection || $vo['child'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?>
          <li <?php if($sub['name'] == $rule): ?>class="active"<?php endif; ?>><a href="<?php echo url('/'.$sub['name']); ?>"><i class="menu-icon fa fa-caret-right"></i> <?php echo $sub['title']; ?> </a><b class="arrow"></b></li>
          <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
      </li>
    <?php endforeach; endif; else: echo "" ;endif; ?>
  </ul>
  <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
    <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
  </div>
</div> 
  <!-- /section:basics/sidebar -->
  <div class="main-content">
    <div class="main-content-inner"> 
      <!-- #section:basics/content.breadcrumbs -->
      <div class="breadcrumbs" id="breadcrumbs">
        <ul class="breadcrumb">
          <li> <i class="ace-icon fa fa-home home-icon"></i> <a href="<?php echo url('Index/index'); ?>"><?php echo config('WEB_SITE_NAME'); ?></a> </li>
          <li> <a href="<?php echo url('feature'); ?>">特色专区</a> </li>
          <li class="active"><?php echo $pagename; ?></li>
        </ul>
        <!-- /.breadcrumb --> 
      </div>
      <!-- /section:basics/content.breadcrumbs -->
      <div class="page-content">
        <div class="page-header">
          <h1> <?php echo $pagename; ?> <a class="btn btn-sm btn-success" style="float:right; margin-right:10px;" href="<?php echo url('feature'); ?>"><img src='/static/ace/images/back.png'/>&nbsp;返&nbsp;回&nbsp;</a></h1>
        </div>
          <!-- 添加用户 -->
          <div class="row">
            <div class="col-xs-12">
              <form class="form-horizontal form-post" role="form" method="post">
                
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 商品分类 </label>
                  <div class="col-sm-9">
                    <select name="gid" class="col-xs-10 col-sm-5">
                      <option value="0">请选择分类</option>
                      <?php if(is_array($class) || $class instanceof \think\Collection || $class instanceof \think\Paginator): $i = 0; $__LIST__ = $class;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo $vo['id']; ?>"><?php echo $vo['name']; ?></option>
                      <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 商品名 </label>
                  <div class="col-sm-9">
                    <input name="name" type="text" class="col-xs-10 col-sm-5" placeholder="请填写商品名" value="<?php echo $goods['name']; ?>" />
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 优惠价格 </label>
                  <div class="col-sm-9">
                    <input name="price" type="text" class="col-xs-10 col-sm-5" placeholder="请填写优惠价格" value="<?php echo $goods['price']; ?>" />
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 原价 </label>
                  <div class="col-sm-9">
                    <input name="original_price" type="text" class="col-xs-10 col-sm-5" placeholder="请填写原价" value="<?php echo $goods['original_price']; ?>" />
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 库存 </label>
                  <div class="col-sm-9">
                    <input name="number" type="text" class="col-xs-10 col-sm-5" placeholder="请填写库存" value="<?php echo $goods['number']; ?>" />
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 品牌 </label>
                  <div class="col-sm-9">
                    <input name="brand" type="text" class="col-xs-10 col-sm-5" placeholder="请填写品牌" value="<?php echo $goods['brand']; ?>" />
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 规格型号 </label>
                  <div class="col-sm-9">
                    <input name="type" type="text" class="col-xs-10 col-sm-5" placeholder="请填写规格型号" value="<?php echo $goods['type']; ?>" />
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 联系方式 </label>
                  <div class="col-sm-9">
                    <input name="tel" type="text" class="col-xs-10 col-sm-5" placeholder="请填写联系方式" value="<?php echo $goods['tel']; ?>" />
                  </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right"> 详情图 </label>
                    <div class="col-sm-3 col-lg-3" style="padding-right: 0px;">
                        <input name="detail_pic" type="text" class="form-control" readonly placeholder="这里详情图上传地址" value="<?php echo $goods['detail_pic']; ?>" /><br />
                        <img name ="detail_pic" width="500px" height="139px"  hidden="" id="detail_pic_view" <?php if(isset($goods['detail_pic'])): ?>src='<?php echo $goods['detail_pic']; ?>' style='display:inline;'<?php endif; ?> />
                    </div>
                    <div class="col-sm-2 col-lg-2" style="padding-left: 0px;">
                        <a href="javascript:void(0);" class="btn btn-sm btn-success" id="detail_pic_upload" data-type="headimg">上传详情图</a>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right"> 专享服务图 </label>
                    <div class="col-sm-3 col-lg-3" style="padding-right: 0px;">
                        <input name="service_pic" type="text" class="form-control" readonly placeholder="这里专享服务图上传地址" value="<?php echo $goods['service_pic']; ?>" /><br />
                        <img name ="service_pic" width="500px" height="139px"  hidden="" id="service_pic_view" <?php if(isset($goods['service_pic'])): ?>src='<?php echo $goods['service_pic']; ?>' style='display:inline;'<?php endif; ?> />
                    </div>
                    <div class="col-sm-2 col-lg-2" style="padding-left: 0px;">
                        <a href="javascript:void(0);" class="btn btn-sm btn-success" id="service_pic_upload" data-type="headimg">上传专享服务图</a>
                    </div>
                </div>
                
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 备注 </label>
                  <div class="col-sm-9">
                    <input name="remarks" type="text" class="col-xs-10 col-sm-5" placeholder="请填写备注" value="<?php echo $goods['remarks']; ?>" />
                  </div>
                </div>
                
                <div class="space-4"></div>
                <div class="alert alert-danger" style="display:none;"></div>
                <div class="clearfix form-actions">
                  <div class="col-md-offset-3 col-md-9">
                  	<input name="id" class="hidden" type="text" value="<?php echo $goods['id']; ?>">
                    <button class="btn btn-info" type="submit" id="btn"> <i class="ace-icon fa fa-check bigger-110"></i> 保存 </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
      </div>
    </div>
  </div>
  <!-- /.main-content -->
  <div class="footer">
    <div class="footer-inner">
      <div class="footer-content"> <span class="bigger-120"> <span class="blue bolder"><?php echo config('WEB_SITE_NAME'); ?> </span><?php echo WEB_VERSION; ?>版 </span></div>
    </div>
  </div>
  <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse"> <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i> </a> </div>
<!-- /.main-container --> 
<!-- basic scripts --> 
<script type="text/javascript">if($(window).width()<1024)  $("#sidebar").addClass('menu-min');</script>
<script src="/static/ace/js/bootstrap.js"></script>
<script src="/static/ace/js/ace/ace.js"></script> 
<script src="/static/ace/js/ace/ace.sidebar.js"></script> 
<link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
<script src="/static/layui/layui.js"></script>
<script type="text/javascript">
    $(document).ready(function (){
        var ue = UE.getEditor('container');
    });
</script>
<!-- 上传单图片 -->
<script>
// 详情图
layui.use('upload', function(){
  var upload = layui.upload;
  //执行实例
  var uploadInst = upload.render({
    elem: '#detail_pic_upload' //绑定元素
    ,url: "<?php echo url('Shop/upload_pic'); ?>" //上传接口
    ,data: {type: 'shop'}
    ,done: function(res){
      // console.log(res)
      //上传完毕回调
      if(res.status == 0){
        layer.msg(res.info, {icon: res.status,time: 1500});
      }else{
        // 显示img里边的图片
        $('#detail_pic_view').show();
        //返回路径
        $("input[name=detail_pic]").val(res.msg);
        //给IMG返回路径
        $("img[name=detail_pic]").attr("src",res.msg)
      }
    }
  });
});

// 专享服务图
layui.use('upload', function(){
  var upload = layui.upload;
  //执行实例
  var uploadInst = upload.render({
    elem: '#service_pic_view' //绑定元素
    ,url: "<?php echo url('Shop/upload_pic'); ?>" //上传接口
    ,data: {type: 'shop'}
    ,done: function(res){
      // console.log(res)
      //上传完毕回调
      if(res.status == 0){
        layer.msg(res.info, {icon: res.status,time: 1500});
      }else{
        // 显示img里边的图片
        $('#service_pic_upload').show();
        //返回路径
        $("input[name=service_pic]").val(res.msg);
        //给IMG返回路径
        $("img[name=service_pic]").attr("src",res.msg)
      }
    }
  });
});
</script>
<script type="text/javascript">
$('a[href="/Admin/Shop/feature"]').parents().filter('li').addClass('open active');
//提交表单
$(".form-post").find('button:submit').click(function() {
	
  $.post("<?php echo url('add_feature_goods'); ?>", $(".form-post").serialize()).success(function(data) {
    $('#btn').text('正在保存').attr('disabled', "true");
    if (data.status === 0) {
      $(".form-post .alert").text(data.info).show();
      setTimeout(function() {
        $('#btn').text('保存').removeAttr('disabled');
        $(".form-post .alert").empty().hide();
      },
      1000);
    }else{
      setTimeout(function() {
        location.href = data.url;
      },
      1000);
    }
  });
  return false;
});
</script> 
</body>
</html>
