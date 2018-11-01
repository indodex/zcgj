<?php
namespace app\index\controller;

use app\common\controller\Base;
use think\Request;

class Game extends Base
{
	/**
	 * controller 游戏中心首页
	 */
	public function index(){
		// 获取用户ID
		$uid = $_SESSION['think']['uid'];
		$this -> assign('uid',$uid);
		
		// 获取游戏期号ID
		$this -> assign('game',model('Game') -> gameInfo($uid));
		
		return $this -> fetch();
	}
	
	/**
	 * controller 获取是否有正在游戏进行
	 */
	public function have_game(){
		return json(model('Game') -> haveGame());
	}
	
	/**
	 * controller 获取种子数量并计算所需要的ETH数量
	 */
	public function seed_etc($gid,$seed_total){
		return json(model('Game') -> seedEtc($gid,$seed_total));
	}
	
	/**
	 * controller 点击购买种子
	 */
	public function seed_buy(){
		if(Request::instance() -> isPost()){
			return json(model('Game') -> seedBuy(input('post.')));
		}
	}
}
