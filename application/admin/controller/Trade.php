<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;

class Trade extends Admin
{
	
	const PAGE_LIMIT = '10';	// 用户表分页限制
    const PAGE_SHOW = '10';		// 显示分页菜单数量
	
	/**
	 * controller 挂单列表
	 */
	public function index($p = 1){
		$map = [];
		
		// 挂卖人名称
		$keywords = input('keywords') ? input('keywords') : null;
		if($keywords){
			$map['account'] = array('like','%'.trim($keywords).'%');
		}
		
		// 查看挂卖状态
		$trade_status = input('trade_status');
		if($trade_status){
			$map['trade_status'] = $trade_status;
		}
		$this -> assign('get_trade_status',$trade_status);
		
		// 查看交易类型
		$trade_type = input('trade_type');
		if($trade_type){
			$map['trade_type'] = $trade_type;
		}
		$this -> assign('get_trade_type',$trade_type);
		
		// 查看挂单币种
        $cur_type = input('cur_id');
        if($cur_type){
        	$map['cur_id'] = $cur_type;
        }
        $this -> assign('get_cur_type',$cur_type);
		
		$this -> assign('trade_status',model('Common/Dict') -> showList('trade_status'));
		$this -> assign('trade_type',model('Common/Dict') -> showList('trade_type'));
		$this -> assign('cur_type',model('Trade') -> get_cur_type());
		$this -> assign('list',model('Trade') -> tradeList($map,$p));
		
		return $this -> fetch();
	}
	
	/**
	 * controller 驳回挂单信息
	 */
	public function reject($id){
		return json(model('Trade') -> tradeReject($id));
	}
}

