<?php
namespace  app\index\model;
use app\common\model\Base;
use think\Request;
use think\db;
use think\Session;
class Trade extends Base
{
//	// K线图
//	public function klineList($cur_id){
//		$fileurl = "./json/cur".$cur_id.".json";
//      $string = file_get_contents($fileurl);
//      $list = json_decode($string, true);
//      $list = array_slice($list,(count($list)-300));
////      $list = array_reverse($list);
//      $data['lines'] = [];
//		foreach($list as $k => $v){
//			$data['lines'][$k][0] = $v['time']*1000;
//			$data['lines'][$k][1] = (float)$v['open_price'];
//			$data['lines'][$k][2] = (float)$v['max_price'];
//			$data['lines'][$k][3] = (float)$v['min_price'];
//			$data['lines'][$k][4] = (float)$v['close_price'];
//			$data['lines'][$k][5] = (float)$v['vol'];
//		}
//		
//		$ret['success'] = 'true';
//		$ret['data'] = $data;
//		return $ret;
//	}
	
//	// K线图
//	public function klineList($cur_id,$range){
//		$now_time = time();	// 当前时间
//		$set_time = time() - $range;	// 查看区间时间
//		// 获取最后一条id
//		$lines = Db::name('kline') -> where('cur_id',$cur_id) -> whereTime('time','between',[$now_time,$set_time]) -> field('time,open_price,max_price,min_price,close_price,vol') -> order('time DESC') -> limit(3000) -> select();
//		$lines = array_reverse($lines);
//		$data['lines'] = [];
//		foreach($lines as $k => $v){
////			$line_data = [];
////			$line_data[] = $v['time']*1000;
////			$line_data[] = (float)$v['open_price'];
////			$line_data[] = (float)$v['max_price'];
////			$line_data[]= (float)$v['min_price'];
////			$line_data[]= (float)$v['close_price'];
////			$line_data[] = (float)$v['vol'];
//			$data['lines'][$k][0] = $v['time']*1000;
//			
//			$data['lines'][$k][1] = (float)$v['open_price'];
//			$data['lines'][$k][2] = (float)$v['max_price'];
//			$data['lines'][$k][3] = (float)$v['min_price'];
//			$data['lines'][$k][4] = (float)$v['close_price'];
//			$data['lines'][$k][5] = (float)$v['vol'];
//			//$data['lines'][$k] = $line_data;
//		}
//		
//		$ret['success'] = 'true';
//		$ret['data'] = $data;
//		return $ret;
//	}
	
//	// K线图
//	public function klineList($cur_id,$range){
//		$set_time =  $range/60000;	// 查看区间时间(转换单位为:分钟)
//		$ii = 0;
//		$info = [];
//	    for($i = 0 ;$i < 1000;$i++){
//	    	$start_time = time() - 60 * $set_time * $ii;
//	    	$end_time = $start_time - 60 * $set_time;
//	    	$info[$i] = Db::name('kline') -> where('cur_id',2) -> whereTime('time','between',[$end_time,$start_time]) -> field('time,open_price,max_price,min_price,close_price,vol') ->order('time DESC') -> find();
//			$info[$i]['time'] = $start_time * 1000;
//	    	$ii = $ii + 1;
//	    }
//	    
//	    // 反转数组
//	    $info = array_reverse($info);
//	    $data['lines'] = [];
//	    foreach($info as $k => $v){
//	    	$data['lines'][$k ][0] = $v['time'];
//			$data['lines'][$k ][1] = (float)$v['open_price'];
//			$data['lines'][$k ][2] = (float)$v['max_price'];
//			$data['lines'][$k ][3] = (float)$v['min_price'];
//			$data['lines'][$k ][4] = (float)$v['close_price'];
//			$data['lines'][$k ][5] = (float)$v['vol'];
//	    }
//	    
//	    $ret['success'] = 'true';
//		$ret['data'] = $data;
//		return $ret;
//	}
	
	// K线图
	public function klineList($cur_id,$range)
    {
        $i = 0;
        $ii = 0;
        $time = $range/60000;
        $fileurl = "./json/cur".$cur_id.".json";
        $string = file_get_contents($fileurl);
        $list = json_decode($string, true);

    	for($s=count($list)-1;$s>=0;$s-=$time){
        	$kline_arr['lines'][$ii][0] = $list[$s]['time']*1000;
            $kline_arr['lines'][$ii][1] = (float)$list[$s]['open_price'];
            $kline_arr['lines'][$ii][2] = (float)$list[$s]['max_price'];
            $kline_arr['lines'][$ii][3] = (float)$list[$s]['min_price'];
            $kline_arr['lines'][$ii][4] = (float)$list[$s]['close_price'];
            $kline_arr['lines'][$ii][5] = (float)$list[$s]['vol'];
            $ii = $ii + 1;
        }
        

		$kline_arr['lines'] = array_reverse($kline_arr['lines'] );
        $ret['success'] = 'true';
		$ret['data'] = $kline_arr;
		return $ret;
    }
	// K线图
	public function klineList2($cur_id,$range)
    {
        $i = 0;
        $ii = 0;
        $time = $range/60000;
        $list = Db::name('kline') -> where('cur_id',$cur_id)->order('time DESC')->limit(15000) -> select();
        //pre($list);
        $list = array_reverse($list);
        foreach ($list as $k => $v) {
            if($k == $i){
            	$kline_time2[] = time()-$time*60*$ii;
            	$kline_time = time()-$time*60*$ii*1000;
                $kline_arr['lines'][$ii][0] = $v['time'];
                $kline_arr['lines'][$ii][1] = (float)$v['open_price'];
                $kline_arr['lines'][$ii][2] = (float)$v['max_price'];
                $kline_arr['lines'][$ii][3] = (float)$v['min_price'];
                $kline_arr['lines'][$ii][4] = (float)$v['close_price'];
                $kline_arr['lines'][$ii][5] = (float)$v['vol'];
                $i = $i + $time*60;
                $ii = $ii + 1;
            }

        }
        $kline_time2 = array_reverse($kline_time2);
       // pre($kline_time2 );
        foreach($kline_time2 as $k =>$v){
        	$kline_arr['lines'][$k][0] = $v*1000;
        	if(!$this->get_curr_time_section2($v)){
	    	 	$kline_arr['lines'][$k][1] = 0;
	            $kline_arr['lines'][$k][2] = 0;
	            $kline_arr['lines'][$k][3] = 0;
	            $kline_arr['lines'][$k][4] = 0;
	            $kline_arr['lines'][$k][5] = 0;
        	}
        	
        }
        $ret['success'] = 'true';
		$ret['data'] = $kline_arr;
		return $ret;
    }

	// 判断时间是否在不可交易时间内
    public function get_curr_time_section2($time){
        $checkDayStr = date('Y-m-d ',$time);
        $begin = config('START_TRADE');
        $end = config('END_TRADE');
        $timeBegin = strtotime($checkDayStr.$begin);
        $timeEnd = strtotime($checkDayStr.$end);
        $curr_time = $time;
        if($curr_time >= $timeBegin && $curr_time <= $timeEnd){
            return true;
        }else{
            return false;
        }
    }

    // 挂买挂卖
    public function buySell($data){
        if(session('uid')){
        	// 判断可执行交易时间域
        	$time_zone = $this -> get_curr_time_section();
        	if($time_zone === true || empty(config('START_TRADE')) || empty(config('END_TRADE'))){
	            if(!$data['price'] || !$data['number'] || !$data['pwd']){
	                return ['status'=>0, 'info'=>'不能为空','en_info' => 'Can not be empty'];
	            }else{
	            	if($data['price'] <= 0 || $data['number'] <= 0){
	            		return ['status' => 0,'info' => '数值不能小于0'];
	            	}
	            	$data['price'] = sprintf('%.4f',$data['price']);
	            	$data['number'] = sprintf('%.2f',$data['number']);
	            	
        			// 获取当前用户信息
        			$userinfo = db('user')->where('id',session('uid'))->find();
	                if($userinfo['identity_status'] == 1){
	                    if($userinfo['payment_password'] == encrypt(trim($data['pwd']))){
	                        // 在交易表中插入数据
	                        if($data['type'] == 1){	// 卖 条件
	                            $map['uid'] = session('uid');
	                            $map['cur_id'] = $data['cur_id'];
	                            $cur_number = db('user_cur')->where($map)->value('number');
	                            if($cur_number < $data['number']){
	                                return ['status'=>0, 'info'=>'虚拟币不足','en_info' => 'Insufficient voice'];
	                            }
	                        }else{	// 买 条件
	                            $map['uid'] = session('uid');
	                            $map['cur_id'] = 1;
	                            $cur_number = db('user_cur')->where($map)->value('number');
	                            if($cur_number < ($data['number']*$data['price']) ){
	                                return ['status'=>0, 'info'=>'USDT不足','en_info' => 'USDT insufficient'];
	                            }
	                        }
	                        
	                        // 获取币种的 涨跌幅
			            	$rise_fall = Db::name('currency') -> where('id',$data['cur_id']) -> value('rise_fall');
			            	if($rise_fall){
			            		// 今日的开盘价
			            		$open_price = Db::name('trade') -> where('cur_id',$data['cur_id']) -> whereTime('start_time','today') -> order('start_time ASC') -> value('price');
			            		// 判断是否为今天第一笔挂单
			            		if($open_price){
				            		$up = sprintf('%.2f',$open_price + $open_price * ($rise_fall * 0.01));		// 交易涨幅
				            		$down = sprintf('%.2f',$open_price - $open_price * ($rise_fall * 0.01));	// 交易跌幅
				            		// 判断是否超过 涨跌幅
				            		if($data['price'] > $up || $data['price'] < $down){
				            			return ['status' => 0,'info' => '单价应在'.$up.'和'.$down.'之间!','en_info' => 'Unit price should be between '.$up.' and '.$down.'!'];
				            		}else{
				            			/////////
				                        $insert_data['uid'] = session('uid');
				                        $insert_data['number'] = $data['number'];
				                        $insert_data['price'] = $data['price'];
				                        $insert_data['start_time'] = time();
				                        $insert_data['trade_type'] = $data['type'];
				                        $insert_data['cur_id'] = $data['cur_id'];
				                        $last_trade_id = db('trade')->insertGetId($insert_data);
				                        
				                        // 扣除用户相应的金额
				                        if($data['type'] == 1){	// 卖
				                            $map['uid'] = session('uid');
				                            $map['cur_id'] = $data['cur_id'];
				                            db('user_cur')->where($map)->setDec('number',$data['number']);
				                        }else{	// 买
				                            $map['uid'] = session('uid');
				                            $map['cur_id'] = 1;
				                            db('user_cur')->where($map)->setDec('number',($data['number']*$data['price']));
				                        }
				                        
				                        // 判断是否存在对应交易的挂单数据,如果存在则直接交易并扣除手续费
				                        if($data['type'] == 1){	// 卖
				                        	$uid = session('uid');
				                        	$this -> suitable_trader_sell($last_trade_id,$uid,2,$data['cur_id'],$data['price'],$data['number']);
				                        }else{	// 买
				                        	$uid = session('uid');
				                        	$this -> suitable_trader_buy($last_trade_id,$uid,1,$data['cur_id'],$data['price'],$data['number']);
				                        }
		                        		
		                        		// 插入 币种行情统计表
		                        		$cur_market = $this -> cur_market($data['cur_id']);
		                        		if($cur_market === false){
		                        			return ['status' => 0,'info' => '获取币种行情统计失败!'];
		                        		}
		                        		
		                        		return ['status'=>1, 'info'=>'成功','en_info' => 'Success'];
		                        	}
	                        	}else{
	                        		/////////
			                        $insert_data['uid'] = session('uid');
			                        $insert_data['number'] = $data['number'];
			                        $insert_data['price'] = $data['price'];
			                        $insert_data['start_time'] = time();
			                        $insert_data['trade_type'] = $data['type'];
			                        $insert_data['cur_id'] = $data['cur_id'];
			                        $last_trade_id = db('trade')->insertGetId($insert_data);
			                        
			                        // 扣除用户相应的金额
			                        if($data['type'] == 1){	// 卖
			                            $map['uid'] = session('uid');
			                            $map['cur_id'] = $data['cur_id'];
			                            db('user_cur')->where($map)->setDec('number',$data['number']);
			                        }else{	// 买
			                            $map['uid'] = session('uid');
			                            $map['cur_id'] = 1;
			                            db('user_cur')->where($map)->setDec('number',($data['number']*$data['price']));
			                        }
			                        
			                        // 判断是否存在对应交易的挂单数据,如果存在则直接交易并扣除手续费
			                        if($data['type'] == 1){	// 卖
			                        	$uid = session('uid');
			                        	$this -> suitable_trader_sell($last_trade_id,$uid,2,$data['cur_id'],$data['price'],$data['number']);
			                        }else{	// 买
			                        	$uid = session('uid');
			                        	$this -> suitable_trader_buy($last_trade_id,$uid,1,$data['cur_id'],$data['price'],$data['number']);
			                        }
	                        		
	                        		// 插入 币种行情统计表
	                        		$cur_market = $this -> cur_market($data['cur_id']);
	                        		if($cur_market === false){
	                        			return ['status' => 0,'info' => '获取币种行情统计失败!'];
	                        		}
	                        		
	                        		return ['status'=>1, 'info'=>'成功','en_info' => 'Success'];
	                        	}
	                        }else{
	                        	/////////
		                        $insert_data['uid'] = session('uid');
		                        $insert_data['number'] = $data['number'];
		                        $insert_data['price'] = $data['price'];
		                        $insert_data['start_time'] = time();
		                        $insert_data['trade_type'] = $data['type'];
		                        $insert_data['cur_id'] = $data['cur_id'];
		                        $last_trade_id = db('trade')->insertGetId($insert_data);
		                        
		                        // 扣除用户相应的金额
		                        if($data['type'] == 1){	// 卖
		                            $map['uid'] = session('uid');
		                            $map['cur_id'] = $data['cur_id'];
		                            db('user_cur')->where($map)->setDec('number',$data['number']);
		                        }else{	// 买
		                            $map['uid'] = session('uid');
		                            $map['cur_id'] = 1;
		                            db('user_cur')->where($map)->setDec('number',($data['number']*$data['price']));
		                        }
		                        
		                        // 判断是否存在对应交易的挂单数据,如果存在则直接交易并扣除手续费
		                        if($data['type'] == 1){	// 卖
		                        	$uid = session('uid');
		                        	$this -> suitable_trader_sell($last_trade_id,$uid,2,$data['cur_id'],$data['price'],$data['number']);
		                        }else{	// 买
		                        	$uid = session('uid');
		                        	$this -> suitable_trader_buy($last_trade_id,$uid,1,$data['cur_id'],$data['price'],$data['number']);
		                        }
//		                        // 执行添加K线图数据
//                      		$this -> kLineGraph($last_trade_id,$data['cur_id']);
                        		
                        		// 插入 币种行情统计表
                        		$cur_market = $this -> cur_market($data['cur_id']);
                        		if($cur_market === false){
                        			return ['status' => 0,'info' => '获取币种行情统计失败!'];
                        		}
                        		
                        		return ['status'=>1, 'info'=>'成功','en_info' => 'Success'];
	                        }
	                    }else{
	                        return ['status'=>0, 'info'=>'支付密码不正确','en_info' => 'Payment password is incorrect'];
	                    }
	                }else{
	                    return ['status'=>0, 'info'=>'请先实名认证','en_info' => 'Please first real name certification'];
	                }
	            	
	            }
	        }else{
	        	return ['status' => 0,'info' => '交易时间为'.config('START_TRADE').'~'.config('END_TRADE'),'en_info' => 'Trading hours are '.config('START_TRADE').'~'.config('END_TRADE')];
	        }
        }else{
            return ['status'=>0, 'info'=>'请登录','en_info' => 'Please sign in'];
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
	
	// 判断是否存在 地应需求的交易
	/**
	 * $last_trade_id	得到交易表中刚刚交易的记录ID
	 * $uid				用户ID
	 * $trade_type		交易类型 1出售 2求购
	 * $cur_id			虚拟币ID
	 * $price			交易单价
	 * $number			交易数量
	 */
	// 买
	protected function suitable_trader_buy($last_trade_id,$uid,$trade_type,$cur_id,$price,$number){
		$exist_where['trade_type'] = $trade_type;	// 1卖
		$exist_where['cur_id'] = $cur_id;	// 币种
		$exist_where['price'] = array('<=',$price);		// 单价
		$exist_where['trade_status'] = 1;	// 挂卖中
		$exist_where['trade_mold'] = 0;
//		$exist_where['uid'] = array('neq',$uid);	// 不等于自己
		$exist = Db::name('trade') -> where($exist_where) -> order('price ASC') -> find();
		if($exist){
			// 插入订单条件
			$history_map['trade_id'] = $last_trade_id;
			$history_map['order'] = generateOrderNumber();
	        $history_map['order_number'] = $exist['number'];
	        $history_map['price'] = $exist['price'];
	        $history_map['order_status'] = 3;
	        $history_map['addtime'] = time();
	        $history_map['done_time'] = time();
	        $history_map['trade_type'] = 2;
	        $history_map['cur_id'] = $exist['cur_id'];
        	$history_map['buyer_id'] = $uid;
	    	$history_map['seller_id'] = $exist['uid'];
			
			// 数量正好匹配
			if($number == $exist['number']){
				if($price >= $exist['price']){
					Db::startTrans();
					try{
						// 修改购买信息
				    	$this -> mod_buy_info($cur_id,$uid,$price,$exist['price'],$exist['number'],$exist['uid']);
				    	
				    	// 插入订单
						$order_id = Db::name('order') -> insertGetId($history_map);	// 历史交易人
						
						// 修改交易表状态并插入 order_id
						$trade_mod['trade_status'] = 3;
						$trade_mod['end_time'] = time();
						$trade_mod['order_id'] = $order_id;	// 从订单表中得到最后交易状态
						Db::name('trade') -> where('id',$exist['id']) -> update($trade_mod);	// 历史交易人
						Db::name('trade') -> where('id',$last_trade_id) -> update($trade_mod);	// 当前交易人
						
						Db::commit();
					}catch(\Exception $e){
						Db::rollback();
					}
				}
			}
			
			// 实际购买大于挂单数量
			if($number > $exist['number']){
				if($price >= $exist['price']){
					Db::startTrans();
					try{
						// 修改购买信息
				    	$this -> mod_buy_info($cur_id,$uid,$price,$exist['price'],$exist['number'],$exist['uid']);
				    	
				    	// 插入订单
						$order_id = Db::name('order') -> insertGetId($history_map);	// 历史交易人
						
						// 修改交易表状态
						$trade_mod['trade_status'] = 3;
						$trade_mod['end_time'] = time();
						$trade_mod['order_id'] = $order_id;
						Db::name('trade') -> where('id',$exist['id']) -> update($trade_mod);	// 历史交易人
						
						// 修改已交易的挂卖信息并将多出的部分再次挂卖
						$mod_trade['number'] = $number - $exist['number'];
						$mod_trade['number'] = sprintf('%.2f',$mod_trade['number']);
						Db::name('trade') -> where('id',$last_trade_id) -> update($mod_trade);
						$this -> suitable_trader_buy($last_trade_id,$uid,1,$cur_id,$price,$mod_trade['number']);
						
						Db::commit();
					}catch(\Exception $e){
						Db::rollback();
					}
				}
			}
			
			// 实际购买小于挂单数量
			if($number < $exist['number']){
				if($price >= $exist['price']){
					Db::startTrans();
					try{
						// 判断是否需要返回给买家的差价
						$back_where['uid'] = $uid;
						$back_where['cur_id'] = 1;
						$back_num = sprintf('%.4f',($price - $exist_price) * $exist_number);
						Db::name('user_cur') -> where($back_where) -> setInc('number',$back_num);
						
						// 计算手续费 修改买家交易金额
				    	$buy_where['uid'] = $uid;
				    	$buy_where['cur_id'] = $cur_id;
				    	$buy_num = sprintf('%.4f',$number - ($number * config('BUY_SERVICE_CHARGE')));
				    	Db::name('user_cur') -> where($buy_where) -> setInc('number',$buy_num);
				    	
				    	// 计算手续费 修改卖家交易金额
				    	$sell_where['uid'] = $exist['uid'];
				    	$sell_where['cur_id'] = 1;
				    	$service_charge = sprintf('%.4f',$exist['price'] * $number * config('BUY_SERVICE_CHARGE'));
				    	$buy_num = sprintf('%.4f',$exist['price'] * $number - $service_charge);
				    	Db::name('user_cur') -> where($sell_where) -> setInc('number',$buy_num);
				    	
				    	// 插入订单
						$history_map['trade_id'] = $last_trade_id;
				        $history_map['order_number'] = $number;
						$order_id = Db::name('order') -> insertGetId($history_map);	// 当前交易人
				    	
				    	// 修改交易表状态
				    	$trade_mod['trade_status'] = 3;
						$trade_mod['end_time'] = time();
						$trade_mod['order_id'] = $order_id;
						Db::name('trade') -> where('id',$last_trade_id) -> update($trade_mod);	// 当前交易人
						
						// 修改已交易的挂卖信息并将多出的部分再次挂卖
						$mod_trade['number'] = $exist['number'] - $number;
						$mod_trade['number'] = sprintf('%.2f',$mod_trade['number']);
						Db::name('trade') -> where('id',$exist['id']) -> update($mod_trade);
						
						Db::commit();
					}catch(\Exception $e){
						Db::rollback();
					}
				}
			}
		}
	}
	
	// 修改购买信息
	protected function mod_buy_info($cur_id,$uid,$price,$exist_price,$exist_number,$exist_uid){
		// 判断是否需要返回给买家的差价
		$back_where['uid'] = $uid;
		$back_where['cur_id'] = 1;
		$back_num = sprintf('%.4f',($price - $exist_price) * $exist_number);
		Db::name('user_cur') -> where($back_where) -> setInc('number',$back_num);
    	
    	// 计算手续费 修改用户交易金额
    	$buy_where['uid'] = $uid;
    	$buy_where['cur_id'] = $cur_id;
    	$buy_num = sprintf('%.4f',$exist_number - ($exist_number * config('BUY_SERVICE_CHARGE')));
    	Db::name('user_cur') -> where($buy_where) -> setInc('number',$buy_num);
    	
    	// 计算手续费 修改卖家交易金额
    	$sell_where['uid'] = $exist_uid;
    	$sell_where['cur_id'] = 1;
    	$service_charge = sprintf('%.4f',$exist_price * $exist_number * config('BUY_SERVICE_CHARGE'));
    	$buy_num = sprintf('%.4f',$exist_price * $exist_number - $service_charge);
    	Db::name('user_cur') -> where($sell_where) -> setInc('number',$buy_num);
	}
	
	// 卖
	protected function suitable_trader_sell($last_trade_id,$uid,$trade_type,$cur_id,$price,$number){
		$exist_where['trade_type'] = $trade_type;	// 2买
		$exist_where['cur_id'] = $cur_id;	// 币种
		// $exist_where['price'] = array('>=',$price);		// 单价
		$exist_where['trade_status'] = 1;	// 挂卖中
		$exist_where['trade_mold'] = 0;
//		$exist_where['uid'] = array('neq',$uid);	// 不等于自己
		$exist = Db::name('trade') -> where($exist_where) -> where('price >='.$price) -> order('price DESC') -> find();
		
		if($exist){
			// 插入订单条件
			$history_map['trade_id'] = $last_trade_id;
			$history_map['order'] = generateOrderNumber();
	        $history_map['order_number'] = $exist['number'];
	        $history_map['price'] = $exist['price'];
	        $history_map['order_status'] = 3;
	        $history_map['addtime'] = time();
	        $history_map['done_time'] = time();
	        $history_map['trade_type'] = 1;
	        $history_map['cur_id'] = $exist['cur_id'];
        	$history_map['buyer_id'] = $uid;
	    	$history_map['seller_id'] = $exist['uid'];
			
			// 数量正好匹配
			if($number == $exist['number']){
				Db::startTrans();
				try{
					// 修改出售信息
			    	$this -> mod_sell_info($cur_id,$uid,$price,$exist['price'],$exist['number'],$exist['uid']);
			    	
			    	// 插入订单
					$order_id = Db::name('order') -> insertGetId($history_map);	// 历史交易人
					
					// 修改交易表状态
					$trade_mod['trade_status'] = 3;
					$trade_mod['end_time'] = time();
					$trade_mod['order_id'] = $order_id;	// 从订单表中得到最后交易状态
					Db::name('trade') -> where('id',$exist['id']) -> update($trade_mod);	// 历史交易人
					Db::name('trade') -> where('id',$last_trade_id) -> update($trade_mod);	// 当前交易人
					
					Db::commit();
				}catch(\Exception $e){
					Db::rollback();
				}
			}
			
			// 实际出售大于挂单数量
			if($number > $exist['number']){
				Db::startTrans();
				try{
					// 修改购买信息
			    	$this -> mod_sell_info($cur_id,$uid,$price,$exist['price'],$exist['number'],$exist['uid']);
			    	
			    	// 插入订单
					$order_id = Db::name('order') -> insertGetId($history_map);	// 历史交易人
					
					// 修改交易表状态
					$trade_mod['trade_status'] = 3;
					$trade_mod['end_time'] = time();
					$trade_mod['order_id'] = $order_id;
					Db::name('trade') -> where('id',$exist['id']) -> update($trade_mod);	// 历史交易人
					
					// 修改已交易的挂卖信息并将多出的部分再次挂卖
					$mod_trade['number'] = $number - $exist['number'];
					$mod_trade['number'] = sprintf('%.2f',$mod_trade['number']);
					Db::name('trade') -> where('id',$last_trade_id) -> update($mod_trade);
					$this -> suitable_trader_sell($last_trade_id,$uid,2,$cur_id,$price,$mod_trade['number']);
					
					Db::commit();
				}catch(\Exception $e){
					Db::rollback();
				}
			}
			
			// 实际出售小于挂单数量
			if($number < $exist['number']){
				Db::startTrans();
				try{
					// 计算手续费 修改买家交易金额
			    	$buy_where['uid'] = $exist['uid'];
			    	$buy_where['cur_id'] = $cur_id;
			    	$buy_num = sprintf('%.4f',$number - ($number * config('BUY_SERVICE_CHARGE')));
			    	Db::name('user_cur') -> where($buy_where) -> setInc('number',$buy_num);
			    	
			    	// 计算手续费 修改卖家交易金额
			    	$sell_where['uid'] = $uid;
			    	$sell_where['cur_id'] = 1;
			    	$service_charge = sprintf('%.4f',$exist['price'] * $number * config('BUY_SERVICE_CHARGE'));
			    	$buy_num = sprintf('%.4f',$exist['price'] * $number - $service_charge);
			    	Db::name('user_cur') -> where($sell_where) -> setInc('number',$buy_num);
			    	
			    	// 插入订单
					$history_map['trade_id'] = $last_trade_id;
			        $history_map['order_number'] = $number;
					$order_id = Db::name('order') -> insertGetId($history_map);	// 当前交易人
			    	
			    	// 修改交易表状态
			    	$trade_mod['trade_status'] = 3;
					$trade_mod['end_time'] = time();
					$trade_mod['order_id'] = $order_id;
					Db::name('trade') -> where('id',$last_trade_id) -> update($trade_mod);	// 当前交易人
					
					// 修改已交易的挂卖信息并将多出的部分再次挂卖
					$mod_trade['number'] = $exist['number'] - $number;
					$mod_trade['number'] = sprintf('%.2f',$mod_trade['number']);
					Db::name('trade') -> where('id',$exist['id']) -> update($mod_trade);
					
					Db::commit();
				}catch(\Exception $e){
					Db::rollback();
				}
			}
		}
	}
	
	// 修改出售信息
	protected function mod_sell_info($cur_id,$uid,$price,$exist_price,$exist_number,$exist_uid){
//  	// 判断是否需要返回给买家的差价
//		$back_where['uid'] = $exist_uid;
//		$back_where['cur_id'] = 1;
//		$back_num = sprintf('%.4f',($exist_price - $price) * $exist_number);
//		Db::name('user_cur') -> where($back_where) -> setInc('number',$back_num);
    	
    	// 计算手续费 修改买家交易金额
    	$buy_where['uid'] = $exist_uid;
    	$buy_where['cur_id'] = $cur_id;
    	$buy_num = sprintf('%.4f',$exist_number - ($exist_number * config('BUY_SERVICE_CHARGE')));
    	Db::name('user_cur') -> where($buy_where) -> setInc('number',$buy_num);
    	
    	// 计算手续费 修改卖家交易金额
    	$sell_where['uid'] = $uid;
    	$sell_where['cur_id'] = 1;
    	$buy_num = sprintf('%.4f',$exist_price * $exist_number * (1- config('BUY_SERVICE_CHARGE')));
//  	$buy_num = sprintf('%.4f',$price * $exist_number - $service_charge);
    	Db::name('user_cur') -> where($sell_where) -> setInc('number',$buy_num);
	}
	
	// 插入 币种行情统计表
	public function cur_market($id){
		// order 表查询条件
		$order_map['cur_id'] = $id;
		$order_map['order_status'] = 3;
		$order_map['trade_mold'] = 0;
		
		// 获取最新价
		$price_new = db('order')->where($order_map)->whereTime('done_time', 'today')->order('done_time DESC')->value('price');	// 最新价
		$data['price_new'] = sprintf('%.2f',$price_new);
		$data['price_new_cny'] = sprintf('%.2f',$price_new * config('EXCHANGE_RATE'));	// 最新价转换为人民币
		
		// 获取最高价
		$data['max_price'] = sprintf('%.2f',db('order')->where($order_map)->whereTime('done_time', 'today')->max('price'));
		$data['max_price_cny'] = sprintf('%.2f',$data['max_price'] * config('EXCHANGE_RATE'));
		
		// 获取最低价
		$data['min_price'] = sprintf('%.2f',db('order')->where($order_map)->whereTime('done_time', 'today')->min('price'));
		$data['min_price_cny'] = sprintf('%.2f',$data['min_price'] * config('EXCHANGE_RATE'));
		
		// 获取买一价
		$one_map['cur_id'] = $id;
		$one_map['trade_status'] = 1;
		$one_map['trade_type'] = 2;
		$one_map['trade_mold'] = 0;
		$data['buy_one'] = sprintf('%.2f',db('trade')->where($one_map)->order('price DESC')->value('price'));
		$data['buy_one_cny'] = sprintf('%.2f',$data['buy_one'] * config('EXCHANGE_RATE'));
		
		// 获取卖一价
		$one_map['trade_type'] = 1;
		$data['sell_one'] = sprintf('%.2f',db('trade')->where($one_map)->order('price ASC')->value('price'));
		$data['sell_one_cny'] = sprintf('%.2f',$data['sell_one'] * config('EXCHANGE_RATE'));
		
		// 日成交量
		$data['volume'] = Db::name('order') -> where($order_map) -> whereTime('done_time','today') -> sum('order_number');
		
		// 获取日涨跌
		$today_last_price = Db::name('order') -> where($order_map) -> whereTime('done_time','today') -> order('done_time DESC') -> value('price');	// 今天最后成交价格
		$yesterday_last_price = Db::name('order') -> where($order_map) -> whereTime('done_time','yesterday') -> order('done_time DESC') -> value('price');	// 昨天最后成交价格
		if($today_last_price && $yesterday_last_price){
			// ((今天最后成交价-昨天最后成交价)/昨天最后成交价)*100
			$data['day_rise_fall'] = sprintf('%.2f',(($today_last_price - $yesterday_last_price)/$yesterday_last_price)*100);
			// 判断日涨跌样式
			if(strstr($data['day_rise_fall'],'-') === false){
				$data['day_rise_fall_color'] = 'increase';
      		}else{
      			$data['day_rise_fall_color'] = 'lower';
      		}
		}else{
			$data['day_rise_fall'] = 0;
		}
		
		// 获取当天的开盘价(查询当天的 order 表第一条交易记录的价格)
		$today_open_price = Db::name('order') -> where($order_map) -> whereTime('done_time','today') -> order('done_time ASC') -> value('price');
		if($today_open_price){
			$data['open_price'] = $today_open_price;
		}else{
			$data['open_price'] = 0;
		}
		
		$data['create_time'] = time();
		$mod = Db::name('cur_market') -> where('cur_id',$id) -> update($data);
		if($mod){
			return true;
		}else{
			return false;
		}
	}
	
//	// 判断是否存在对应需求的交易
//	protected function suitable_trader($last_trade_id,$uid,$trade_type,$cur_id,$price,$number){
//		$exist_where['trade_type'] = $trade_type;
//		$exist_where['cur_id'] = $cur_id;
//		$exist_where['price'] = $price;
//		$exist_where['number'] = $number;
//		$exist_where['trade_status'] = 1;
//		$exist_where['uid'] = array('neq',$uid);
//		$exist = Db::name('trade') -> where($exist_where) -> order('start_time ASC') -> find();
//		if($exist){
//			Db::startTrans();
//			try{
//				// 插入订单
//				$history_map['trade_id'] = $exist['id'];
//				$history_map['order'] = generateOrderNumber();
//		        $history_map['order_number'] = $exist['number'];
//		        $history_map['price'] = $exist['price'];
//		        $history_map['order_status'] = 3;
//		        $history_map['addtime'] = time();
//		        $history_map['done_time'] = time();
//		        $history_map['trade_type'] = $exist['trade_type'];
//		        $history_map['cur_id'] = $exist['cur_id'];
//		        if($trade_type === 2){	// 买
//		        	$history_map['buyer_id'] = $exist['uid'];
//			    	$history_map['seller_id'] = $uid;
//			    	
//			    	// 计算手续费 修改用户交易金额
//			    	$buy_where['uid'] = $exist['uid'];
//			    	$buy_where['cur_id'] = 2;
//			    	$service_charge = $price * $number * config('BUY_SERVICE_CHARGE');
//			    	$buy_num = $exist['number'];
//			    	Db::name('user_cur') -> where($buy_where) -> setInc('number',$buy_num);
//			    	
//			    	$sell_where['uid'] = $uid;
//			    	$sell_where['cur_id'] = 1;
//			    	$service_charge = $price * $number * config('BUY_SERVICE_CHARGE');
//			    	$sell_num = $price * $number - $service_charge;
//			    	Db::name('user_cur') -> where($sell_where) -> setInc('number',$sell_num);
//		        }else{	// 卖
//		        	$history_map['buyer_id'] = $uid;
//			    	$history_map['seller_id'] = $exist['uid'];
//			    	
//			    	// 计算手续费 修改用户交易金额
//			    	$buy_where['uid'] = $uid;
//			    	$buy_where['cur_id'] = 2;
//			    	$service_charge = $price * $number * config('BUY_SERVICE_CHARGE');
//			    	$buy_num = ($price * $number - $service_charge)/$number;
//			    	Db::name('user_cur') -> where($buy_where) -> setInc('number',$buy_num);
//		        }
//				Db::name('order') -> insert($history_map);	// 历史交易人
//				
//				// 修改交易表状态
//				Db::name('trade') -> where('id',$exist['id']) -> update(array('trade_status' => 3));	// 历史交易人
//				Db::name('trade') -> where('id',$last_trade_id) -> update(array('trade_status' => 3));	// 当前交易人
//				
//				Db::commit();
//			}catch(\Exception $e){
//				Db::rollback();
//			}
//	        
//		}
//	}
	
    //挂买挂卖ETH
    public function buySellEth($data)
    {
        if(session('uid')){
        	$is_bind = Db::name('user') -> where('id',session('uid')) -> field('identity_front,identity_behind,bank_number,wechat_accout,alipay_accout') -> find();
        	if(!$is_bind['identity_front'] || !$is_bind['identity_behind'] || !$is_bind['bank_number'] || !$is_bind['wechat_accout'] || !$is_bind['alipay_accout']){
        		return ['status' => 0,'info' => '请先实名认证,并绑定您的银行卡、微信和支付宝账号','en_info' => 'Please first verify your real name and bind your bank card, WeChat and Alipay account.'];
        	}
            if(!$data['price'] || !$data['number']){
                return ['status'=>0, 'info'=>'不能为空','en_info' => 'Can not be empty'];
            }else{
                if($data['type'] == 1){
                    $map['uid'] = session('uid');
                    $map['cur_id'] = 1;
                    $cur_number = db('user_cur')->where($map)->value('number');
                    if($cur_number < $data['number']){
                        return ['status'=>0, 'info'=>'虚拟币不足','en_info' => 'Insufficient voice'];
                    }
                }
                $insert_data['uid'] = session('uid');
                $insert_data['number'] = $data['number'];
                $insert_data['price'] = $data['price'];
                $insert_data['start_time'] = time();
                $insert_data['trade_type'] = $data['type'];
                $insert_data['cur_id'] = 1;
                $insert_data['trade_mold'] = 1;
                db('trade')->insert($insert_data);
                if($data['type'] == 1){
                    $map['uid'] = session('uid');
                    $map['cur_id'] = 1;
                    db('user_cur')->where($map)->setDec('number',$data['number']);
                }
                return ['status'=>1, 'info'=>'成功','en_info' => 'Success'];
            }
        }else{
            return ['status'=>0, 'info'=>'请登录','en_info' => 'Please sign in'];
        }
    }

    //买入页面
    public function buyPage($cur_id)
    {
        $result['nav'] = db('currency')->field('id,name,icon')->select();
        $result['currencyinfo'] = model('Currency')->currencyInfo2($cur_id);
        $map['trade_type'] = 2;
        $map['trade_status'] = array('in','1,5');
        $map['cur_id'] = $cur_id;
        $map['trade_mold'] = 0;
        $result['trade'] = db('trade')->where($map)->select();
        foreach ($result['trade'] as $k => $v) {
          $result['trade'][$k]['username'] = db('user')->where('id',$v['uid'])->value('username');
        }
        return $result;
    }

    //卖出页面
    public function sellPage($cur_id)
    {
        $result['nav'] = db('currency')->field('id,name,icon')->select();
        $result['currencyinfo'] = model('Currency')->currencyInfo2($cur_id);
        $map['trade_type'] = 1;
        $map['trade_status'] = array('in','1,5');
        $map['cur_id'] = $cur_id;
        $map['trade_mold'] = 0;
        $result['trade'] = db('trade')->where($map)->select();
        foreach ($result['trade'] as $k => $v) {
          $result['trade'][$k]['username'] = db('user')->where('id',$v['uid'])->value('username');
        }
        return $result;
    }
	
	/**
	 * model 最新成交
	 */
	public function newDeal($cur_id){
		$order_where['order_status'] = 3;
		$order_where['trade_mold'] = 0;
		$order_where['cur_id'] = $cur_id;
		// 分组显示,只显示每一单中的实际成交价格(去掉交易中最后一笔成交数据)
		$list = Db::name('order') -> where($order_where) -> order('done_time DESC') -> limit(30) -> select();
		foreach($list as $k => $v){
			$list[$k]['done_date'] = date('m-d H:i:s',$v['done_time']);
			switch($v['trade_type']){
				case 1:
					$list[$k]['text_color'] = 'green_color';
					break;
				case 2:
					$list[$k]['text_color'] = 'red_color';
					break;
			}
		}
		return ['code' => 1,'data' => $list];
	}
	
	/**
	 * model 最新成交(区分求购/出售)
	 */
	public function newDeal2($cur_id){
		$order_where['order_status'] = 3;
		$order_where['trade_mold'] = 0;
		$order_where['cur_id'] = $cur_id;
		// 求购
		$order_where['trade_type'] = 2;
		$list_buy = Db::name('order') -> where($order_where) -> order('done_time DESC') -> limit(30) -> select();
		foreach($list_buy as $k => $v){
//			$list_buy[$k]['done_date'] = date('Y-m-d',$v['done_time']);
			$list_buy[$k]['text_color'] = 'red_color';
			$list_buy[$k]['all_price'] = sprintf('%.2f',$v['price'] * $v['order_number']);
		}
		// 出售
		$order_where['trade_type'] = 1;
		$list_sell = Db::name('order') -> where($order_where) -> order('done_time DESC') -> limit(30) -> select();
		foreach($list_sell as $k => $v){
//			$list_sell[$k]['done_date'] = date('Y-m-d',$v['done_time']);	// 日期格式
			$list_sell[$k]['text_color'] = 'red_color';	// 数字颜色(绿:出售 红:求购)
			$list_sell[$k]['all_price'] = sprintf('%.2f',$v['price'] * $v['order_number']);	// 折合USDT
		}
		$list['buy'] = $list_buy;
		$list['sell'] = $list_sell;
		return ['code' => 1,'data' => $list];
	}
	
	// 委托信息
	public function allTrade($cur_id){
		$list['buy'] = Db::name('trade') -> where('trade_status=1 AND trade_type=2 AND trade_mold=0 AND cur_id='.$cur_id) -> field('sum(number) as number,price,trade_type') -> order('price DESC') -> limit(5) -> group('price') -> select();
		foreach($list['buy'] as $k => $v){
			$list['buy'][$k]['trade_type_text'] = '买';
		}
		$list['sell'] = Db::name('trade') -> where('trade_status=1 AND trade_type=1 AND trade_mold=0 AND cur_id='.$cur_id) -> field('sum(number) as number,price,trade_type') -> order('price ASC') -> limit(5) -> group('price') -> select();
		$list['sell'] = array_reverse($list['sell']);	// 以相反的元素顺序返回数组
		$sell_count = Db::name('trade') -> where('trade_status=1 AND trade_type=1 AND trade_mold=0') -> limit(5) -> count();
		foreach($list['sell'] as $k => $v){
			$list['sell'][$k]['trade_type_text'] = '卖';
		}
		if($list){
			return ['status' => 1,'buy' => $list['buy'],'sell' => $list['sell'],'sell_count' => $sell_count];
		}else{
			return ['status' => 0];
		}
	}
	
    //我的委托
    public function entrustPage($cur_id)
    {
        $result['nav'] = db('currency')->field('id,name,icon')->select();
        $result['currencyinfo'] = model('Currency')->currencyInfo2($cur_id);
        $map['uid'] = session('uid');
        $map['trade_status'] = array('in','1,5');
        $map['cur_id'] = $cur_id;
        $map['trade_mold'] = 0;
        $result['trade'] = db('trade')->where($map)->select();
        foreach ($result['trade'] as $k => $v) {
          $result['trade'][$k]['username'] = db('user')->where('id',$v['uid'])->value('username');
        }
        return $result;
    }

    //我的成交
    public function dealPage($cur_id)
    {
        $complete_map['order_status'] = 2;
        $result['complete'] = db('order')->where($complete_map)->where('buyer_id|seller_id','=',session('uid'))->select();
        $no_complete_map['order_status'] = array('in','0,1');
        $result['no_complete'] = db('order')->where($no_complete_map)->where('buyer_id|seller_id','=',session('uid'))->select();
        $orderStatusArr = model('Common/Dict')->showkey('order_status');
        foreach ($result['no_complete'] as $k => $v) {
            $result['no_complete'][$k]['orderStatustext'] = $orderStatusArr[$v['order_status']];   
            if($v['seller_id'] == session('uid')){
                //卖币
                $buyerinfo = db('user')->where('id',$v['buyer_id'])->find();
                $result['no_complete'][$k]['buyer_name'] = $buyerinfo['username'];
                $result['no_complete'][$k]['buyer_tel'] = $buyerinfo['tel'];
            }else{
                //买币
                $result['no_complete'][$k]['sellerinfo'] = db('user')->where('id',$v['seller_id'])->find();
                $result['no_complete'][$k]['cur_name'] = db('currency')->where('id',$v['cur_id'])->value('name');
            }
        }
        return $result;
    }
    
    //已支付
    public function orderPayment($data)
    {
        $data['order_status'] = 1;
        $data['pay_time'] = time();
        db('order')->update($data);
        return ['status'=>1,'info'=>'成功','en_info' => 'Success'];

    }

    public function receive($data)
    {
        $orderinfo = db('order')->where('id',$data['id'])->find();
        $data['order_status'] = 2;
        $data['done_time'] = time();
        db('order')->update($data);
        db('trade')->where('id',$orderinfo['trade_id'])->update(array('trade_status'=>3));
        $relationship_map['uid'] = $orderinfo['buyer_id'];
        $relationship_map['cur_id'] = $orderinfo['cur_id'];
        db('relationship')->where($relationship_map)->setInc('number',$orderinfo['order_number']);
        return ['status'=>1,'info'=>'成功','en_info' => 'Success'];
    }

    public function transactionActive($data)
    {
        $tradeinfo = db('trade')->where('id',$data['id'])->find();
        if($tradeinfo['trade_status'] == 1){
            if($tradeinfo['trade_type'] == 1){
                return $this->buyactive($data);
            }else{
                return $this->sellactive($data);
            }
        }else{
            return ['status' => 0, 'info'=>'该委托不能交易','en_info' => 'The commission cannot be traded'];
        }

    }

    public function buyactive($data)
    {
        $tradeinfo = db('trade')->where('id',$data['id'])->find();
        $userinfo = db('user_cur')->where('uid',session('uid'))->where('cur_id',1)->find();
        if(session('uid')){
        	if(session('uid') !== $tradeinfo['uid']){
                $all_money = $tradeinfo['price'] * $tradeinfo['number'];
                if($userinfo['number'] < $all_money){
                    return ['status' => 0, 'info'=>'余额不足','en_info' => 'Insufficient balance'];
                }else{
                    $map['trade_status'] = 3;
                    $map['end_time'] = time();
                    db('trade')->where('id',$data['id'])->update($map);
                    db('user_cur')->where('uid',session('uid'))->where('cur_id',1)->setDec('number',$all_money);
                    $user_money = $tradeinfo['number']*(1-config('BUY_SERVICE_CHARGE'));
                    db('user_cur')->where('uid',session('uid'))->where('cur_id',$tradeinfo['cur_id'])->setInc('number',$user_money);
                    $trader_money = $all_money*(1-config('SELL_SERVICE_CHARGE'));
                    db('user_cur')->where('uid',$tradeinfo['uid'])->where('cur_id',1)->setInc('number',$trader_money);
                    model('Order')->createOrder($data,session('uid'),$tradeinfo['uid']);
                    return ['status' => 1, 'info'=>'成功','en_info' => 'Success'];
                }      
        	}else{
        		return ['status' => 0, 'info'=>'不能购买自己的挂单','en_info' => "Can't buy your own pending order"];

        	}
        }else{
             return ['status' => 0, 'info'=>'请登录','en_info' => 'Please sign in'];
        }

    }

    public function sellactive($data)
    {
        $tradeinfo = db('trade')->where('id',$data['id'])->find();
        $cur_map['uid'] = session('uid');
        $cur_map['cur_id'] = $tradeinfo['cur_id'];
        $user_cur = db('user_cur')->where($cur_map)->find();
        if(session('uid')){
        	if(session('uid') !== $tradeinfo['uid']){
                $all_money = $tradeinfo['price'] * $tradeinfo['number'];
                if($user_cur['number'] < $tradeinfo['number']){
                    return ['status' => 0, 'info'=>'余额不足','en_info' => 'Insufficient balance'];
                }else{
                    $map['trade_status'] = 3;
                    $map['end_time'] = time();
                    db('trade')->where('id',$data['id'])->update($map);
                    db('user_cur')->where($cur_map)->setDec('number',$tradeinfo['number']);
                    $user_money = $all_money*(1-config('SELL_SERVICE_CHARGE'));
                    db('user_cur')->where('uid',session('uid'))->where('cur_id',1)->setInc('number',$user_money);
                    $trader_money = $tradeinfo['number']*(1-config('BUY_SERVICE_CHARGE'));
                    db('user_cur')->where('uid',$tradeinfo['uid'])->where('cur_id',$tradeinfo['cur_id'])->setInc('number',$trader_money);
                    model('Order')->createOrder($data,$tradeinfo['uid'],session('uid'));
                    return ['status' => 1, 'info'=>'成功','en_info' => 'Success'];
                }    
        	}else{
        		return ['status' => 0, 'info'=>'不能卖给自己的挂单','en_info' => "Can't sell your own pending order"];
        	}
        }else{
             return ['status' => 0, 'info'=>'请登录','en_info' => 'Please sign in'];
        }

    }

    public function revoke($data)
    {
        $tradeinfo = db('trade')->where('id',$data['id'])->find();
        if($tradeinfo['trade_status'] == 1 || $tradeinfo['trade_status'] ==5){
            if($tradeinfo['uid'] == session('uid')){
                $trade_map['trade_status'] = 4;
                $trade_map['end_time'] = time();
                db('trade')->where('id',$data['id'])->update($trade_map);
                if($tradeinfo['trade_type'] == 1){
                    $cur_map['uid'] = $tradeinfo['uid'];
                    $cur_map['cur_id'] = $tradeinfo['cur_id'];
                    db('user_cur')->where($cur_map)->setInc('number',$tradeinfo['number']);
                }else{
                    $cur_map['uid'] = $tradeinfo['uid'];
                    $cur_map['cur_id'] = 1;
                    db('user_cur')->where($cur_map)->setInc('number',($tradeinfo['number']*$tradeinfo['price']));
                }
                return ['status' => 1, 'info'=>'撤销成功','en_info' => 'Successful cancellation'];
            }else{
                return ['status' => 0, 'info'=>'该委托不能撤销','en_info' => 'The commission cannot be revoked'];
            }
        }else{
            return ['status' => 0, 'info'=>'该委托不能撤销','en_info' => 'The commission cannot be revoked'];
        }
    }

        //获取虚拟币行情信息
    public function getCurData()
    {
        $cur_map['id'] = ['neq',1];
        $currency_area = db('currency')->where($cur_map)->select();
        foreach ($currency_area as $k => $v) {
                // trade 表查询条件
                $map['cur_id'] = $v['id'];
                $map['trade_status'] = 3;
                $map['trade_mold'] = 0;
                // order 表查询条件
		        $order_map['cur_id'] = $v['id'];
		        $order_map['order_status'] = 3;
		        $order_map['trade_mold'] = 0;
		        // 获取最新价
                $currency_area[$k]['price_usd'] = db('order')->where($order_map)->order('done_time desc')->value('price');	// 最新交易价格
                $today_close_price = db('trade')->where($map)->whereTime('end_time', 'today')->order('end_time asc')->value('price');
                $currency_area[$k]['today_close_price'] = $today_close_price;
                $yesterday_close_price = db('kline')->where('cur_id',$v['id'])->whereTime('time','yesterday')->value('close_price');
                $currency_area[$k]['yesterday_close_price'] = $yesterday_close_price;
                $currency_area[$k]['high'] = db('trade')->where($map)->whereTime('end_time', 'today')->max('price');
                $currency_area[$k]['low'] = db('trade')->where($map)->whereTime('end_time', 'today')->min('price');
                // 24H成效额
//              $all_number = db('order')->where($order_map)->whereTime('done_time', 'today')->sum('order_number');	// 查询订单表中今天总交易量
//              $price = db('order')->where($order_map)->whereTime('done_time', 'today')->avg('price');	// 查询订单表中今天成交的平均价
//              $currency_area[$k]['24vol'] = sprintf('%.4f',$all_number * $price);	// 计算24H成交额
                $order_24H = Db::name('order') -> where($order_map) -> whereTime('done_time','today') -> field('order_number,price') -> select();
                $vol24 = '';
                foreach($order_24H as $order_k => $order_v){
                	$vol24 += $order_v['order_number'] * $order_v['price'];
                }
                $currency_area[$k]['24vol'] = sprintf('%.4f',$vol24);	// 计算24H成交额
                // 24小时成交量
                $order_where['order_status'] = 3;
		  		$order_where['trade_mold'] = 0;
		  		$order_where['cur_id'] = $v['id'];
                $currency_area[$k]['percent_change_24h'] = Db::name('order') -> where($order_where) -> whereTime('done_time','today') -> sum('order_number');
                
//				// 判断是否设置涨跌幅
//              if(empty($currency_area[$k]['rise_fall'])){
//              	if($yesterday_close_price != 0){
//	                   $currency_area[$k]['percent_change_24h'] = ($today_close_price - $yesterday_close_price)/$yesterday_close_price*100; 
//	                }
//              }else{
//              	$currency_area[$k]['percent_change_24h'] = $currency_area[$k]['rise_fall'];
//              }
//              if(!$currency_area[$k]['percent_change_24h']){
//                  $currency_area[$k]['percent_change_24h'] = 0;
//              }else{
//                  $currency_area[$k]['percent_change_24h'] = round($currency_area[$k]['percent_change_24h'],4);
//              }
				// 日涨跌
                $currency_area[$k]['day_rise_fall'] = model('Currency') -> currencyInfo2($v['id']);
                
                if(!$currency_area[$k]['price_usd']){
                    $currency_area[$k]['price_usd'] = 0;
                }
        }
//  pre($currency_area);exit;
        return $currency_area;
    }
    
//  // 匹配交易成功后生成K线图
//  // $last_trade_id 最后的交易记录id 
//  // $cur_id 币种ID
//  public function kLineGraph($last_trade_id,$cur_id){
//		// 通过最后插入的交易ID查询 order 表并获取最后匹配所成交的订单数量
//      $order = Db::name('order') -> where('trade_id',$last_trade_id) -> field('id') -> select();
//      $order_id = '';
//      foreach($order as $k => $v){
//      	$order_id .= $v['id'].',';
//      }
//      $order_id = trim($order_id,',');	// 获取最后一笔交易所成交的订单条数ID
//      
//      // 通过 order_id 在交易表中获取最后成效数据
//      $map['order_id'] = array('in',$order_id);
//      $trade_list = Db::name('trade') -> where($map) -> select();
//      foreach($trade_list as $k => $v){
//      	// 获取今天交易的最后一单记录
//          $open_trade = db('trade')->where('trade_status',3)->where('cur_id',$cur_id)->whereTime('end_time', 'today')->order('end_time desc')->find();
//          if($open_trade){
//              // 今天的开盘价
//              $trade_where['trade_status'] = 3;
//              $trade_where['trade_mold'] = 0;
//              $trade_where['cur_id'] = $cur_id;
//              $today_open_price = Db::name('trade') -> where($trade_where) -> whereTime('end_time','today') -> order('end_time ASC') -> value('open_price');
//              if($today_open_price == 0){
//              	$data['open_price'] = 0;
//              }else{
//              	$data['open_price'] = $today_open_price;
//              }
//              
//              // 今天的收盘价
//              $data['close_price'] = $v['price'];
//              
//              // 今天交易最高价
//              $data['max_price'] = db('trade')->where($trade_where)->whereTime('end_time', 'today')->max('price');
//              
//              // 今天交易最低价
//              $data['min_price'] = db('trade')->where($trade_where)->whereTime('end_time', 'today')->min('price');
//              // 今天交易的每分钟总交易量
////              $avg = db('trade')->where('trade_status',3)->where('cur_id',$cur_id)->whereTime('end_time', 'between', [(time()-60), time()])->avg('price');
////              $data['vol'] = $avg * $count;
//              $count = db('trade')->where('trade_status',3)->where('cur_id',$cur_id)->whereTime('end_time', 'between', [(time()-60), time()])->sum('number');
//				$data['vol'] = $count;
//				
//              // K线图时间
//              $data['time'] = time();
//              
//              // 交易币种
//              $data['cur_id'] = $cur_id;
//              db('kline') -> insert($data);	// 将最后成交的数据插入K线图数据表中
//          }
//         
//      }
//      
//  }
   


}
