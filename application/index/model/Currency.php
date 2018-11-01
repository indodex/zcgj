<?php
namespace  app\index\model;
use app\common\model\Base;
use think\Request;
use think\db;
use think\Session;
class Currency extends Base
{
	const PAGE_LIMIT = '6';	// 用户表分页限制
    const PAGE_SHOW = '4';	// 显示分页菜单数量
   
	/**
	 * model 个人中心
	 */
	public function indexList($uid){
		
		/////////// 账户资产
//		// PEA
//		$all_pea = Db::name('user_cur') -> where('uid='.$uid.' AND cur_id=2') -> value('number');	// 账户中的PEA
//		$frozen_pea_where['uid'] = $uid;
//		$frozen_pea_where['trade_status'] = array('in',1,5);	//1挂卖中 5部分成交
//		$frozen_pea_where['cur_id'] = 2;
//		$frozen_pea = Db::name('trade') -> where($frozen_pea_where) -> sum('number');	// 交易表中冻结的PEA
//		$return['all_pea'] = sprintf('%.4f',$all_pea + $frozen_pea);
//		// 获取最新的 PEA 对 USDT 汇率
//		$exchange_where['trade_status'] = 3;
//		$exchange_where['cur_id'] = 2;
//		$exchange_where['trade_mold'] = 0;
//		$exchange = Db::name('trade') -> where($exchange_where) -> order('end_time DESC') -> value('price');
//		if(!$exchange){
//			$exchange = 1;
//		}
//		$usdt = $return['all_pea'] * $exchange;
//		$return['usdt'] = sprintf('%.4f',$usdt);
//		// 总资产
//		$assets_where['uid'] = $uid;
//		$all_usdt = Db::name('user_cur') -> where('uid='.$uid.' AND cur_id=1') -> value('number');
//		$return['all_usdt'] = sprintf("%.4f",$all_usdt + $return['usdt']);
//		
//		// CNY
//		$url = file_get_contents('http://market.niuyan.com/api/v2/web/coin?coin_id=bitcny');
//		$json = json_decode($url,true);
//		$cny_data = $json['data']['coin']['price_usd'];
//		$cny = $return['all_usdt'] * config('EXCHANGE_RATE');
//		$return['cny'] = sprintf("%.4f",$cny);
		
		// 账户币种资产统计
		$cur_info = Db::name('currency') -> where('id != 1') -> field('id,name') -> select();
		$all_cur_num = '';
		foreach($cur_info as $k => $v){
			$all_cur = Db::name('user_cur') -> where('uid='.$uid.' AND cur_id='.$v['id']) -> value('number');	// 获取用户账户中的币种数量
			// 查询用户冻结的币种数量
			$frozen_where['uid'] = $uid;
			$frozen_where['trade_status'] = array('in','1,5');
			$frozen_where['cur_id'] = $v['id'];
			$frozen_cur = Db::name('trade') -> where($frozen_where) -> sum('number');	// 用户在交易表中所冻结的币种数量
			$cur_info[$k]['all_cur'] = sprintf('%.4f',$all_cur + $frozen_cur);
			// 获取最新的币种交易对 USDT 的汇率
			$exchange_where['trade_status'] = 3;
			$exchange_where['cur_id'] = $v['id'];
			$exchange_where['trade_mold'] = 0;
			$exchange = Db::name('trade') -> where($exchange_where) -> order('end_time DESC') -> value('price');	// 获取币种最新的价格率汇
			if(!$exchange){
				$exchange = 1;
			}
			$cur_num = $cur_info[$k]['all_cur'] * $exchange;
			$cur_info[$k]['cur_num'] = sprintf('%.4f',$cur_num);
			
			// 查询用户总资产
			$all_cur_num += sprintf('%.4f',$cur_info[$k]['cur_num']);
		}
		// 获取用户现有 USDT 
		$assets_where['uid'] = $uid;
		$user_usdt = Db::name('user_cur') -> where('uid='.$uid.' AND cur_id=1') -> value('number');
		// 获取用户所有的 CNY
		$url = file_get_contents('http://market.niuyan.com/api/v2/web/coin?coin_id=bitcny');
		$json = json_decode($url,true);
		$cny_data = $json['data']['coin']['price_usd'];
		$user_usdt = $user_usdt + $all_cur_num;	// 获取用户现有的 USDT 和所有的币种哲合为 USDT 的总数
		$return['user_usdt'] = $user_usdt;
		$cny = $user_usdt * config('EXCHANGE_RATE');	// 获取用户所有的币种拆合为 CNY
		$user_cny = sprintf("%.4f",$cny);
		$return['user_cny'] = $user_cny;
		$return['cur_info'] = $cur_info;
		
		// 账户币种列表
		$assets = Db::name('user_cur') -> where($assets_where) -> select();
		foreach($assets as $k => $v){
			// 币图标/币名
			$assets[$k]['cur_icon'] = Db::name('currency') -> where('id',$v['cur_id']) -> value('icon');
			$assets[$k]['cur_name'] = Db::name('currency') -> where('id',$v['cur_id']) -> value('name');
			
			// 冻结
			$trade_where['uid'] = $uid;
			$trade_where['trade_type'] = 1;
			$trade_where['trade_status'] = array('in',1,5);	//1挂卖中 5部分成效
			$trade_where['cur_id'] = $v['cur_id'];
			$trade_frozen = Db::name('trade') -> where($trade_where) -> sum('number');	// 查询交易表中被冻结的资金
			$trade_frozen = sprintf("%.4f",$trade_frozen);	// 转换为4位小数
			
			$method_where['uid'] = $uid;
			$method_where['cur_id'] = $v['cur_id'];
			$method_where['method_type'] = 2;	// 2提现
			$method_where['recharge_status'] = 1;	// 1待审核
			$method_frozen = Db::name('method') -> where($method_where) -> sum('cur_num');	// 查询充值/提现申请表中被冻结的资金
			$method_frozen = sprintf("%.4f",$method_frozen);	// 转换为4位小数
			$assets[$k]['frozen'] = $trade_frozen - $method_frozen;	// 由于提现中的数据为负数。所以使用 - 号(负负得正)
			
			$assets[$k]['frozen'] = sprintf("%.4f",$assets[$k]['frozen']);
			$assets[$k]['number'] = sprintf('%.4f',$v['number'] + $assets[$k]['frozen']);
			
			// 可用
			$assets[$k]['available'] = sprintf("%.4f",$v['number']);
			$assets[$k]['available'] = sprintf("%.4f",$assets[$k]['available']);
			// 单独设置usdt可用
			if($v['cur_id'] === 1){
				$usdt_available = $assets[$k]['available'];
			}
		}
		// 返回账户资产
		$return['assets'] = $assets;
		$return['usdt_available'] = $usdt_available;
		
		/////////// 财务日志统计
		$log_count = Db::name('record') -> where('user_id',$uid) -> count();
		$return['log_count'] = $log_count;
		
		/////////// 委托管理统计
		$entrust_manage_where['uid'] = $uid;
		$entrust_manage_where['trade_status'] = 1;
		$entrust_manage_count = Db::name('trade') -> where($entrust_manage_where) -> count();
		$return['entrust_manage_count'] = $entrust_manage_count;
		
		/////////// 我的成交统计
		$deal_count = Db::name('order') -> where('buyer_id='.$uid.' OR seller_id='.$uid) -> count();
		$return['deal_count'] = $deal_count;
		
		/////////// 委托历史统计
		$entrust_history_count = Db::name('trade') -> where('trade_status=3') -> count();
		$return['entrust_history_count'] = $entrust_history_count;
		
		/////////// 个人信息
		$user = Db::name('user') -> where('id',$uid) -> field('id,account,invitation_code,invitation_qrcode,username,real_name,identity,tel,address,identity_front,identity_behind,wallet,bank_number,alipay_accout,wechat_accout,identity_status') -> find();
		// 是否实名认证
		if(empty($user['identity_front']) && empty($user['identity_behind'])){
			$user['is_real'] = 0;
		}else{
			$user['is_real'] = 1;
		}
		// 返回个人信息
		$return['user'] = $user;
		
		/////////// 提币地址
		$withdraw_addr = Db::name('user_withdraw_address') -> where('uid',$uid) -> field('id,uid,withdraw_address,remarks') -> select();
		foreach($withdraw_addr as $k => $v){
			$withdraw_addr[$k]['real_name'] = Db::name('user') -> where('id',$v['uid']) -> value('real_name');
			$withdraw_addr[$k]['band'] = '已绑定';
		}
		// 返回用户地址
		$return['withdraw_addr'] = $withdraw_addr;
		
		/////////// 充值记录统计
		$recharge_where['uid'] = $uid;
		$recharge_where['method_type'] = 1;
		$recharge_count = Db::name('method') -> where($recharge_where) -> count();
		$return['recharge_count'] = $recharge_count;
		
		/////////// 提现记录统计
		$withdraw_where['uid'] = $uid;
		$withdraw_where['method_type'] = 2;
		$withdraw_count = Db::name('method') -> where($withdraw_where) -> count();
		$return['withdraw_count'] = $withdraw_count;
		
		/////////// 邀请好友
		$son = Db::name('user') -> where('parent_id',$uid) -> field('id,username,real_name,tel') -> select();
		$son_count = Db::name('user') -> where('parent_id',$uid) -> count();
		$gid = Db::name('game') -> where('end_time',null) -> value('id');	// 获取游戏ID
		foreach($son as $k => $v){
			// 入金量&分红奖&加权分红奖
			$son_game_statistic = Db::name('user_game_statistic') -> where('gid='.$gid.' AND uid='.$v['id']) -> field('eth,bonus,get_weighted') -> find();
			$son[$k]['usdt'] = $son_game_statistic['eth'];
			$son[$k]['bonus'] = $son_game_statistic['bonus'];
			if(!$son_game_statistic['get_weighted']){
				$son[$k]['weighted'] = '--';
			}else{
				$son[$k]['weighted'] = $son_game_statistic['get_weighted'];
			}
			// 查询下下级用户
			$son[$k]['grandson'] = Db::name('user') -> where('parent_id',$v['id']) -> field('id,username,real_name,tel') -> select();
			foreach($son[$k]['grandson'] as $key => $value){
				// 入金量&分红奖&加权分红奖
				$grandson_game_statistic = Db::name('user_game_statistic') -> where('gid='.$gid.' AND uid='.$value['id']) -> field('eth,bonus,get_weighted') -> find();
				$son[$k]['grandson'][$key]['usdt'] = $grandson_game_statistic['eth'];
				$son[$k]['grandson'][$key]['bonus'] = $grandson_game_statistic['bonus'];
				if(!$grandson_game_statistic['get_weighted']){
					$son[$k]['grandson'][$key]['weighted'] = '--';
				}else{
					$son[$k]['grandson'][$key]['weighted'] = $grandson_game_statistic['get_weighted'];
				}
			}
		}
		$return['son_count'] = $son_count;	// 邀请人数
		$return['son'] = $son;
		// 计算总排名
		$ranking = Db::name('user') -> field('id')  -> select();
		foreach($ranking as $k => $v){
			$ranking[$k]['num'] = Db::name('user') -> where('parent_id',$v['id']) -> count();
		}
		// $ranking 是二维数组
		$sort = array(
		    'direction' => 'SORT_DESC',        //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
		    'field'     => 'num',            //排序字段
		);
		$arrSort = array();
		foreach($ranking as $k => $v){
		    foreach($v as $k2 => $v2){
		        $arrSort[$k2][$k] = $v2;
		    }
		}
		if($sort['direction']){
		    array_multisort($arrSort[$sort['field']], constant($sort['direction']), $ranking);
		}
		foreach($ranking as $k => $v){
			if($v['id'] === $uid){
				$return['ranking'] = $k + 1;	// 返回排名信息
			}
		}
		// 我的游戏状态
		$game_record_where['gid'] = $gid;
		$game_record_where['uid'] = $uid;
		$game_status = Db::name('game_record') -> where($game_record_where) -> order('create_time DESC') -> value('game_status');
		switch($game_status){
			case 1:
				$is_out = '游戏中';
				break;
			case 2:
				$is_out = '已出局';
				break;
			default:
				$is_out = '未参加';
		}
		$game['status'] = $game_status;
		$game['is_out'] = $is_out;
		$return['game'] = $game;
		
		/////////// 系统消息统计
		$msg_count = Db::name('msg') -> where('uid',$uid) -> count();
		$return['msg_count'] = $msg_count;
		
		return $return;
	}
	
	/**
	 * model 点击取消委托
	 */
	public function cancelCommission($data){
		if(!$data['id']){
			return ['code' => 0,'msg' => '未获取到委托信息!','en_msg' => 'Not obtained commission information!'];
		}
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取到用户信息!','en_msg' => 'User information not obtained!'];
		}
		$check_trade = Db::name('trade') -> where('id',$data['id']) -> find();
		if($check_trade['uid'] != $data['uid']){
			return ['code' => 0,'msg' => '该委托的用户ID与实际不符!','en_msg' => 'The delegated user ID does not match the actual one!'];
		}
		
		switch($check_trade['trade_mold']){
			case 0:
				if($check_trade['trade_type'] == 1){	// 出售
					Db::name('user_cur') -> where('uid='.$check_trade['uid'].' AND cur_id=2') -> setInc('number',$check_trade['number']);
					$trade_where['id'] = $data['id'];
					$trade_where['uid'] = $data['uid'];
					$result = Db::name('trade') -> where($trade_where) -> update(['trade_status' => 4]);
				}else{	// 求购
					$number = $check_trade['number'] * $check_trade['price'];
					Db::name('user_cur') -> where('uid='.$check_trade['uid'].' AND cur_id=1') -> setInc('number',$number);
					$trade_where['id'] = $data['id'];
					$trade_where['uid'] = $data['uid'];
					$result = Db::name('trade') -> where($trade_where) -> update(['trade_status' => 4]);
				}
				// 判断是否成功
				if($result){
					$condition = 1;
				}else{
					$condition = 0;
				}
				break;
			case 1:	// USDT交易
				if($check_trade['trade_type'] == 1){	// 出售
					// 返回卖方支付的预定金
					Db::name('user_cur') -> where('uid='.$check_trade['uid'].' AND cur_id=1') -> setInc('number',$check_trade['number']);
					$trade_where['id'] = $data['id'];
					$trade_where['uid'] = $data['uid'];
					$result = Db::name('trade') -> where($trade_where) -> update(['trade_status' => 4]);
				}else{	// 求购
					$trade_where['id'] = $data['id'];
					$trade_where['uid'] = $data['uid'];
					$result = Db::name('trade') -> where($trade_where) -> update(['trade_status' => 4]);
				}
				// 判断是否成功
				if($result){
					$condition = 1;
				}else{
					$condition = 0;
				}
				break;
		}
		
		if($condition === 1){
			return ['code' => 1,'msg' => '取消成功!','en_msg' => 'Cancel success!'];
		}else{
			return ['code' => 0,'msg' => '取消失败!','en_msg' => 'Cancel failure!'];
		}
	}
	
	/**
	 * model 修改登陆密码
	 */
	public function modLoginPwd($data){
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!','en_msg' => 'User information not obtained!'];
		}
		if(!$data['old_pwd']){
			return ['code' => 0,'msg' => '请输入原登陆密码!','en_msg' => 'Please enter the original login password!'];
		}else{
			$old_pwd = encrypt(trim($data['old_pwd']));
			$is_correct_where['id'] = $data['uid'];
			$is_correct_where['password'] = $old_pwd;
			$is_correct = Db::name('user') -> where($is_correct_where) -> field('password') -> find();
			if(!$is_correct){
				return ['code' => 0,'msg' => '原登陆密码错误!','en_msg' => 'The original login password is incorrect!'];
			}
		}
		if(!$data['new_pwd'] || !$data['re_new_pwd']){
			return ['code' => 0,'msg' => '请输入新登陆密码!','en_msg' => 'Please enter your new login password!'];
		}else{
			$len = strlen($data['new_pwd']);
			if($len < 8 || $len > 32){
				return ['code' => 0,'msg' => '登陆密码需为8~32位!','en_msg' => 'Login password must be 8~32 digits!'];
			}
		}
		if($data['new_pwd'] != $data['re_new_pwd']){
			return ['code' => 0,'msg' => '两次输入的登陆密码不相同!','en_msg' => 'The login password entered twice is different!'];
		}
		
		$where['id'] = $data['uid'];
		$mod['password'] = encrypt(trim($data['new_pwd']));
		$mod_login_pwd = Db::name('user') -> where($where) -> update($mod);
		if($mod_login_pwd){
			return ['code' => 1,'msg' => '修改成功!','en_msg' => 'Successfully modified!'];
		}else{
			return ['code' => 0,'msg' => '修改失败!','en_msg' => 'Fail to edit!'];
		}
	}
	
	/**
	 * model 修改交易密码
	 */
	public function modTransactionPwd($data){
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!','en_msg' => 'User information not obtained!'];
		}
		if(!$data['old_pwd']){
			return ['code' => 0,'msg' => '请输入原交易密码!','en_msg' => 'Please enter the original transaction password!'];
		}else{
			$old_pwd = encrypt(trim($data['old_pwd']));
			$is_correct_where['id'] = $data['uid'];
			$is_correct_where['payment_password'] = $old_pwd;
			$is_correct = Db::name('user') -> where($is_correct_where) -> field('payment_password') -> find();
			if(!$is_correct){
				return ['code' => 0,'msg' => '原交易密码错误!','en_msg' => 'Original transaction password error!'];
			}
		}
		if(!$data['new_pwd'] || !$data['re_new_pwd']){
			return ['code' => 0,'msg' => '请输入新交易密码!','en_msg' => 'Please enter a new transaction password!'];
		}else{
			$len = strlen($data['new_pwd']);
			if($len < 8 || $len > 32){
				return ['code' => 0,'msg' => '交易密码需为8~32位!','en_msg' => 'The transaction password must be 8~32 digits!'];
			}
		}
		if($data['new_pwd'] != $data['re_new_pwd']){
			return ['code' => 0,'msg' => '两次输入的交易密码不相同!','en_msg' => 'The transaction password entered twice is different!'];
		}
		
		$where['id'] = $data['uid'];
		$mod['payment_password'] = encrypt(trim($data['new_pwd']));
		$mod_login_pwd = Db::name('user') -> where($where) -> update($mod);
		if($mod_login_pwd){
			return ['code' => 1,'msg' => '修改成功!','en_msg' => 'Successfully modified!'];
		}else{
			return ['code' => 0,'msg' => '修改失败!','en_msg' => 'Fail to edit!'];
		}
	}
	
	/**
	 * model 修改个人信息
	 */
	public function modPersonalInfo($data){
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!','en_msg' => 'User information not obtained!'];
		}
		$data['username'] = trim($data['username']);
		if(!$data['username']){
			return ['code' => 0,'msg' => '请输入昵称!','en_msg' => 'Please enter a nickname!'];
		}
		if(isset($data['username'])){
			$mod['username'] = $data['username'];
		}
		if(isset($data['address'])){
			$mod['address'] = $data['address'];
		}
		
		$where['id'] = $data['uid'];
		$mod_user_info = Db::name('user') -> where($where) -> update($mod);
		$data = Db::name('user') -> where($where) -> field('username,address') -> find();
		if($mod_user_info){
			return ['code' => 1,'msg' => '修改成功!','en_msg' => 'Successfully modified!','data' => $data];
		}else{
			return ['code' => 0,'msg' => '修改失败!','en_msg' => 'Fail to edit!'];
		}
	}
	
	/**
	 * model 点击保存实名认证
	 */
	public function modIDImg($data){
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!','en_msg' => 'User information not obtained!'];
		}
		// 提交身份证图片路径写入数据库
		$where['id'] = $data['uid'];
		$mod['identity_behind'] = $data['identity_behind'];
		$mod['identity_front'] = $data['identity_front'];
		$result = Db::name('user') -> where($where) -> update($mod);
		
		if($result){
			return ['code' => 1,'msg' => '保存成功!','en_msg' => 'Saved successfully!'];
		}else{
			return ['code' => 0,'msg' => '保存失败!','en_msg' => 'Save failed!'];
		}
	}
	
	/**
	 * model 点击提交充值信息
	 */
	public function userRecharge($data){
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!','en_msg' => 'User information not obtained'];
		}
		if(!$data['cur_num']){
			return ['code' => 0,'msg' => '请填写充值数量!','en_msg' => 'Please fill in the recharge amount!'];
		}
		if(!$data['choose']){
			return ['code' => 0,'msg' => '请选择充值类型!','en_msg' => 'Please select a recharge type!'];
		}else{
			if(isset($data['choose']) && $data['choose'] === 'is_wallet'){
				if(!$data['wallet_adress']){
					return ['code' => 0,'msg' => '未获取钱包信息!','en_msg' => 'No wallet information!'];
				}
			}
			if(isset($data['choose']) && $data['choose'] === 'is_bank'){
				if(!$data['bankname'] || !$data['bank'] || !$data['bankcard']){
					return ['code' => 0,'msg' => '未获取银行卡信息!','en_msg' => 'No bank card information!'];
				}
			}
		}
		if(!$data['url']){
			return ['code' => 0,'msg' => '请上传支付截图!','en_msg' => 'Please upload a payment screenshot!'];
		}
		if(!$data['remarks']){
			return ['code' => 0,'msg' => '请填写备注信息!','en_msg' => 'Please fill in the remarks!'];
		}
		
		$data['cur_id'] = 1;
		$data['status'] = 1;
		$data['method_type'] = 1;
		$data['recharge_status'] = 1;
		$data['create_time'] = time();
		unset($data['choose']);
		$recharge = Db::name('method') -> insert($data);
		if($recharge){
			return ['code' => 1,'msg' => '提交成功,请联系客服!','en_msg' => 'Submitted successfully, please contact customer service!'];
		}else{
			return ['code' => 0,'msg' => '提交失败!','en_msg' => 'Submission Failed!'];
		}
	}
	
	/**
	 * model 点击添加提币地址
	 */
	public function addWithdrawAddr($data){
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!','en_msg' => 'User information not obtained!'];
		}
		if(!$data['remarks']){
			return ['code' => 0,'msg' => '请输入地址标签!','en_msg' => 'Please enter the address label!'];
		}
		if(!$data['withdraw_address']){
			return ['code' => 0,'msg' => '请输入提币地址!','en_msg' => 'Please enter the coin address!'];
		}
		$data['create_time'] = time();
		
		$address_id = Db::name('user_withdraw_address') -> insertGetId($data);
		$data = Db::name('user_withdraw_address') -> where('id',$address_id) -> field('id,uid,withdraw_address,remarks') -> find();
		$data['band'] = '已绑定';
		if($data){
			return ['code' => 1,'msg' => '添加地址成功!','en_msg' => 'Add address successfully!','data' => $data];
		}else{
			return ['code' => 0,'msg' => '添加地址失败!','en_msg' => 'Add address failed!'];
		}
	}
	
	/**
	 * model 点击删除提币地址
	 */
	public function delAddr($id){
		if(!$id){
			return ['code' => 0,'msg' => '未获取要删除的地址信息!','en_msg' => 'Did not get the address information to delete!'];
		}
		$del = Db::name('user_withdraw_address') -> where('id',$id) -> delete();
		if($del){
			return ['code' => 1,'msg' => '删除成功!','en_msg' => 'Successfully deleted!'];
		}else{
			return ['code' => 0,'msg' => '删除失败!','en_msg' => 'Failed to delete!'];
		}
	}
	
	/**
	 * model 获取提现手续费后的金额
	 */
	public function withdrawService($cur_num){
		if(!$cur_num){
			return ['code' => 0,'msg' => '获取提现金额失败!','en_msg' => 'Failed to get cash withdrawal!'];
		}
		$withdraw_service = config('WITHDRAW_SERVICE_CHARGE') * 0.01;
		$service = sprintf('%.4f',$cur_num - ($cur_num * $withdraw_service));
		if($service){
			return ['code' => 1,'data' => $service];
		}else{
			return ['code' => 0,'msg' => '获取实际到账信息失败!','en_msg' => 'Failed to get actual arrival information!'];
		}
	}
	
	/**
	 * model 点击提现
	 */
	public function withdraw($data){
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!','en_msg' => 'User information not obtained!'];
		}
		if(!$data['cur_num']){
			return ['code' => 0,'msg' => '请填写提现数额!','en_msg' => 'Please fill in the withdrawal amount!'];
		}else{
			if($data['cur_num'] > 10000 || $data['cur_num'] < 1){
				return ['code' => 0,'msg' => '提现数额需为1~10000之间!','en_msg' => 'The withdrawal amount needs to be between 1 and 10000!'];
			}
		}
		
		if(!$data['wallet_adress']){
			return ['code' => 0,'msg' => '请选择提币地址!','en_msg' => 'Please select the coin address!'];
		}
		if(!$data['payment_password']){
			return ['code' => 0,'msg' => '请填写交易密码!','en_msg' => 'Please fill in the transaction password!'];
		}else{
			$where['id'] = $data['uid'];
			$where['payment_password'] = encrypt($data['payment_password']);
			$pay_pwd = Db::name('user') -> where($where) -> field('payment_password') -> find();
			if(!$pay_pwd){
				return ['code' => 0,'msg' => '交易密码错误!','en_msg' => 'Transaction password is wrong!'];
			}
		}
		$data['service_charge'] = $data['cur_num'] - $data['service_charge'];
		$data['cur_num'] = '-'.$data['cur_num'];
		$data['cur_id'] = 1;
		$data['status'] = 1;
		$data['method_type'] = 2;
		$data['recharge_status'] = 1;
		$data['create_time'] = time();
		unset($data['payment_password']);
		$withdraw_id = Db::name('method') -> insertGetId($data);
		$data = Db::name('method') -> where('id',$withdraw_id) -> field('id,wallet_adress,cur_num,recharge_status,create_time') -> find();
		// 日期
		$data['create_date'] = date('Y-m-d H:i:s',$data['create_time']);
		// 提现状态
		$dict_where['type'] = 'identity_status';
		$dict_where['value'] = $data['recharge_status'];
		$data['recharge_status_text'] = Db::name('dict') -> where($dict_where) -> value('key');
		// 实际到账
		if($data['recharge_status'] === 2){
			$data['actual'] = trim($data['recharge_status'],'-') - $data['service_charge'];
		}else{
			$data['actual'] = 0;
		}
		
		if($data){
			return ['code' => 1,'msg' => '申请提现成功!','en_msg' => 'Apply for withdrawal!','data' => $data];
		}else{
			return ['code' => 0,'msg' => '申请提现失败!','en_msg' => 'Application withdrawal failed!'];
		}
	}
	
	/**
	 * model 点击绑定手机
	 */
	public function bandTel($data){
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!','en_msg' => 'User information not obtained!'];
		}
		if(!$data['tel']){
			return ['code' => 0,'msg' => '请输入手机号!','en_msg' => 'Please enter phone number!'];
		}else{
			if(is_mobile($data['tel']) === false){
				return ['code' => 0,'msg' => '手机号码格式有误!','en_msg' => 'Mobile phone number format is incorrect!'];
			}
		}
		
		$where['id'] = $data['uid'];
		$mod['tel'] = $data['tel'];
		$result = Db::name('user') -> where($where) -> update($mod);
		$data = Db::name('user') -> where($where) -> value('tel');
		if($result){
			return ['code' => 1,'msg' => '绑定手机号成功!','en_msg' => 'Bind the phone number successfully!','data' => $data];
		}else{
			return ['code' => 0,'msg' => '绑定手机号失败!','en_msg' => 'Binding phone number failed!'];
		}
	}
	
	/**
	 * model 点击绑定私人钱包
	 */
	public function bandWallet($data){
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!','en_msg' => 'User information not obtained!'];
		}
		if(!$data['wallet']){
			return ['code' => 0,'msg' => '请输入绑定私人钱包!','en_msg' => 'Please enter a binding private wallet!'];
		}
		$exist = Db::name('user') -> where('wallet',$data['wallet']) -> find();
		if($exist){
			return ['code' => 0,'msg' => '该钱包地址已经存在!','en_msg' => 'The wallet address already exists!'];
		}
		
		$where['id'] = $data['uid'];
		$mod['wallet'] = $data['wallet'];
		$result = Db::name('user') -> where($where) -> update($mod);
		$data = Db::name('user') -> where($where) -> value('wallet');
		if($result){
			return ['code' => 1,'msg' => '绑定私人钱包成功!','en_msg' => 'Bind a private wallet successfully!','data' => $data];
		}else{
			return ['code' => 0,'msg' => '绑定私人钱包失败!','en_msg' => 'Binding a private wallet failed!'];
		}
	}
	
	/**
	 * model 点击绑定银行卡
	 */
	public function bandBank($data){
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!','en_msg' => 'User information not obtained!'];
		}
		if(!$data['bank_user']){
			return ['code' => 0,'msg' => '请输入姓名!','en_msg' => 'Please type in your name!'];
		}
		if(!$data['bank_name']){
			return ['code' => 0,'msg' => '请输入开户行!','en_msg' => 'Please enter the bank!'];
		}
		if(!$data['bank_number']){
			return ['code' => 0,'msg' => '请输入卡号!','en_msg' => 'Please enter the card number!'];
		}
		if(!$data['bank_tel']){
			return ['code' => 0,'msg' => '请输入预留手机号!','en_msg' => 'Please enter a reserved mobile number!'];
		}
		
		$where['id'] = $data['uid'];
		$mod['bank_user'] = $data['bank_user'];
		$mod['bank_name'] = $data['bank_name'];
		$mod['bank_number'] = $data['bank_number'];
		$mod['bank_tel'] = $data['bank_tel'];
		$result = Db::name('user') -> where($where) -> update($mod);
		$data = Db::name('user') -> where($where) -> value('bank_number');
		if($result){
			return ['code' => 1,'msg' => '绑定银行卡成功!','en_msg' => 'Bind bank card successfully!','data' => $data];
		}else{
			return ['code' => 0,'msg' => '绑定银行卡失败!','en_msg' => 'Binding bank card failed!'];
		}
	}
	
	/**
	 * model 点击绑定支付宝
	 */
	public function bandAlipay($data){
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!','en_msg' => 'User information not obtained!'];
		}
		if(!$data['alipay_account']){
			return ['code' => 0,'msg' => '请输入支付宝账号!','en_msg' => 'Please enter an Alipay account!'];
		}
		
		$where['id'] = $data['uid'];
		$mod['alipay_accout'] = $data['alipay_account'];
		$result = Db::name('user') -> where($where) -> update($mod);
		$data = Db::name('user') -> where($where) -> value('alipay_accout');
		if($result){
			return ['code' => 1,'msg' => '绑定支付宝成功!','en_msg' => 'Binding Alipay successfully!','data' => $data];
		}else{
			return ['code' => 0,'msg' => '绑定支付宝失败!','en_msg' => 'Binding Alipay failed!'];
		}
	}
	
	/**
	 * model 点击绑定微信
	 */
	public function bandWechat($data){
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!','en_msg' => 'User information not obtained!'];
		}
		if(!$data['wechat_account']){
			return ['code' => 0,'msg' => '请输入微信账号!','en_msg' => 'Please enter a WeChat account!'];
		}
		
		$where['id'] = $data['uid'];
		$mod['wechat_accout'] = $data['wechat_account'];
		$result = Db::name('user') -> where($where) -> update($mod);
		$data = Db::name('user') -> where($where) -> value('wechat_accout');
		if($result){
			return ['code' => 1,'msg' => '绑定微信成功!','en_msg' => 'Bind WeChat success!','data' => $data];
		}else{
			return ['code' => 0,'msg' => '绑定微信失败!','en_msg' => 'Binding WeChat failed!'];
		}
	}
	
	/**
	 * model 点击显示消息详情
	 */
	public function msgDetail($id){
		if(!$id){
			return ['code' => 0,'msg' => '未获取消息信息!','en_msg' => 'Not getting message information!'];
		}
		$info = Db::name('msg') -> where('id',$id) -> field('id,title,en_title,content,en_content,create_time') -> find();
		$info['create_date'] = date('Y-m-d H:i:s',$info['create_time']);
		if($info){
			return ['code' => 1,'data' => $info];
		}else{
			return ['code' => 0,'msg' => '获取消息失败!','en_msg' => 'Failed to get message!'];
		}
	}
	
	/**
	 * model 财务日志(layui分页)
	 */
	public function logList($data){
		if($data['page']){
			$page_start = $data['page'] * 15 - 15;
		}else{
			$page_start = 0;
		}
		$page_end = 15;
		$log = Db::name('record') -> where('user_id',$data['uid']) -> field('money,cur_num,record_type,create_time,remarks,cur_id') -> order('create_time DESC') -> limit($page_start,$page_end) -> select();
		foreach($log as $k => $v){
			// 类型
			$dict_where['type'] = 'trade_type';
			$dict_where['value'] = $v['record_type'];
			$dict_where['state'] = 1;
			$log[$k]['record_type_text'] = Db::name('dict') -> where($dict_where) -> value('key');
			// 变动资金
			$log[$k]['finance'] = sprintf("%.4f",$v['cur_num']);
			// 交易类型
			$type = strstr($v['cur_num'],'-');
			if($type === false){
				$log[$k]['type_text'] = '收入';
			}else{
				$log[$k]['type_text'] = '支出';
			}
			// 币名
			$log[$k]['cur_name'] = Db::name('currency') -> where('id',$v['cur_id']) -> value('name');
			// 成效时间
			$log[$k]['create_date'] = date('Y-m-d H:i:s',$v['create_time']);
		}
		if($log){
			return ['code' => 1,'log' => $log];
		}else{
			return ['code' => 0];
		}
	}
	
	/**
     * model 委托管理(layui分页)
     */
    public function entrustList($data){
    	if($data['page']){
			$page_start = $data['page'] * 10 - 10;
		}else{
			$page_start = 0;
		}
		$page_end = 10;
    	$entrust_manage_where['uid'] = $data['uid'];
		$entrust_manage_where['trade_status'] = 1;
		$entrust_manage = Db::name('trade') -> where($entrust_manage_where) -> limit($page_start,$page_end) -> select();
		foreach($entrust_manage as $k => $v){
			// 日期格式
			$entrust_manage[$k]['start_date'] = date('Y-m-d H:i:s',$v['start_time']);
			// 币名
			$entrust_manage[$k]['cur_name'] = Db::name('currency') -> where('id',$v['cur_id']) -> value('name');
			// 类型
			$dict_where['type'] = 'trade_type';
			$dict_where['value'] = $v['trade_type'];
			$entrust_manage[$k]['trade_type_text'] = Db::name('dict') -> where($dict_where) -> value('key');
			// 成交量
			$entrust_manage[$k]['deal'] = 0;
			// 尚未数量
			$entrust_manage[$k]['no_deal'] = $v['number'];
			// 状态
			switch($v['trade_status']){
				case 1:
					$entrust_manage[$k]['trade_status_text'] = '挂卖中';
					$entrust_manage[$k]['trade_status_text_en'] = 'Hanging up';
					break;
				case 2:
					$entrust_manage[$k]['trade_status_text'] = '交易中';
					$entrust_manage[$k]['trade_status_text_en'] = 'In transaction';
					break;
			}
		}
		if($entrust_manage){
			return ['code' => 1,'entrust_manage' => $entrust_manage];
		}else{
			return ['code' => 0];
		}
    }
	
	/**
     * model 我的成交(layui分页)
     */
    public function dealList($data){
    	if($data['page']){
			$page_start = $data['page'] * 15 - 15;
		}else{
			$page_start = 0;
		}
		$page_end = 15;
		$deal = Db::name('order') -> where('buyer_id='.$data['uid'].' OR seller_id='.$data['uid']) -> field('id,order,order_number,price,buyer_id,seller_id,done_time,trade_type,cur_id') -> limit($page_start,$page_end) -> order('addtime DESC') -> select();
		foreach($deal as $k => $v){
			// 币名
			$deal[$k]['cur_name'] = Db::name('currency') -> where('id',$v['cur_id']) -> value('name');
			// 成效时间
			$deal[$k]['done_date'] = date('Y-m-d H:i:s',$v['done_time']);
			// 类型
			if($v['buyer_id'] === $data['uid']){
				$deal[$k]['type_text'] = '买';
			}else{
				$deal[$k]['type_text'] = '卖';
			}
			// 成交价格
			$deal[$k]['deal_price'] = sprintf('%.4f',$v['price']);
			// 成效额
			$turnover = $v['order_number'] * $v['price'];
			$deal[$k]['turnover'] = sprintf('%.4f',$turnover);
		}
		if($deal){
			return ['code' => 1,'deal' => $deal];
		}else{
			return ['code' => 0];
		}
    }
	
	/**
	 * model 委托历史(layui分页)
	 */
	public function historyList($data){
		if($data['page']){
			$page_start = $data['page'] * 15 - 15;
		}else{
			$page_start = 0;
		}
		$page_end = 15;
		$entrust_history = Db::name('trade') -> where('trade_status=3') -> limit($page_start,$page_end) -> select();
		foreach($entrust_history as $k => $v){
			// 日期格式
			$entrust_history[$k]['start_date'] = date('Y-m-d H:i:s',$v['start_time']);
			// 币名
			$entrust_history[$k]['cur_name'] = Db::name('currency') -> where('id',$v['cur_id']) -> value('name');
			// 类型
			$dict_where['type'] = 'trade_type';
			$dict_where['value'] = $v['trade_type'];
			$entrust_history[$k]['trade_type_text'] = Db::name('dict') -> where($dict_where) -> value('key');
			// 成交量
			$entrust_history[$k]['deal'] = $v['number'];
			// 尚未数量
			$entrust_history[$k]['no_deal'] = 0;
			// 状态
			$entrust_history[$k]['trade_status_text'] = '交易完成';
		}
		if($entrust_history){
			return ['code' => 1,'history' => $entrust_history];
		}else{
			return ['code' => 0];
		}
	}
	
	/**
	 * model 充值记录(layui分页)
	 */
	public function rechargeList($data){
		if($data['page']){
			$page_start = $data['page'] * 5 - 5;
		}else{
			$page_start = 0;
		}
		$page_end = 5;
		$recharge_where['uid'] = $data['uid'];
		$recharge_where['method_type'] = 1;
		$recharge_list = Db::name('method') -> where($recharge_where) -> field('id,wallet_adress,cur_num,service_charge,recharge_status,create_time') -> limit($page_start,$page_end) -> order('create_time DESC') -> select();
		foreach($recharge_list as $k => $v){
			// 日期
			$recharge_list[$k]['create_date'] = date('Y-m-d H:i:s',$v['create_time']);
			// 充值状态
			$dict_where['type'] = 'identity_status';
			$dict_where['value'] = $v['recharge_status'];
			$recharge_list[$k]['recharge_status_text'] = Db::name('dict') -> where($dict_where) -> value('key');
			// 实际到账
			if($v['recharge_status'] === 2){
				$recharge_list[$k]['actual'] = trim($v['cur_num'],'-') - $v['service_charge'];
			}else{
				$recharge_list[$k]['actual'] = 0;
			}
		}
		if($recharge_list){
			return ['code' => 1,'recharge' => $recharge_list];
		}else{
			return ['code' => 0];
		}
	}
	
	/**
	 * model 提现记录(layui分页)
	 */
	public function withdrawList($data){
		if($data['page']){
			$page_start = $data['page'] * 5 - 5;
		}else{
			$page_start = 0;
		}
		$page_end = 5;
		$withdraw_where['uid'] = $data['uid'];
		$withdraw_where['method_type'] = 2;
		$withdraw_list = Db::name('method') -> where($withdraw_where) -> field('id,wallet_adress,cur_num,service_charge,recharge_status,create_time') -> limit($page_start,$page_end) -> order('create_time DESC') -> select();
		foreach($withdraw_list as $k => $v){
			// 日期
			$withdraw_list[$k]['create_date'] = date('Y-m-d H:i:s',$v['create_time']);
			// 提现状态
			$dict_where['type'] = 'identity_status';
			$dict_where['value'] = $v['recharge_status'];
			$withdraw_list[$k]['recharge_status_text'] = Db::name('dict') -> where($dict_where) -> value('key');
			// 实际到账
			if($v['recharge_status'] === 2){
				$withdraw_list[$k]['actual'] = trim($v['cur_num'],'-') - $v['service_charge'];
			}else{
				$withdraw_list[$k]['actual'] = 0;
			}
			// 提现数量
			$withdraw_list[$k]['cur_num'] = trim($v['cur_num'],'-');
		}
		if($withdraw_list){
			return ['code' => 1,'withdraw' => $withdraw_list];
		}else{
			return ['code' => 0];
		}
	}
	
	/**
	 * model 系统消息(layui分页)
	 */
	public function msgList($data){
		if($data['page']){
			$page_start = $data['page'] * 15 - 15;
		}else{
			$page_start = 0;
		}
		$page_end = 15;
		$msg = Db::name('msg') -> where('uid',$data['uid']) -> limit($page_start,$page_end) -> select();
		foreach($msg as $k => $v){
			$msg[$k]['create_date'] = date('Y-m-d H:i:s',$v['create_time']);
		}
		if($msg){
			return ['code' => 1,'msg' => $msg];
		}else{
			return ['code' => 0];
		}
	}
	
    public function currencyInfo($id)
    {
        $userinfo['use_eth'] = sprintf('%.4f',db('user_cur')->where('uid',session('uid'))->where('cur_id',1)->value('number'));
        $map['uid'] = session('uid');
        $map['cur_id'] = $id;
        $map['trade_status'] = ['in','1,2'];
        $map['trade_type'] = 2;
        $map['trade_mold'] = 0;
        // 查询冻结的资金
        $frozen = Db::name('trade') -> where($map) -> field('price,number') -> select();
        $frozen_num = '';
        foreach($frozen as $k => $v){
        	$frozen_num += $v['price'] * $v['number'];
        }
//      $frozen_price = db('trade')->where($map)->avg('price');
//      $frozen_number = db('trade')->where($map)->sum('number');
        $userinfo['frozen_eth'] = sprintf('%.4f',$frozen_num);
        $map2['uid'] = session('uid');
        $map2['cur_id'] = $id;
        $userinfo['use_cur'] = sprintf('%.4f',db('user_cur')->where($map2)->value('number'));
        $map3['uid'] = session('uid');
        $map3['trade_status'] = ['in','1,2,5'];
        $map3['trade_type'] = 1;
        $map3['cur_id'] = $id;
        $map3['trade_mold'] = 0;
        $userinfo['frozen_cur'] = db('trade')->where($map3)->sum('number');
        $userinfo['currencyinfo'] = $this->currencyInfo2($id);
        $userinfo['total'] = sprintf('%.4f',$userinfo['use_eth'] + $userinfo['frozen_eth']);
        $userinfo['cny'] = sprintf('%.4f',$userinfo['total'] * config('EXCHANGE_RATE'));
        return $userinfo;
    }

    public function currencyInfo2($id)
    {
      $currencyinfo = db('currency')->where('id',$id)->field('id,name,icon')->find();
			// 最佳买价
			$base_where['cur_id'] = $id;
			$bast_where['trade_status'] = 1;
			$bast_where['end_time'] = 0;
			$bast_where['trade_type'] = 2;
			$currencyinfo['buy_price'] = Db::name('trade') -> where($bast_where) -> order('price DESC') -> value('price');
			$currencyinfo['buy_price_cny'] = sprintf('%.2f',$currencyinfo['buy_price'] * config('EXCHANGE_RATE'));
			if(!$currencyinfo['buy_price']){
				$currencyinfo['buy_price'] = 0;
			}
			// 最佳卖价
			$bast_where['trade_type'] = 1;
			$currencyinfo['sell_price'] = Db::name('trade') -> where($bast_where) -> order('price ASC') -> value('price');
			$currencyinfo['sell_price_cny'] = sprintf('%.2f',$currencyinfo['sell_price'] * config('EXCHANGE_RATE'));
			if(!$currencyinfo['sell_price']){
				$currencyinfo['sell_price'] = 0;
			}
			
			// 获取币种行情
			$cur_market = Db::name('cur_market') -> where('cur_id',$id) -> whereTime('create_time','today') -> find();
			$currencyinfo['price_new'] = isset($cur_market['price_new']) ? $cur_market['price_new'] : 0;
			$currencyinfo['price_new_cny'] = isset($cur_market['price_new_cny']) ? $cur_market['price_new_cny'] : 0;
			$currencyinfo['max_price'] = isset($cur_market['max_price']) ? $cur_market['max_price'] : 0;
			$currencyinfo['max_price_cny'] = isset($cur_market['max_price_cny']) ? $cur_market['max_price_cny'] : 0;
			$currencyinfo['min_price'] = isset($cur_market['min_price']) ? $cur_market['min_price'] : 0;
			$currencyinfo['min_price_cny'] = isset($cur_market['min_price_cny']) ? $cur_market['min_price_cny'] : 0;
			$currencyinfo['buy_one'] = isset($cur_market['buy_one']) ? $cur_market['buy_one'] : 0;
			$currencyinfo['buy_one_cny'] = isset($cur_market['buy_one_cny']) ? $cur_market['buy_one_cny'] : 0;
			$currencyinfo['sell_one'] = isset($cur_market['sell_one']) ? $cur_market['sell_one'] : 0;
			$currencyinfo['sell_one_cny'] = isset($cur_market['sell_one_cny']) ? $cur_market['sell_one_cny'] : 0;
			$currencyinfo['volume'] = isset($cur_market['volume']) ? $cur_market['volume'] : 0;
			$currencyinfo['day_rise_fall'] = isset($cur_market['day_rise_fall']) ? $cur_market['day_rise_fall'] : 0;
			$currencyinfo['day_rise_fall_color'] = isset($cur_market['day_rise_fall_color']) ? $cur_market['day_rise_fall_color'] : '';
			
    		return $currencyinfo;
    }

    public function tradelist($id)
    {
    	$all_map['uid'] = ['neq',session('uid')];
    	$all_map['trade_status'] = 1;
    	$all_map['cur_id'] = $id;
    	$all_map['trade_mold'] = 0;
    	$all_trade = db('trade')->where($all_map)->select();
    	$my_map['order_status'] = 3;
    	$my_map['cur_id'] = $id;
    	$my_map['trade_mold'] = 0;
    	$my_trade = db('order')->where('buyer_id|seller_id',session('uid'))->where($my_map)->order('done_time desc')->select();
    	foreach ($my_trade as $k => $v) {
    		$table_trade = Db::name('trade') -> where('id',$v['trade_id']) -> field('uid,trade_type') -> find();
    		if($table_trade['uid'] === session('uid')){
    			if($table_trade['trade_type'] === 1){
    				$my_trade[$k]['order_type'] = '卖';
    			}else{
    				$my_trade[$k]['order_type'] = '买';
    			}
    		}else{
    			if($table_trade['trade_type'] === 1){
    				$my_trade[$k]['order_type'] = '买';
    			}else{
    				$my_trade[$k]['order_type'] = '卖';
    			}
    		}
    		
    		// 成交额
    		$my_trade[$k]['turnover'] = sprintf('%.4f',$v['price'] * $v['order_number']);
    		
    		$my_trade[$k]['done_date'] = date('Y-m-d H:i:s',$v['done_time']);
//  		if($v['buyer_id'] == session('uid')){
//  			$my_trade[$k]['order_type'] = '买';
//  		}else{
//  			$my_trade[$k]['order_type'] = '卖';
//  		}
    	}
    	$map['uid'] = session('uid');
    	$map['trade_status'] = 1;
    	$map['cur_id'] = $id;
    	$map['trade_mold'] = 0;
    	$trade = db('trade')->where($map)->select();
    	$result['all_trade'] = $all_trade;
    	$result['my_trade'] = $my_trade;
    	$result['trade'] = $trade;
    	return $result;
    }


    //K线图
    public function klinegraph($id)
    {
        $currencyinfo = db('currency')->where('id',$id)->field('id,name,icon,api,huobi_key')->find();
          $datas = db('kline')->where('cur_id',$id)->order('time asc')->select();
          foreach ($datas as $k => $v) {
                //$result[$k][0] = $v['time']*1000;
                $result[$k][0] = date('Y-m-d H:i:s',$v['time']);
                $result[$k][1] = (float)$v['open_price'];
                $result[$k][2] = (float)$v['max_price'];
                $result[$k][3] = (float)$v['min_price'];
                $result[$k][4] = (float)$v['close_price'];
                $result[$k][5] = (float)$v['vol'];
            }
        return $result;
    }
}
