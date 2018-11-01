<?php
namespace app\admin\model;

use app\common\model\Base;
use think\Request;
use think\Db;

class Trade extends Base
{
	const PATH_LIMIT = 10;	// 用户表分页限制
	const PATH_SHOW = 10;	// 显示分页菜单数量
	
	/**
	 * model 挂单列表
	 */
	public function tradeList($map,$p){
		
		$request = Request::instance();
		
		// 搜索挂卖人名称
		if($map['account']){
			$user['account'] = $map['account'];
			$user_seller_id = Db::name('user') -> field('id') -> where($user) -> select();
			$seller_id = '';
			foreach($user_seller_id as $k => $v){
				$seller_id .= $v['id'].',';
			}
			unset($map['account']);
			$map['uid'] = array('in',trim($seller_id,','));
		}
		
//		// 条件数据(PEA)
//      $data['cur_pea_icon'] = Db::name('currency') -> where('id=2') -> value('icon');		// PEA 图标
//      $pea_buy_where['trade_status'] = 1;
//      $pea_buy_where['cur_id'] = 2;
//      $pea_buy_where['trade_type'] = 2;
//      $data['cur_pea_buy_all_num'] = Db::name('trade') -> where($pea_buy_where) -> sum('number');
//      $data['cur_pea_buy_all_price'] = Db::name('trade') -> where($pea_buy_where) -> field('number,price') -> sum('number * price');
//      $pea_sell_where['trade_status'] = 1;
//      $pea_sell_where['cur_id'] = 2;
//      $pea_sell_where['trade_type'] = 1;
//      $data['cur_pea_sell_all_num'] = Db::name('trade') -> where($pea_sell_where) -> sum('number');
//      $data['cur_pea_sell_all_price'] = Db::name('trade') -> where($pea_sell_where) -> field('number,price') -> sum('number * price');
		
		// 挂单币种统计
		$cur_info = Db::name('currency') -> field('id,name,icon') -> select();
		foreach($cur_info as $k => $v){
			// 求购统计
			$cur_buy_where['trade_status'] = 1;
        	$cur_buy_where['cur_id'] = $v['id'];
        	$cur_buy_where['trade_type'] = 2;
        	$cur_info[$k]['cur_buy_all_num'] = Db::name('trade') -> where($cur_buy_where) -> sum('number');
        	$cur_info[$k]['cur_buy_all_price'] = Db::name('trade') -> where($cur_buy_where) -> field('number,price') -> sum('number * price');
        	
        	// 出售统计
        	$cur_sell_where['trade_status'] = 1;
        	$cur_sell_where['cur_id'] = $v['id'];
        	$cur_sell_where['trade_type'] = 1;
        	$cur_info[$k]['cur_sell_all_num'] = Db::name('trade') -> where($cur_sell_where) -> sum('number');
        	$cur_info[$k]['cur_sell_all_price'] = Db::name('trade') -> where($cur_sell_where) -> field('number,price') -> sum('number * price');
		}
		
		$list = $this -> where($map) -> order('id DESC') -> page($p,self::PATH_LIMIT) -> select() -> toArray();
		$count = $this -> where($map) -> count();
		foreach($list as $k => $v){
			// 挂卖人名称
			$list[$k]['user_name'] = Db::name('user') -> where('id',$v['uid']) -> value('account');
			
			// 挂卖状态
			$where['type'] = 'trade_status';
			$where['value'] = $v['trade_status'];
			$list[$k]['trade_status_text'] = Db::name('dict') -> where($where) -> value('key');
			switch($v['trade_status']){
				case 2:
					$list[$k]['trade_status_button'] = 'trade_status_active';	// 交易中
					break;
				case 3:
					$list[$k]['trade_status_button'] = 'trade_status_visited';	// 交易完成
					break;
				case 4:
					$list[$k]['trade_status_button'] = 'trade_status_hover';	// 交易取消
					break;
				default :
					$list[$k]['trade_status_button'] = 'trade_status_link';		// 挂卖中
			}
			
			// 虚拟币
			$list[$k]['cur_name'] = Db::name('currency') -> where('id',$v['cur_id']) -> value('name');
			
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
		}
		
		$return['data'] = $cur_info;
		$return['list'] = $list;
		$return['count'] = $count;
		$return['page'] = boot_page($count,self::PATH_LIMIT,self::PATH_SHOW,$p,$request -> action());
		
		return $return;
	}
	
	/**
	 * model 驳回挂单信息
	 */
	public function tradeReject($id){
		if(!$id){
			return ['code' => 0,'msg' => '未获取挂单信息!'];
		}
		$info = $this -> where('id',$id) -> field('uid,number,price,trade_type,cur_id') -> find();
		$condition = 0;
		switch($info['trade_type']){
			case 1:	// 出售
				Db::startTrans();
				try{
					$where['uid'] = $info['uid'];
					$where['cur_id'] = $info['cur_id'];
					$number = $info['number'];
					// 返回用户出售时提前扣出的USDT
					Db::name('user_cur') -> where($where) -> setInc('number',$number);
					$this -> where('id',$id) -> delete();
					$condition = 1;
					Db::commit();
				}catch(\Exception $e){
					Db::rollback();
				}
				break;
			case 2:	// 求购
				$del = $this -> where('id',$id) -> delete();
				if($del){
					$condition = 1;
				}
				break;
		}
		
		if($condition === 1){
			return ['code' => 1,'msg' => '驳回成功!'];
		}else{
			return ['code' => 0,'msg' => '驳回失败!'];
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
}


