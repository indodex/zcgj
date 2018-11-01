<?php
namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use think\Request;
use think\Db;

class Method extends Admin
{
	/**
	 * controller 财务列表
	 */
	public function index($p = 1){
		$map = [];
		
		// 搜索关键词
		$keywords = trim(input('keywords')) ? trim(input('keywords')) : null;
		if($keywords){
			$map['account'] = array('like','%'.$keywords.'%');
		}
		
		// 搜索充值/提现类型
		if(input('type')){
			$map['method_type'] = input('type');
			$this -> assign('get_type',input('type'));
		}
		
		// 搜索充值/提现种类
		if(input('status')){
			$map['status'] = input('status');
			$this -> assign('get_status',input('type'));
		}
		
		// 搜索审核状态
		if(input('review')){
			$map['recharge_status']  = input('review');
			$this -> assign('get_review',input('review'));
		}
		
		// 财务类型
		$type_where['type'] = 'method_type';
		$type_where['state'] = 1;
		$type = Db::name('dict') -> where($type_where) -> field('key,value') -> select();
		$this -> assign('type',$type);
		// 币种
		$status_where['type'] = 'method_status';
		$status_where['state'] = 1;
		$status = Db::name('dict') -> where($status_where) -> field('key,value') -> select();
		$this -> assign('status',$status);
		// 审核状态
		$veview_where['type'] = 'identity_status';
		$vaview_where['state'] = 1;
		$review = Db::name('dict') -> where($veview_where) -> field('key,value') -> select();
		$this -> assign('review',$review);
		
		$this -> assign('list',model('Method') -> methodList($map,$p));
		
		return $this -> fetch();
	}
	
	/**
	 * controller 财务审核
	 */
	public function review(){
		if(Request::instance() -> isPost()){
			return json(model('Method') -> is_review(input('post.')));
		}
	}
	
	
	
	/**
	 * controller 交易列表
	 */
	public function record($p = 1){		
		$map = [];
		
		// 搜索用户账户
		$keywords = input('keywords') ? input('keywords') : null;
		if($keywords){
			$map['account'] = array('like','%'.trim($keywords).'%');
		}
		
		// 查看交易类型
		$record_type = input('record_type');
		if($record_type){
			$map['record_type'] = $record_type;
		}
		$this -> assign('get_record_type',$record_type);
		
		$this -> assign('record_type',model('Common/Dict') -> showList('trade_type'));
		$this -> assign('record',model('Method') -> recordList($map,$p));
		
		return $this -> fetch();
	}
	
	/**
	 * controller 删除交易信息
	 */
	public function record_del($id){
		return json(model('Method') -> recordDel($id));
	}
	
	
	
	/**
     * controller 订单列表
     */
    public function order($p = 1){
        $map = [];
        
        // 搜索关键词
        $order = input('order') ? input('order') : null;	// 订单号
        if ($order) {
            $map['order'] = array('like', '%' . trim($order) . '%');
        }
        $buyer = input('buyer') ? input('buyer') : null;	// 买家
        if($buyer){
        	$map['buyer'] = array('like', '%' . trim($buyer) . '%');
        }
        $seller = input('seller') ? input('seller') : null;	// 卖家
        if($seller){
        	$map['seller'] = array('like', '%' . trim($seller) . '%');
        }
        
        // 查看交易类型
        $trade_type = input('trade_type');
        if($trade_type){
        	$map['trade_type'] = $trade_type;
        }
        $this -> assign('get_trade_type',$trade_type);
        
        // 查看交易状态
        $order_status = input('order_status');
        if($order_status){
        	$map['order_status'] = $order_status;
        }
        $this -> assign('get_order_status',$order_status);
        
        // 查看交易币种
        $cur_type = input('cur_id');
        if($cur_type){
        	$map['cur_id'] = $cur_type;
        }
        $this -> assign('get_cur_type',$cur_type);
        
        $this -> assign('trade_type_list', model('Common/Dict') -> showList('trade_type'));
        $this -> assign("order_status_list", model("Common/Dict") -> showList('order_status'));
        $this -> assign('cur_type',model('Method') -> get_cur_type());
        $this -> assign("order", model('Method') -> orderList($map, $p));
        
        return $this -> fetch();
    }

    /**
     * controller 删除订单
     * @param  string $id ID
     */
    public function order_del(){
        if (Request::instance() -> isPost()) {
            return json(model('Method') -> orderDel(input('post.id')));
        }
    }
}


