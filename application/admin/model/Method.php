<?php
namespace app\admin\model;

use app\common\model\Base;
use think\Request;
use think\Db;

class Method extends Base
{
	const PAGE_LIMIT = 10;	// 用户表分页限制
	const PAGE_SHOW = 10;	// 显示分页菜单数量
	
	/**
	 * model 财务列表
	 */
	public function methodList($map,$p){
		$request = Request::instance();
		
		// 查询用户账号
		if($map['account']){
			$user['account'] = $map['account'];
			$user_method_id = Db::name('user') -> where($map) -> field('id') -> select();
			$user_id = '';
			foreach($user_method_id as $k => $v){
				$user_id .= $v['id'].',';
			}
			$map['uid'] = array('in',trim($user_id,','));
			unset($map['account']);
		}
		
		$list = $this -> where($map) -> order('id DESC') -> page($p,self::PAGE_LIMIT) -> select() -> toArray();
		foreach($list as $k => $v){
			// 用户账号
			$list[$k]['user_account'] = Db::name('user') -> where('id',$v['uid']) -> value('account');
			
			// 充值/提现类型
			$type['type'] = 'method_type';
			$type['value'] = $v['method_type'];
			$list[$k]['method_type_text'] = Db::name('dict') -> where($type) -> value('key');
			switch($v['method_type']){
				case 1:
					$list[$k]['method_type_btn'] = 'method_type_red';
					break;
				case 2:
					$list[$k]['method_type_btn'] = 'method_type_green';
					break;
			}
			
			// 充值/提现种类
			$status['type'] = 'method_status';
			$status['value'] = $v['status'];
			$list[$k]['method_status_text'] = Db::name('dict') -> where($status) -> value('key');
			switch($v['status']){
				case 1:
					$list[$k]['method_status_btn'] = 'method_status_fictitious';
					break;
				case 2:
					$list[$k]['method_status_btn'] = 'method_status_rmb';
					break;
			}
			
			// 充值/提现审核
			$review['type'] = 'identity_status';
			$review['value'] = $v['recharge_status'];
			$list[$k]['review_text'] = Db::name('dict') -> where($review) -> value('key');
			switch($v['recharge_status']){
				case 2:
					$list[$k]['review_btn'] = 'review_pass';
					break;
				case 3:
					$list[$k]['review_btn'] = 'review_fail';
					break;
				default:
					$list[$k]['review_btn'] = 'review_on';
					break;
			}
		}
		$count = $this -> where($map) -> count();
		
		$return['list'] = $list;
		$return['count'] = $count;
		$return['page'] = boot_page($count,self::PAGE_LIMIT,self::PAGE_SHOW,$p,$request -> action());
		
		return $return;
	}
	
	/**
	 * model 财务审核
	 */
	public function is_review($data){
		if(!$data['id']){
			return ['code' => 0,'msg' => '未获取到要审核的信息!'];
		}
		
		if(!$data['review']){
			return ['code' => 0,'msg' => '未获取到要审核的状态!'];
		}
		
		// 查询对应财务信息
		$info = $this -> where('id',$data['id']) -> field('uid,cur_id,cur_num,money,status,method_type,url,recharge_status,create_time,update_time') -> find();
		
		Db::startTrans();
		$condition = 0;
		try{
			///// 对相应的用户进行充值&提现开始
			switch($info['method_type']){
				case 1:	// 充值
					$user_cur_where['uid'] = $info['uid'];
					$user_cur_where['cur_id'] = $info['cur_id'];
					Db::name('user_cur') -> where($user_cur_where) -> setInc('number',$info['cur_num']);
					$msg_title = '充值';
					break;
				case 2:	// 提现
					$user_cur_where['uid'] = $info['uid'];
					$user_cur_where['cur_id'] = $info['cur_id'];
					$cur_num = trim($info['cur_num'],'-');
					Db::name('user_cur') -> where($user_cur_where) -> setDec('number',$cur_num);
					$msg_title = '提现';
					break;
			}
			
			///// 生成对应用户的系统消息开始
			// 获取币种名称
			switch($info['status']){
				case 1:
					$method_name = Db::name('currency') -> where('id',$info['cur_id']) -> value('name');
					$method_name_en = Db::name('currency') -> where('id',$info['cur_id']) -> value('name');
					break;
				case 2:
					$method_name = '人民币';
					$method_name_en = 'RMB';
					break;
			}
			
			// 修改 充值/提现 审核状态
			$mod['recharge_status'] = $data['review'];
			$result = $this -> where('id',$data['id']) -> update($mod);
			
			// 获取 充值/提现 状态
			$type_where['type'] = 'method_type';
			$type_where['value'] = $this -> where('id',$data['id']) -> value('method_type');
			$method_type = Db::name('dict') -> where($type_where) -> value('key');	// 中文消息
			
			// 获取消息
			$status_where['type'] = 'identity_status';
			$status_where['value'] = $this -> where('id',$data['id']) -> value('recharge_status');
			$method_status = Db::name('dict') -> where($status_where) -> value('key');	// 中文消息
			
			// 系统消息参数
			$uid = $info['uid'];
			$title = '【个人信息】'.$msg_title;
			$content = $method_name.' '.$method_type.' '.$method_status;
			
			// 生成消息
			generate_msg($uid,$title,$content);
			///// 生成对应用户的系统消息结束
			
			$condition = 1;
			Db::commit();
		}catch(\Exception $e){
			Db::rollback();
		}
		
		if($condition === 0){
			return ['code' => 0,'msg' => '提交失败!'];
		}else{
			return ['code' => 1,'msg' => '提交成功!'];
		}
	}
	
	
	/**
	 * model 交易列表
	 */
	public function recordList($map,$p){
		$request = Request::instance();
		
		// 搜索用户账户
		if($map['account']){
			$user['account'] = $map['account'];
			$user_record_id = Db::name('user') -> field('id') -> where($user) -> select();
			$record_id = '';
			foreach($user_record_id as $k => $v){
				$record_id .= $v['id'].',';
			}
			unset($map['account']);
			$map['user_id'] = array('in',trim($record_id,','));
		}
		
		$list = Db::name('record') -> where($map) -> order('id DESC') -> page($p,self::PAGE_LIMIT) -> select();
		$count = Db::name('record') -> where($map) -> count();
		foreach($list as $k => $v){
			// 用户/交易人
			$list[$k]['user_name'] = Db::name('user') -> where('id',$v['user_id']) -> value('account');
			$list[$k]['trader_name'] = Db::name('user') -> where('id',$v['trader_id']) -> value('account');
			
			// 虚拟币
			$list[$k]['cur_name'] = Db::name('currency') -> where('id',$v['cur_id']) -> value('name');
			
			// 日期
			$list[$k]['create_date'] = date('Y-m-d H:i:s',$v['create_time']);
			
			// 交易类型
			$where['type'] = 'trade_type';
			$where['value'] = $v['record_type'];
			$list[$k]['record_type_text'] = Db::name('dict') -> where($where) -> value('key');
			switch($v['record_type']){
				case 1:
					$list[$k]['record_button'] = 'record_green';
					break;
				case 2:
					$list[$k]['record_button'] = 'record_red';
					break;
			}
		}
		
		$return['list'] = $list;
		$return['count'] = $count;
		$return['page'] = boot_page($count,self::PAGE_LIMIT,self::PAGE_SHOW,$p,$request -> action());
		return $return;
	}
	
	/**
	 * model 删除交易信息
	 */
	public function recordDel($id){
		if(!$id){
			return ['code' => 1,'msg' => '未获取要到要删除的交易信息!'];
		}
		
		$del = Db::name('record') -> where(array('id' => $id)) -> delete();
		if($del){
			return ['code' => 1,'msg' => '删除成功!'];
		}else{
			return ['code' => 0,'msg' => '删除失败,请重试!'];
		}
	}
	
	
	/**
	 * model 获取币种信息
	 */
	public function get_cur_type(){
		// $cur_where['id'] = array('neq',1);
		$result = Db::name('currency') -> where($cur_where) -> field('id,name') -> select();
		return $result;
	}
	
	/**
     * model 订单列表
     */
    public function orderList($map, $p){
        $request = Request::instance();
        
        // 搜索买家账户
        if($map['buyer']){
        	$user['account'] = $map['buyer'];
        	$user_buyer_id = Db::name('user') -> field('id') -> where($user) -> select();
        	$buyer_id = '';
        	foreach($user_buyer_id as $k => $v){
        		$buyer_id .= $v['id'].',';
        	}
        	unset($map['buyer']);
        	$map['buyer_id'] = array('in',trim($buyer_id,','));
        }
        
        // 搜索卖家账户
        if($map['seller']){
        	$user['account'] = $map['seller'];
        	$user_seller_id = Db::name('user') -> field('id') -> where($user) -> select();
        	$seller_id = '';
        	foreach($user_seller_id as $k => $v){
        		$seller_id .= $v['id'].',';
        	}
        	unset($map['seller']);
        	$map['seller_id'] = array('in',trim($seller_id,','));
        }
//      // 条件数据
//      $data['cur_pea_icon'] = Db::name('currency') -> where('id=2') -> value('icon');		// PEA 图标
//      $pea_buy_where['cur_id'] = 2;
//      $pea_buy_where['trade_type'] = 2;
//      $data['cur_pea_buy_all_num'] = Db::name('order') -> where($pea_buy_where) -> sum('order_number');
//      $data['cur_pea_buy_all_price'] = Db::name('order') -> where($pea_buy_where) -> field('order_number,price') -> sum('order_number * price');
//      $pea_sell_where['cur_id'] = 2;
//      $pea_sell_where['trade_type'] = 1;
//      $data['cur_pea_sell_all_num'] = Db::name('order') -> where($pea_sell_where) -> sum('order_number');
//      $data['cur_pea_sell_all_price'] = Db::name('order') -> where($pea_sell_where) -> field('order_number,price') -> sum('order_number * price');
//      
//      $data['cur_usdt_icon'] = Db::name('currency') -> where('id=1') -> value('icon');	// USDT 图标
//      $usdt_buy_where['cur_id'] = 1;
//      $usdt_buy_where['trade_type'] = 2;
//      $data['cur_usdt_buy_all_num'] = Db::name('order') -> where($usdt_buy_where) -> sum('order_number');
//      $data['cur_usdt_buy_all_price'] = Db::name('order') -> where($usdt_buy_where) -> field('order_number,price') -> sum('order_number * price');
//      $usdt_sell_where['cur_id'] = 1;
//      $usdt_sell_where['trade_type'] = 1;
//      $data['cur_usdt_sell_all_num'] = Db::name('order') -> where($usdt_sell_where) -> sum('order_number');
//      $data['cur_usdt_sell_all_price'] = Db::name('order') -> where($usdt_sell_where) -> field('order_number,price') -> sum('order_number * price');
        
        // 订单币种统计
        $cur_info = Db::name('currency') -> field('id,name,icon') -> select();
        foreach($cur_info as $k => $v){
        	// 求购统计
        	$cur_buy_where['cur_id'] = $v['id'];
        	$cur_buy_where['trade_type'] = 2;
        	$cur_info[$k]['buy_all_num'] = Db::name('order') -> where($cur_buy_where) -> sum('order_number');	// 求购总数量
        	$cur_info[$k]['buy_all_price'] = Db::name('order') -> where($cur_buy_where) -> field('order_number,price') -> sum('order_number * price');	// 求购总价
        	
        	// 出售总数
        	$cur_sell_where['cur_id'] = $v['id'];
        	$cur_sell_where['trade_type'] = 1;
        	$cur_info[$k]['sell_all_num'] = Db::name('order') -> where($cur_sell_where) -> sum('order_number');	// 求购总数量
        	$cur_info[$k]['sell_all_price'] = Db::name('order') -> where($cur_sell_where) -> field('order_number,price') -> sum('order_number * price');	// 求购总价
        }
        
        // 订单列表
        $list = Db::name('order') -> where($map) -> order('id DESC') -> page($p, self::PAGE_LIMIT) -> select();
        $count = Db::name('order') -> where($map) -> count();
        foreach ($list as $k => $v) {
        	// 总价
        	$list[$k]['all_price'] = $list[$k]['order_number'] * $list[$k]['price'];
        	
        	// 虚拟币
        	$list[$k]['currency'] = Db::name('currency') -> where('id',$v['cur_id']) -> value('name');
        	
        	// 买家/卖家
        	$list[$k]['buyer'] = Db::name('user') -> where('id',$v['buyer_id']) -> value('account');
        	$list[$k]['seller'] = Db::name('user') -> where('id',$v['seller_id']) -> value('account');
        	
        	// 支付日期/结束日期
        	$list[$k]['pay_time'] = isset($v['pay_time']) ? date('Y-m-d H:i:s',$v['pay_time']) : '暂无';
        	$list[$k]['done_time'] = isset($v['done_time']) ? date('Y-m-d H:i:s',$v['done_time']) : '暂无';
        	
        	// 交易类型
        	$where['type'] = 'trade_type';
        	$where['value'] = $v['trade_type'];
        	$list[$k]['trade_type_text'] = Db::name('dict') -> where($where) -> value('key');
        	switch($v['trade_type']){
        		case 1:
        			$list[$k]['trade_type_button'] = 'trade_type_green';
        			break;
        		case 2:
        			$list[$k]['trade_type_button'] = 'trade_type_red';
        			break;
        	}
        	
        	// 订单状态
        	$where['type'] = 'order_status';
        	$where['value'] = $v['order_status'];
        	$list[$k]['order_status_text'] = Db::name('dict') -> where($where) -> value('key');
        	switch($v['order_status']){
        		case 1:
        			$list[$k]['order_status_button'] = 'order_status_one';
        			break;
        		case 2:
        			$list[$k]['order_status_button'] = 'order_status_two';
        			break;
        		case 3:
        			$list[$k]['order_status_button'] = 'order_status_three';
        			break;
        	}
        }
        
        
        $return['data'] = $cur_info;
        $return['count'] = $count;
        $return['list'] = $list;
        $return['page'] = boot_page($return['count'], self::PAGE_LIMIT, self::PAGE_SHOW, $p,$request -> action());
        
        return $return;
    }

    /**
     * model 删除订单
     */
    public function orderDel($id){
    	
    	if(!$id){
    		return ['code' => 0,'msg' => '未获取到要删除的订单信息!'];
    	}
    	
    	$del = Db::name('order') -> where(array('id' => $id)) -> delete();
        if($del){
            return ['code' => 1,'msg' => '删除成功'];
        }else{
            return ['code' => 0,'msg' => '删除失败,请重试'];
        }
    }
}


