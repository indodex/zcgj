<?php
namespace  app\index\model;

use app\common\model\Base;
use think\Request;
use think\db;

class C2c extends Base
{
	/**
	 * model 
	 */

	public function buySellList()
	{
		$buy_map['trade_status'] = 1;
		$buy_map['trade_type'] = 2;
		$buy_map['trade_mold'] = 1;
		$buy_list = db('trade')->where($buy_map)->select();
		foreach ($buy_list as $k => $v) {
			$traderinfo = db('user')->where('id',$v['uid'])->find();
			if($traderinfo['identity_status'] == 1){
				$buy_list[$k]['name'] = $traderinfo['account'].'(已实名)';
			}else{
				$buy_list[$k]['name'] = $traderinfo['account'].'(未实名)';
			}
			
		}
		$sell_map['trade_status'] = 1;
		$sell_map['trade_type'] = 1;
		$sell_map['trade_mold'] = 1;
		$sell_list = db('trade')->where($sell_map)->select();
		foreach ($sell_list as $k => $v) {
			$traderinfo = db('user')->where('id',$v['uid'])->find();
			if($traderinfo['identity_status'] == 1){
				$sell_list[$k]['name'] = $traderinfo['account'].'(已实名)';
			}else{
				$sell_list[$k]['name'] = $traderinfo['account'].'(未实名)';
			}
			
		}
		$list['buy_list'] = $buy_list;
		$list['sell_list'] = $sell_list;
		return $list;
	}

	public function transactionActive($data)
    {
    	if(session('uid')){
    		// 判断可执行交易时间域
        	$time_zone = $this -> get_curr_time_section();
        	if($time_zone === true || empty(config('START_TRADE')) || empty(config('END_TRADE'))){
        		// 查询指定交易C2C挂单中的数据
		        $tradeinfo = db('trade')->where('id',$data['id'])->find();
		        if($tradeinfo['trade_status'] == 1){	// 判断挂单状态为 挂卖中
		            if(session('uid') == $tradeinfo['uid']){
		                return ['status'=>0,'info'=>'不能交易自己的商品','en_info' => "Can't trade your own goods"];
		            }else{
		                if($tradeinfo['trade_type'] == 1 ){	// 判断为出售
		                    $buyer_id = session('uid');
		                    $seller_id = $tradeinfo['uid'];
		                    $map['payment_method'] = $data['payment_method'];
		                }else{	// 判断为求购
		                    $buyer_id = $tradeinfo['uid'];	// 买家
		                    $seller_id = session('uid');	// 卖家
		                    
		                    // 以卖家的身份卖给求购的买家
		                    $user_map['uid'] = session('uid');
		                    $user_map['cur_id'] = 1;
		                    $user_usdt = db('user_cur')->where($user_map)->value('number');
		                    if($user_usdt < $tradeinfo['number']){
		                    	return ['status'=>0,'info'=>'失败','en_info' => 'Fail'];
		                    }
		                    db('user_cur')->where($user_map)->setDec('number',$tradeinfo['number']);
		                }
		                $map['order'] = generateOrderNumber();
		                $map['order_number'] = $tradeinfo['number'];
		                $map['price'] = $tradeinfo['price'];
		                $map['buyer_id'] = $buyer_id;
		                $map['seller_id'] = $seller_id;
		                $map['addtime'] = time();
		                $map['trade_id'] = $tradeinfo['id'];
		                $map['trade_type'] = $tradeinfo['trade_type'];
		                $map['cur_id'] = $tradeinfo['cur_id'];
		                $map['trade_mold'] = 1;
		                // 挂入订单表
		                if($last_order_id = db('order')->insertGetId($map)){
		                    db('trade')->where(array('id'=>$tradeinfo['id']))->update(array('trade_status'=>2));
		                    return ['status'=>1,'data' => $last_order_id,'info'=>'成功','en_info' => 'Success'];
		                }else{
		                    return ['status'=>0,'info'=>'失败','en_info' => 'Fail'];
		                }
		            }
		        }else{
		            return ['status' => 0, 'info'=>'该委托不能交易','en_info' => 'The commission cannot be traded'];
		        }
	        }else{
	        	return ['status' => 0,'info' => '交易时间为'.config('START_TRADE').'~'.config('END_TRADE'),'en_info' => 'Trading hours are '.config('START_TRADE').'~'.config('END_TRADE')];
	        }
    	}else{
    		return ['status' => 0, 'info'=>'请登录','en_info' => 'Please sign in'];
    	}
    }
    
    // 判断每天交易时间
	public function get_curr_time_section(){
		$checkDayStr = date('Y-m-d ',time());
		$begin = config('START_TRADE');
		$end = config('END_TRADE');
		$timeBegin = strtotime($checkDayStr.$begin);
		$timeEnd = strtotime($checkDayStr.$end);
		$curr_time = time();
		if($curr_time >= $timeBegin && $curr_time <= $timeEnd){
			return true;
		}else{
			return false;
		}
	}
}
