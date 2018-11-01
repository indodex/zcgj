<?php
namespace app\index\controller;
use app\common\controller\Base;
use think\Request;
use think\Session;
class Market extends Base
{


    /**
     * controller 关于我们
     */
    public function index()
    {
        $this -> assign('about',model('Market') -> aboutUs());
        return $this->fetch();
    }
}
