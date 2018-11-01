<?php
namespace app\index\controller;

use app\common\controller\Base;
use think\Request;
use think\Session;

class News extends Base
{

    /**
     * controller 公告列表
     */
    public function index($p = 1)
    {
    	$this -> assign('news',model('News') -> newsList($p));
        return $this -> fetch();
    }

    /**
     * controller 公告详情页
     */
    public function detail()
    {
      $id = input('id');
      $this -> assign('newsinfo',model('News') -> newsInfo($id));
      return $this -> fetch();
    }
    
    /**
     * LUCKY KEY 团队
     */
    public function luckyteam(){
    	$this -> assign('team',model('News') -> teamInfo());
    	return $this -> fetch();
    }
}
