<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:71:"D:\phpStudy\WWW\zcgj\public/../application/admin\view\method\index.html";i:1540018327;s:59:"D:\phpStudy\WWW\zcgj\application\admin\view\common\top.html";i:1522230592;s:62:"D:\phpStudy\WWW\zcgj\application\admin\view\common\header.html";i:1530500030;s:63:"D:\phpStudy\WWW\zcgj\application\admin\view\common\sidebar.html";i:1532051872;s:62:"D:\phpStudy\WWW\zcgj\application\admin\view\common\bottom.html";i:1490663526;}*/ ?>
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
<style type="text/css">
.search {text-indent:0.5em;}
.main-container .table tr td {
  vertical-align: middle;
}
.main-container .table tr td a{
  margin-right:10px;
}

.img_url {width:50px;height:40px;cursor:pointer;}

/* 充值/提现开始 */
.method_type_red {width:50px;height:26px;line-height:26px;text-align:center;color:white;border-radius:10px;background-color:red;box-shadow:#006666 1px 1px 2px;}
.method_type_green {width:50px;height:26px;line-height:26px;text-align:center;color:white;border-radius:10px;background-color:green;box-shadow:#18A665 1px 1px 2px;}
/* 充值/提现结束 */

/* 充值/提现种类开始 */
.method_status_fictitious {width:60px;height:26px;line-height:26px;text-align:center;color:white;border-radius:10px;background-color:#BCD883;box-shadow:#BCD883 1px 1px 2px;}
.method_status_rmb {width:60px;height:26px;line-height:26px;text-align:center;color:white;border-radius:10px;background-color:#DD5E7C;box-shadow:#DD5E7C 1px 1px 2px;}
/* 充值/提现种类结束 */

/* 审核状态开始 */
.review_pass {width:100px;height:26px;line-height:26px;text-align:center;color:white;border-radius:10px;background-color:#87B87F;box-shadow:#87B87F 1px 1px 2px;}
.review_fail {width:100px;height:26px;line-height:26px;text-align:center;color:white;border-radius:10px;background-color:#DD0C0C;box-shadow:#DD0C0C 1px 1px 2px;}
.review_on {width:100px;height:26px;line-height:26px;text-align:center;color:white;border-radius:10px;background-color:#2E98F5;box-shadow:#2E98F5 1px 1px 2px;}
/* 审核状态结束 */
</style>
</head>
<body class="no-skin">
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
<div class="main-container" id="main-container"> <div id="sidebar" class="sidebar ">
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
  <div class="main-content">
    <div class="main-content-inner">
      <div class="breadcrumbs" id="breadcrumbs">
        <ul class="breadcrumb">
          <li> <i class="ace-icon fa fa-home home-icon"></i> <a href="<?php echo url('Index/index'); ?>"><?php echo config('WEB_SITE_NAME'); ?></a> </li>
          <li> <a href="<?php echo url('index'); ?>">充值管理</a> </li>
          <li class="active"><?php echo $pagename; ?></li>
        </ul>
      </div>
      <div class="page-content">
        <div class="page-header">
          <h1> <?php echo $pagename; ?> <small> <i class="ace-icon fa fa-angle-double-right"></i> 查询出<?php echo $list['count']; ?>条数据 </small> </h1>
        </div>
        <!-- /.page-header -->
        <div class="row">
          <div class="col-xs-12"> 
            <!-- PAGE CONTENT BEGINS -->
            <div class="row">
              <div class="col-xs-12" style="margin-bottom:10px;">
                <form action="<?php echo url('index'); ?>" method="get" class="form-inline" role="form">
                  <div class="form-group">
                    <label>充值|提现记录查询</label>
                    <input name="keywords" type="text" class="form-control search" placeholder="用户账号" />
                  </div>&nbsp;&nbsp;
                  
                  <div class="form-group"><label>财务类型</label>
                    <select name="type" class="form-control" <!--onchange='look_state(this)'-->>
                    	<option value="">全部</option>
                      <?php if(is_array($type) || $type instanceof \think\Collection || $type instanceof \think\Paginator): $i = 0; $__LIST__ = $type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo $vo['value']; ?>" <?php if($get_type == $vo['value']): ?>selected='selected'<?php endif; ?>><?php echo $vo['key']; ?></option>
                      <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                  </div>&nbsp;&nbsp;
                  
                  <div class="form-group"><label>币种</label>
                    <select name="status" class="form-control" <!--onchange='look_state(this)'-->>
                    	<option value="">全部</option>
                      <?php if(is_array($status) || $status instanceof \think\Collection || $status instanceof \think\Paginator): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo $vo['value']; ?>" <?php if($get_status == $vo['value']): ?>selected='selected'<?php endif; ?>><?php echo $vo['key']; ?></option>
                      <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                  </div>&nbsp;&nbsp;
                  
                  <div class="form-group"><label>审核状态</label>
                    <select name="review" class="form-control" <!--onchange='look_state(this)'-->>
                    	<option value="">全部</option>
                      <?php if(is_array($review) || $review instanceof \think\Collection || $review instanceof \think\Paginator): $i = 0; $__LIST__ = $review;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo $vo['value']; ?>" <?php if($get_review == $vo['value']): ?>selected='selected'<?php endif; ?>><?php echo $vo['key']; ?></option>
                      <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                  </div>&nbsp;&nbsp;
                  
                  <button type="submit" class="btn btn-sm btn-primary">查询</button>
                  <button type="reset" class="btn btn-sm btn-danger hidden-xs" style="float:right;margin-right:10px;">清空查询条件</button>
                </form>
              </div>
              <div class="col-xs-12">
                <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <th class="center">记录ID</th>
                      <th>用户账号</th>
                      <th>充值|提现金额</th>
                      <th>类型</th>
                      <th>充值|提现种类</th>
                      <th>创建时间</th>
                      <th>手续费</th>
                      <th class='center'>转账凭证</th>
                      <th>审核状态</th>
                      <th>操作</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(is_array($list['list']) || $list['list'] instanceof \think\Collection || $list['list'] instanceof \think\Paginator): $k = 0; $__LIST__ = $list['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?>
                    <tr>
                        <td class="center"><?php echo $vo['id']; ?></td>
                        <td><?php echo $vo['user_account']; ?></td>
                        <td><?php echo $vo['money']; ?></td>
                        <td><div class='<?php echo $vo['method_type_btn']; ?>'><?php echo $vo['method_type_text']; ?></div></td>
                        <td><div class='<?php echo $vo['method_status_btn']; ?>'><?php echo $vo['method_status_text']; ?></div></td>
                        <td><?php echo $vo['create_time']; ?></td>
                        <td><?php echo $vo['service_charge']; ?></td>
                        <td class='center'>
                        	<?php if(empty($vo['url']) || (($vo['url'] instanceof \think\Collection || $vo['url'] instanceof \think\Paginator ) && $vo['url']->isEmpty())): ?>
                        		--
                        	<?php else: ?>
                        		<img class='img_url imgZoom' src='<?php echo $vo['url']; ?>' />
                        	<?php endif; ?>
                        </td>
                        <td><div class='<?php echo $vo['review_btn']; ?>'><?php echo $vo['review_text']; ?></div></td>
	                      <td>
	                      	<?php if($vo['recharge_status'] === 1): ?>
	                      		<a class="btn btn-sm btn-success" href="javascript:void(0)" onclick="review(this,<?php echo $vo['id']; ?>,2)">审核</a>
	                      		<a class="btn btn-sm btn-danger" href="javascript:void(0)" onclick="review(this,<?php echo $vo['id']; ?>,3)">失败</a>
	                      	<?php else: ?>
	                      		--
	                      	<?php endif; ?>
	                      </td>
                    </tr>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                  </tbody>
                </table>
                <div style="width:100%;margin: 0 auto; text-align:center;">
                  <ul class="pagination" >
                    <?php echo $list['page']; ?>
                  </ul>
                </div>
              </div>
              <!-- /.span --> 
            </div>
            <!-- /.row --> 
            <!-- PAGE CONTENT ENDS --> 
          </div>
          <!-- /.col --> 
        </div>
        <!-- /.row --> 
      </div>
      <!-- /.page-content --> 
    </div>
  </div>
  <!-- /.main-content -->
  <div class="footer">
    <div class="footer-inner"> 
      <!-- #section:basics/footer -->
      <div class="footer-content"> <span class="bigger-120"> <span class="blue bolder"><?php echo config('WEB_SITE_NAME'); ?> </span><?php echo WEB_VERSION; ?>版 </span></div>
      <!-- /section:basics/footer --> 
    </div>
  </div>
  <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse"><i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i></a> </div>
<!-- /.main-container --> 
<!-- basic scripts --> 
<script type="text/javascript">if($(window).width()<1024)  $("#sidebar").addClass('menu-min');</script>
<script src="/static/ace/js/bootstrap.js"></script>
<script src="/static/ace/js/ace/ace.js"></script> 
<script src="/static/ace/js/ace/ace.sidebar.js"></script> 
<script src="/static/ace/js/layer/layer.js"></script>

<script type="text/javascript" src="/static/ace/js/imgZoom/jquery.mousewheel.js"></script>
<script type="text/javascript" src="/static/ace/js/imgZoom/jquery.imgZoom.js"></script>
<script type="text/javascript" src="/static/ace/js/imgZoom/jquery.drag.js"></script>
<script type="text/javascript">
	// 点击查看大图
	$(".imgZoom").imgZoom();
</script>

<script type="text/javascript">
  $('a[href="/Admin/Method/index"]').parents().filter('li').addClass('open active');
  <?php if(input('get.keywords')): ?>
    $('input[name="keywords"]').val('<?php echo $_GET["keywords"]; ?>');
  <?php endif; if(is_numeric(input('get.state'))): ?>
    $('select[name="state"]').val(<?php echo $_GET['state']; ?>);
  <?php endif; ?>
</script>

<script type="text/javascript">
jQuery(function($) {
  //清除查询条件
  $(document).on('click', 'button:reset',function() {
    location.href = '<?php echo url("index"); ?>';
  }); 
});

// 审核
function review(obj,id,review){
	layer.confirm('确定审核吗？',{
		btn: ['确定','关闭']
	},function(){
		$.post("<?php echo url('review'); ?>", {id:id,review:review}).success(function(data) {
			if(data.code == 0){
				layer.msg(data.msg, {icon: data.code,time: 1500});
			}else{
				layer.msg(data.msg, {icon: data.code,time: 1500},function(){
					location.href = self.location.href;
				});
			}
		})
	});
}
</script>
</body>
</html>
