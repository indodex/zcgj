<?php
namespace app\index\controller;
use think\Db;
use think\Session;
use app\common\controller\Base;
class User extends Base
{
	/**
	 * controller 完善/个性个人信息
	 */
	public function editUser(){
		if(Request::instance() -> isPost()){
			return json(model('User') -> editUser(input('post.')));
		}
	}
	
	/**
	 * 钱包
	 */
	public function wallet(){
		// 获取用户ID
		$uid = is_login($uid);
		$this -> assign('uid',$uid);
		$this -> assign('wallet',model('User') -> userWallet($uid));
		return $this -> fetch();
	}
	
	/**
	 * controller 奖金
	 */
	public function user_bouns(){
		// 获取用户ID
		$uid = is_login($uid);
		$this -> assign('uid',$uid);
		$this -> assign('bouns',model('User') -> userBouns($uid));
		return $this -> fetch();
	}
	
	/**
	 * controller 提现
	 */
	public function withdraw(){
		// 获取用户ID
		$uid = is_login($uid);
		$this -> assign('bouns',model('User') -> withdraw($uid));
		return $this -> fetch();
	}
	
	/**
	 * controller 点击提现
	 */
	public function do_withdraw(){
		if(Request::instance() -> isPost()){
			return json(model('User') -> doWithdraw(input('post.')));
		}
	}
	
	/**
	 * controller 转账
	 */
	public function transfer(){
		if(Request::instance() -> isPost()){
			return json(model('User') -> doTransfer(input('post.')));
		}
		$this -> assign('voucher',model('User') -> getVou());
		return $this -> fetch();
	}
	
	/**
	 * controller 点击绑定银行卡
	 */
	public function band_bank(){
		if(Request::instance() -> isPost()){
			return json(model('User') -> bandBank(input('post.')));
		}
	}
	
	/**
	 * controller 修改密码
	 */
	public function mod_pwd(){
		if(Request::instance() -> isPost()){
			return json(model('User') -> modPwd(input('post.')));
		}
	}
	
	/**
	 * controller 地址列表
	 */
	public function address(){
		// 获取用户ID
		$uid = is_login($uid);
		$this -> assign('address',model('User') -> addressList($uid));
		return $this -> fetch();
	}
	
	/**
	 * controller 添加地址
	 */
	public function add_addr(){
		if(Request::instance() -> isPost()){
			return json(model('User') -> addAddr(input('post.')));
		}
	}
	
	/**
	 * controller 编辑地址
	 */
	public function edit_addr(){
		if(Request::instance() -> isPost()){
			return json(model('User') -> editAddr(input('post.')));
		}
		
		// 获取用户ID
		$uid = is_login($uid);
		$this -> assign('addr',Db::name('user_addr') -> where('uid',$uid) -> find());
		return $this -> fetch();
	}
	
	/**
	 * controller 我的推广
	 */
	public function my_promotion(){
		// 获取用户ID
		$uid = is_login($uid);
		$this -> assign('promotion',Db::name('user') -> where('parent_id',$uid) -> field('id,real_name,tel') -> select());
		return $this -> fetch();
	}
	
	// 上传图片
    public function upload_pic(){
    	$type = trim(input('type'));
    	$uid = input('uid');
    	if(!$type || !$uid){
    		$ret = ['code' => 0,'msg' => '参数错误!'];
    	}else{
    		$file = request() -> file('file');
    		if($file){
    			$info = $file -> move(ROOT_PATH . 'public' . DS . 'upload/' . $type . '/' . $uid,true,true,2);
    			if($info){
    				$link = '/upload/' . $type . '/' . $uid . '/' . $info -> getSaveName();
    				$ret = ['code' => 1,'msg' => '上传成功!','url' => $link];
    			}else{
    				$ret = ['code' => 0,'msg' => $file -> getError()];
    			}
    		}else{
    			$ret = ['code' => 0,'msg' => '未上传!'];
    		}
    	}
    	return json($ret);
    }
    
}