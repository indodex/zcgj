<?php
/**
 * 全局基类
 */
namespace app\common\controller;

use think\Controller;
use think\Request;
class Base extends Controller
{

    public function _initialize()
    {
        // header("Content-Type:text/html; charset=utf-8");

        /* 读取数据库中的配置 */
        $config = model('Common/Config')->getConfig();
        foreach ($config as $k => $v) {
        	config($k,$v);
        }
        $request= Request::instance();
        $rule2 = $request->module() . '/' . $request->controller();
        $rule = $request->module() . '/' . $request->controller() . '/' . $request->action();
        $id = session('uid');
        
        // 判断是否登陆
        $this -> assign('account',isset($_SESSION['think']['account'])?$_SESSION['think']['account']:'');
		
        $this->assign('info',db('user')->where('id',$id)->find());
        $this->assign('rule2',$rule2);
        $this->assign('rule',$rule);
        // 调用导航
        $this->assign('sidebar',model('Common/Menu')->ruleMap());
        
        // 判断是否设置QQ&wechat qrcode
        $this -> assign('is_img',model('Common/Menu') -> isImg());
    }

}