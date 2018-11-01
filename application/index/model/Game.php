<?php
namespace app\index\model;

use app\common\model\Base;
use think\Request;
use think\Db;

class Game extends Base
{
	/**
	 * model 游戏中心首页
	 */
	public function gameInfo($uid){
		// 获取游戏信息
		$info = Db::name('game') -> where('end_time',null) -> field('id,term_num,seed_issue,sell_seed,bonus,weighting') -> find();
		$info['percent'] = sprintf('%.2f',$info['sell_seed'] / 1000000 * 100);	// 查看游戏进度(%)
		$return['info'] = $info;
		
		// 获取用户游戏信息
		if($uid){
			$user_where['uid'] = $uid;
			$user_where['gid'] = $info['id'];
			// 我的排名
//			$user_group = Db::name('game_record') -> where('gid',$info['id']) -> field('uid') -> group('uid') -> order('sum(seed_total) DESC') -> select();
//			foreach($user_group as $k => $v){
//				if($uid === $v['uid']){
//					$user['rank'] = $k + 1;
//				}
//			}
			$user['rank'] = Db::name('game_record') -> where($user_where) -> order('create_time DESC') -> value('seed_num_end');
			
			// 我的分红奖
			$user['my_bonus'] = Db::name('user_game_statistic') -> where($user_where) -> value('bonus');
			
			// 我的种子数量
			$user_where['game_status'] = 1;
			$user['seed'] = Db::name('game_record') -> where($user_where) -> sum('seed_total');
			
			$return['user'] = $user;
		}
		
		// 游戏首页右上滚动信息
    	$gid = Db::name('game') -> where('end_time',null) -> value('id');	// 获取最新游戏ID
    	$game_record = Db::name('game_record') -> where('gid',$gid) -> field('uid,create_time') -> order('create_time DESC') -> limit(5) -> select();
    	foreach($game_record as $k => $v){
    		$account = Db::name('user') -> where('id',$v['uid']) -> value('account');
    		$now = time();
    		$minutes = sprintf('%.0f',($now - $v['create_time']) / 60);
    		$game_record[$k]['text'] = $account.' '.$minutes.'分钟前参与了游戏';
    		$game_record[$k]['en_text'] = $account.' participated in the game '.$minutes.' minutes ago';
    	}
    	$return['game_record'] = $game_record;
		
		// 下方游戏介绍
		$introduction = Db::name('page') -> where('id=5') -> field('name,en_name,content,en_content') -> find();
		$return['introduction'] = $introduction;
		
		return $return;
	}
	
	/**
	 * model 获取是否有正在游戏进行
	 */
	public function haveGame(){
		$have = Db::name('game') -> where('end_time',null) -> find();
		if($have){
			return ['code' => 1];
		}else{
			return ['code' => 0,'msg' => '暂无游戏!','en_msg' => 'No games yet!'];
		}
	}
	
	/**
	 * model 获取种子数量并计算所需要的ETH数量
	 */
	public function seedEtc($gid,$seed_total){
		if(!$gid){
			return ['code' => 0,'msg' => '未获取游戏信息!','en_msg' => 'Game information not acquired!'];
		}
		if(!$seed_total){
			return ['code' => 0,'msg' => '未获取种子数量!','en_msg' => 'Number of seeds not acquired!'];
		}
		$where['id'] = $gid;
		$where['end_time'] = null;
		$equal = Db::name('game') -> where($where) -> field('difference_price,difference_price_times') -> find();
		$price = $seed_total * $equal['difference_price'] * $equal['difference_price_times'];
		$price = sprintf('%.4f',$price);
		if($price){
			return ['code' => 1,'data' => $price];
		}else{
			return ['code' => 0,'msg' => '获取需要支付ETH失败!','en_msg' => 'Get the need to pay for ETH failure!'];
		}
	}
	
	/**
	 * model 点击购买种子
	 */
	public function seedBuy($data){
		
		// 游戏期号
		if(!$data['gid']){
			return ['code' => 0,'msg' => '未获取游戏信息!','en_msg' => 'Game information not acquired!'];
		}
		// 用户ID
		if(!$data['uid']){
			return ['code' => 0,'msg' => '请先登陆!','en_msg' => 'Log in first, please!','url' => url('publics/login')];
		}
		// 种子数量
		if(!$data['seed_total']){
			return ['code' => 0,'msg' => '请输入种子数量!','en_msg' => 'Please enter the number of seeds!'];
		}else{
			if($data['seed_total'] > 100001){
				return ['code' => 0,'msg' => '单次购买最大不能超过10万!','en_msg' => 'A single purchase can not exceed 100,000!'];
			}
			if(!is_numeric($data['seed_total']) || strpos($data['seed_total'],".") !== false){
				return ['code' => 0,'msg' => '种子数量只能输入整数!','en_msg' => 'The number of seeds can only be entered as an integer!'];
			}
			// 判断游戏结束还有多少种子
			$sold_seed = Db::name('game') -> where('id',$data['gid']) -> value('sell_seed');
			$can_sell = 1000000 - $sold_seed;
			if($data['seed_total'] > $can_sell){
				return ['code' => 0,'msg' => '本期游戏只剩下'.$can_sell.'个种子了!'];
			}
		}
		// 需要支付的ETH
		if(!$data['price']){
			return ['code' => 0,'msg' => '未获取需要支付ETH!','en_msg' => 'Not acquired need to pay ETH!'];
		}
		// 交易密码
		if(!$data['payment_password']){
			return ['code' => 0,'msg' => '请输入您的交易密码!','en_msg' => 'Please enter your transaction password!'];
		}else{
			$payment_password = encrypt($data['payment_password']);
			$correct = Db::name('user') -> where('payment_password',$payment_password) -> field('payment_password') -> find();
			if(!$correct){
				return ['code' => 0,'msg' => '交易密码错误!','en_msg' => 'Transaction password is wrong!'];
			}
			// 用户ETH余额是否足够
			$user_cur_where['uid'] = $data['uid'];
			$user_cur_where['cur_id'] = 1;
			$is_enough = Db::name('user_cur') -> where($user_cur_where) -> value('number');
			if($is_enough < $data['price']){
				return ['code' => 0,'msg' => '您的ETH余额不足!','en_msg' => 'Your ETH balance is insufficient!'];
			}
		}
		
		// 购买种子
		Db::startTrans();
		$condition = 0;
		try{
			unset($data['payment_password']);	// 释放支付密码
			
			//----- 得到用户购买种子号码
			$game = Db::name('game') -> where('id',$data['gid']) -> field('term_num,sell_seed,prize_num,set_prize_uid') -> find();	// 获取本期游戏信息
			$start = $game['sell_seed'];	// 已卖出的种子数
			$seed_num_start = $start + 1;	// 本次购买种子起始号码
			Db::name('game') -> where('id',$data['gid']) -> setInc('sell_seed',$data['seed_total']);	// 添加本期累计购买种子数量
			$seed_num_end = Db::name('game') -> where('id',$data['gid']) -> value('sell_seed');	// 本次购买种子结尾号码
			
			//----- 判断后台设置的特殊用户ID和特殊种子号
			$special_seed = $game['prize_num'];	// 获取特殊种子号
			$special_uid = $game['set_prize_uid'];	// 获取特殊用户ID
			$info['uid'] = $data['uid'];
			$info['special_uid'] = $special_uid;
			
			// 判断当前用户购买的种子中是否存在特殊种子(或在购买区间,或等于起始号码,或等于结尾号码)
			if(($special_seed > $seed_num_start && $special_seed < $seed_num_end) || $special_seed == $seed_num_start || $special_seed == $seed_num_end){
				// 判断当前购买用户是否为特殊用户
				if($data['uid'] != $special_uid){
					// 判断非特殊用户是否只买一个种子,而且此种子为特殊种子
					if($data['seed_total'] == 1){
						$seed_num_start = $seed_num_start + 1;
						$seed_num_end = $seed_num_end + 1;
						$this -> insert_record($seed_num_start,$seed_num_end,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
					}else{
						if($special_seed == $seed_num_start){	// 判断非特殊用户购买的第一个种子为特殊种子时
							$seed_num_start = $seed_num_start + 1;
							$seed_num_end = $seed_num_end + 1;
							$this -> insert_record($seed_num_start,$seed_num_end,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
							Db::name('game') -> where('id',$data['gid']) -> setInc('sell_seed');	// 把空出来的特殊种子 + 1
						}else if($special_seed == $seed_num_end){	// 判断非特殊用户购买的最后一个种子为特殊种子时
							$seed_num_end = $seed_num_end - 1;
							$this -> insert_record($seed_num_start,$seed_num_end,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
							$seed_num_end = $seed_num_end + 2;
							$this -> insert_record($seed_num_end,$seed_num_end,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
							Db::name('game') -> where('id',$data['gid']) -> setInc('sell_seed');	// 把空出来的特殊种子 + 1
						}else{	// 判断非特殊用户购买的区间中存在特殊种子时
							$special_before = $special_seed - 1;
							$special_after = $special_seed + 1;
							$seed_num_end = $seed_num_end + 1;
							// 执行插入两条数据(并增加一个种子补齐数据)
							$this -> insert_record($seed_num_start,$special_before,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
							$this -> insert_record($special_after,$seed_num_end,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
							Db::name('game') -> where('id',$data['gid']) -> setInc('sell_seed');	// 把空出来的特殊种子 + 1
						}
					}
				}else{
					// 判断特殊用户是否只买一个种子
					if($data['seed_total'] == 1){
						$this -> insert_record($special_seed,$special_seed,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
					}else{
						$seed_num_end = $seed_num_end - 1;
						// 执行插入两条数据(并减少一个种子来补充特殊种)
						$this -> insert_record($special_seed,$special_seed,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
						$this -> insert_record($seed_num_start,$seed_num_end,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
					}
				}
			}else{
				if($data['uid'] != $special_uid){
					$this -> insert_record($seed_num_start,$seed_num_end,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
				}else{
					// 判断非特殊用户是否只买一个种子,而且此种子为特殊种子
					if($data['seed_total'] == 1){
						$this -> insert_record($special_seed,$special_seed,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
					}else{
						$seed_num_end = $seed_num_end - 1;
						// 执行插入两条数据(并减少一个种子来补充特殊种)
						$this -> insert_record($special_seed,$special_seed,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
						$this -> insert_record($seed_num_start,$seed_num_end,$data['gid'],$data['uid'],$data['price'],$data['seed_total']);
					}
				}
			}
			
			//----- 增加种子购买价格
			Db::name('game') -> where('id',$data['gid']) -> setInc('difference_price_times');	// 默认陪数+1
			
			//----- 增加奖池大奖奖金 30%
			$bonus = $data['price'] * 0.3;
			Db::name('game') -> where('id',$data['gid']) -> setInc('bonus',$bonus);
			
			//----- 增加加权 9%
			if($data['price'] >= 2){
				$weighting = $data['price'] * 0.09;
				Db::name('game') -> where('id',$data['gid']) -> setInc('weighting',$weighting);
			}
			
			//----- 在 游戏统计表中 修改当前用户购买种子数据
			$user_game_statistic_where['gid'] = $data['gid'];
			$user_game_statistic_where['uid'] = $data['uid'];
			$join_game = Db::name('user_game_statistic') -> where($user_game_statistic_where) -> field('uid,seed,eth,loss,update_time') -> find();
			if(!$join_game){
				$user_game_statistic_in['gid'] = $data['gid'];
				$user_game_statistic_in['uid'] = $data['uid'];
				$user_game_statistic_in['seed'] = $data['seed_total'];
				$user_game_statistic_in['eth'] = $data['price'];
				$user_game_statistic_in['loss'] = '-'.$data['price'];
				$user_game_statistic_in['update_time'] = time();
				Db::name('user_game_statistic') -> insert($user_game_statistic_in);
			}else{
				$user_game_statistic_mod['seed'] = $join_game['seed'] + $data['seed_total'];
				$user_game_statistic_mod['eth'] = $join_game['eth'] + $data['price'];
				$user_game_statistic_mod['loss'] = $join_game['etch'] + ('-'.$data['price']);
				$user_game_statistic_mod['update_time'] = time();
				Db::name('user_game_statistic') -> where($user_game_statistic_where) -> update($user_game_statistic_mod);
			}
			
			//----- 判断是否为推荐用户
			$pid = Db::name('user') -> where('id',$data['uid']) -> value('parent_id');
			if($pid){
				// 为上级用户分红 9%
				$one_bonus = sprintf('%.2f',$data['price'] * 0.09);
				$one_user = Db::name('user') -> where('id',$pid) -> field('id,parent_id') -> find();
				$one_user_cur_where['uid'] = $one_user['id'];
				$one_user_cur_where['cur_id'] = 1;
				Db::name('user_cur') -> where($one_user_cur_where) -> setInc('number',$one_bonus);
				$this -> mod_user_game($data['gid'],$one_user['id'],$one_bonus);	// 修改 用户游戏统计表 中的奖金和亏损值
				$this -> add_bonus_msg($one_user['id'],$one_bonus);	// 游戏分红奖励
				if($one_user['parent_id']){
					// 为上上组用户分红 1%
					$two_bonus = sprintf('%.2f',$data['price'] * 0.01);
					$two_user_cur_where['uid'] = $one_user['parent_id'];
					$two_user_cur_where['cur_id'] = 1;
					Db::name('user_cur') -> where($two_user_cur_where) -> setInc('number',$two_bonus);
					$this -> mod_user_game($data['gid'],$one_user['parent_id'],$two_bonus);	// 修改 用户游戏统计表 中的奖金和亏损值
					$this -> add_bonus_msg($one_user['parent_id'],$two_bonus);	// 游戏分红奖励
				}
			}
			
			//----- 游戏分红奖励 46%
			$users = Db::name('game_record') -> where('gid',$data['gid']) -> field('uid') -> group('uid') -> select();
			foreach($users as $k => $v){
				// 获取游戏期号
				$users[$k]['term_num'] = $game['term_num'];
				// 查询每位参与本期游戏用户购买种子总量(未出局)
				$users_where['gid'] = $data['gid'];
				$users_where['uid'] = $v['uid'];
				$users_where['game_status'] = 1;
				$users[$k]['seed'] = Db::name('game_record') -> where($users_where) -> sum('seed_total');
				// 计算给每位用户分红
				if(($data['seed_total'] - 1) != 0){
					$price = $data['price'] - ($data['price'] / $data['seed_total']);
					$all_users_bonus = $price * 0.46;	// 计算所有种子的分红总和
					$sell_seed = Db::name('game') -> where('id',$data['gid']) -> value('sell_seed');	// 计算已售出种子数量
					$every_seed_bonus = $all_users_bonus / $sell_seed;	// 计算出每一个种子可得到的分红
					$users[$k]['seed_bonus'] = sprintf('%.4f',$users[$k]['seed'] * $every_seed_bonus);	// 计算每位用户可得到的购买种子的分红,并保留两位小数
					// 存入每位用户账号
					$users_cur_where['uid'] = $v['uid'];
					$users_cur_where['cur_id'] = 1;
					Db::name('user_cur') -> where($users_cur_where) -> setInc('number',$users[$k]['seed_bonus']);
					// 修改 用户游戏统计表 数据中的 用户已获得的奖金和亏损值
					$this -> mod_user_game($data['gid'],$v['uid'],$users[$k]['seed_bonus']);
					// 为每位参与游戏用户在系统消息表中生成用户购买分红奖励信息
					if($users[$k]["seed_bonus"] > 0){
						$msg_in['uid'] = $v['uid'];
						$msg_in['title'] = '【个人信息】用户参与游戏份红奖励';
						$msg_in['en_title'] = '【Personal information】User participation in the game bonus award';
						$msg_in['content'] = '尊敬的用户：<p>您好，您参与的'.$users[$k]["term_num"].'期游戏。有用户成功购买种子，您获得游戏分红奖励USDT'.$users[$k]["seed_bonus"].'个，已存入您的账户。</p>';
						$msg_in['en_content'] = 'Respected user：<p>Hello, the'.$users[$k]["term_num"].'game you participated in. If a user successfully purchases a seed, you will receive a game bonus bonus of USDT'.$users[$k]["seed_bonus"].'which has been deposited into your account.</p>';
						$msg_in['create_time'] = time();
						$msg_in['type'] = 5;
						Db::name('msg') -> insert($msg_in);
					}
				}
			}
			
			//----- 减去用户相应的 ETH 数量
			$user_cur_where['uid'] = $data['uid'];
			$user_cur_where['cur_id'] = 1;
			Db::name('user_cur') -> where($user_cur_where) -> setDec('number',$data['price']);
			
			Db::commit();
			$condition = 1;
		}catch(\Exception $e){
			Db::rollback();
		}
		
		if($condition === 1){
			return ['code' => 1,'msg' => '参与游戏成功!','en_msg' => 'Participate in the game successfully!'];
		}else{
			return ['code' => 0,'msg' => '参与游戏失败!','en_msg' => 'Participation in the game failed!'];
		}
	}
	
	// 存入用户购买种子记录
	public function insert_record($seed_num_start,$seed_num_end,$gid,$uid,$price,$seed_total){
		
		// 判断用户购买的种子数是否为 10万 20万 30万 40万 50万 60万 70万 ,如果是的话则为两条或多条数据,以便于通计出局种子
		if($seed_num_start < 100001 && $seed_num_end > 100000){	
			$this -> check_buy_seed($seed_num_start,100001,$seed_num_end,100000,$gid,$uid,$price,$seed_total);
		}else if($seed_num_start < 200001 && $seed_num_end > 200000){
			$this -> check_buy_seed($seed_num_start,200001,$seed_num_end,200000,$gid,$uid,$price,$seed_total);
		}else if($seed_num_start < 300001 && $seed_num_end > 300000){
			$this -> check_buy_seed($seed_num_start,300001,$seed_num_end,300000,$gid,$uid,$price,$seed_total);
		}else if($seed_num_start < 400001 && $seed_num_end > 400000){
			$this -> check_buy_seed($seed_num_start,400001,$seed_num_end,400000,$gid,$uid,$price,$seed_total);
		}else if($seed_num_start < 500001 && $seed_num_end > 500000){
			$this -> check_buy_seed($seed_num_start,500001,$seed_num_end,500000,$gid,$uid,$price,$seed_total);
		}else if($seed_num_start < 600001 && $seed_num_end > 600000){
			$this -> check_buy_seed($seed_num_start,600001,$seed_num_end,600000,$gid,$uid,$price,$seed_total);
		}else if($seed_num_start < 700001 && $seed_num_end > 700000){
			$this -> check_buy_seed($seed_num_start,700001,$seed_num_end,700000,$gid,$uid,$price,$seed_total);
		}else{
			//----- 存入用户购买种子记录
			$data['gid'] = $gid;
			$data['uid'] = $uid;
			$data['price'] = $price;
			$data['seed_total'] = $seed_total;
			$data['seed_num_start'] = $seed_num_start;
			$data['seed_num_end'] = $seed_num_end;
			$data['create_time'] = time();
			
			$record_last_id = Db::name('game_record') -> insertGetId($data);
			$this -> check_out_seed($record_last_id,$gid);
		}
	}
	
	// 判断用户种子是否购买到分界点
	public function check_buy_seed($seed_num_start,$boundary1,$seed_num_end,$boundary,$gid,$uid,$price,$seed_total){
		$data['gid'] = $gid;
		$data['uid'] = $uid;
		$data['price'] = $price;
		$data['seed_total'] = $seed_total;
		$data['create_time'] = time();
		
		// 生成插入分界点前数据
		$seed_num_first_start = $seed_num_start;
		$seed_num_first_end = $boundary;
		$data['seed_num_start'] = $seed_num_first_start;
		$data['seed_num_end'] = $seed_num_first_end;
		$record_last_id = Db::name('game_record') -> insertGetId($data);
		$this -> check_out_seed($record_last_id,$gid);
		
		// 生成插入分界点后数据
		$seed_num_second_start = $boundary1;
		$seed_num_second_end = $seed_num_end;
		$data['seed_num_start'] = $seed_num_second_start;
		$data['seed_num_end'] = $seed_num_second_end;
		$record_last_id = Db::name('game_record') -> insertGetId($data);
		$this -> check_out_seed($record_last_id,$gid);
	}
	
	
	// 修改 用户游戏统计表 中的奖金和亏损值
	public function mod_user_game($gid,$uid,$bonus){
		$where['gid'] = $gid;
		$where['uid'] = $uid;
		$user_game = Db::name('user_game_statistic') -> where($where) -> field('bonus,loss') -> find();
		$user_game_mod['bonus'] = $user_game['bonus'] + $bonus;
		$user_game_mod['loss'] = $user_game['loss'] + $bonus;
		$user_game_mod['update_time'] = time();
		Db::name('user_game_statistic') -> where($where) -> update($user_game_mod);
	}
	
	// 添加分红信息
	public function add_bonus_msg($uid,$bonus){
		// 在系统消息表中生成邀请分红信息
		$msg_in['uid'] = $uid;
		$msg_in['title'] = '【个人信息】游戏邀约份红奖励';
		$msg_in['en_title'] = '【Personal information】Game Invitation Bonus Reward';
		$msg_in['content'] = '尊敬的用户：<p>您好，您的下级用户成功参与本期游戏。您作为邀请人获得游戏分红奖励USDT'.$bonus.'个，已存入您的账户。</p>';
		$msg_in['en_content'] = 'Respected user：<p>Hello, your subordinate users have successfully participated in this game. You are the inviting person to get the game bonus bonus USDT'.$bonus.', which has been deposited into your account.</p>';
		$msg_in['create_time'] = time();
		$msg_in['type'] = 6;
		Db::name('msg') -> insert($msg_in);
		return;
	}
	
	// 判断游戏出局种子号
	public function check_out_seed($id,$gid){
		$seed_num = Db::name('game_record') -> where('id',$id) -> value('seed_num_end');
		if($seed_num >= 1000000){
			$this -> out_game($gid,1000000);
		}else if($seed_num >= 900001){
			$this -> out_game($gid,700000);
		}else if($seed_num >= 800001){
			$this -> out_game($gid,600000);
		}else if($seed_num >= 700001){
			$this -> out_game($gid,500000);
		}else if($seed_num >= 600001){
			$this -> out_game($gid,400000);
		}else if($seed_num >= 500001){
			$this -> out_game($gid,300000);
		}else if($seed_num >= 400001){
			$this -> out_game($gid,200000);
		}else if($seed_num >= 300001){
			$this -> out_game($gid,100000);
		}
	}
	
	// 设置出局&游戏结束
	public function out_game($gid,$rule){
		if($rule > 999999){
			//-- 修改 用户报名游戏记录表 
			$mod['game_status'] = 2;
			Db::name('game_record') -> where('gid = '.$gid.' AND seed_num_end <= '.$rule) -> update($mod);
			
			//--获取游戏数据中奖数据
			$game_where['id'] = $gid;
			$game_info = Db::name('game') -> where($game_where) -> field('term_num,prize_num,bonus,weighting') -> find();
			
			//--获取用户中奖ID
			$prize_uid = Db::name('game_record') -> where('seed_num_start = '.$game_info['prize_num'].' OR seed_num_end = '.$game_info['prize_num']) -> value('uid');
			if(!$prize_uid){
				$prize_uid = Db::name('game_record') -> where('seed_num_start < '.$game_info['prize_num'].' AND seed_num_end > '.$game_info['prize_num']) -> value('uid');
			}
			$game_mod['prize_uid'] = $prize_uid;
			$game_mod['end_time'] = time();
			Db::name('game') -> where($game_where) -> update($game_mod);	// 修改 游戏列表 中奖用户ID和游戏结束时间
			
			//-- 将大奖存入中奖用户账户中 30%
			$user_cur_where['uid'] = $prize_uid;
			$user_cur_where['cur_id'] = 1;
			Db::name('user_cur') -> where($user_cur_where) -> setInc('number',$game_info['bonus']);
			
			//-- 在 游戏统计表中 修改中大奖用户奖金和亏损值
			$user_game['gid'] = $gid;
			$user_game['uid'] = $prize_uid;
			$game_winner = Db::name('user_game_statistic') -> where($user_game) -> field('bonus,loss,is_winner,update_time') -> find();
			$winner_mod['bonus'] = $game_winner['bonus'] + $game_info['bonus'];
			$winner_mod['loss'] = $game_winner['loss'] + $game_info['bonus'];
			$winner_mod['is_winner'] = 1;
			$winner_mod['update_time'] = time();
			Db::name('user_game_statistic') -> where($user_game) -> update($winner_mod);
			
			//-- 添加中奖信息
			$msg_in['uid'] = $prize_uid;
			$msg_in['title'] = '【个人信息】游戏中大奖!!!';
			$msg_in['en_title'] = '【Personal information】Grand prize in the game!!!';
			$msg_in['content'] = '尊敬的用户：<p>您好，恭喜您获得'.$game_info['term_num'].'期游戏大奖共USDT'.$game_info['bonus'].'个，已存入您的账户。</p>';
			$msg_in['en_content'] = 'Respected user：<p>Hello, Congratulations, you have won '.$game_info['term_num'].' games game prizes total USDT'.$game_info['bonus'].', which have been deposited into your account.</p>';
			$msg_in['create_time'] = time();
			$msg_in['type'] = 7;
			Db::name('msg') -> insert($msg_in);
			
			//-- 为所有参与游戏用户分红 9%
			$every_weighting = $game_info['weighting'] / 1000000;
			$game_user = Db::name('user_game_statistic') -> where('gid',$gid) -> field('uid,seed') -> select();
			foreach($game_user as $k => $v){
				$game_user[$k]['weighting'] = sprintf('%.4f',$v['seed'] * $every_weighting);
				$user_cur_where['uid'] = $v['uid'];
				$user_cur_where['cur_id'] = 1;
				// 写入每一个参与游戏的用户
				Db::name('user_cur') -> where($user_cur_where) -> setInc('number',$game_user[$k]['weighting']);
				// 在'用户游戏统计表'中修改所获得的加权信息
				$game_statistic_where['gid'] = $gid;
				$game_statistic_where['uid'] = $v['uid'];
				$game_statistic_mod['get_weighted'] = $game_user[$k]['weighting'];
				Db::name('user_game_statistic') -> where($game_statistic_where) -> update($game_statistic_mod);
				
				// 发送分配系统信息
				$msg_in['uid'] = $v['uid'];
				$msg_in['title'] = '【个人信息】用户参与游戏份红奖励';
				$msg_in['en_title'] = '【Personal information】User participation in the game bonus award';
				$msg_in['content'] = '尊敬的用户：<p>您好，您参与的'.$game_info['term_num'].'期游戏已结束。您获得游戏分红奖励USDT'.$game_user[$k]['weighting'].'个，已存入您的账户。</p>';
				$msg_in['en_content'] = 'Respected user：<p>Hello, the '.$game_info['term_num'].' games you participated in are over. You have earned a game bonus of USDT'.$game_user[$k]['weighting'].', which has been deposited into your account.</p>';
				$msg_in['create_time'] = time();
				$msg_in['type'] = 5;
				Db::name('msg') -> insert($msg_in);
			}
		}else{
			$mod['game_status'] = 2;
			$exist = Db::name('game_record') -> where('gid = '.$gid.' AND seed_num_end <= '.$rule) -> find();
			if($exist){
				Db::name('game_record') -> where('gid = '.$gid.' AND seed_num_end <= '.$rule) -> update($mod);
			}
		}
		return;
	}
	
}
