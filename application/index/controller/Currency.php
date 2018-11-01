<?php
namespace app\index\controller;

use app\common\controller\Base;
use think\Request;
use think\Session;
use think\Db;

class Currency extends Base
{
    /**
     * controller 个人中心
     */
    public function index(){
		
		// 获取用户ID
		$uid = is_login($uid);
		$this -> assign('uid',$uid);
    	
    	// 判断是否要跳转到 c2c
    	$jump = input('jump_c2c');
    	if($jump){
    		$this -> assign('jump',1);
    	}else{
    		$this -> assign('jump',0);
    	}
    	
        $this -> assign('list',model('Currency') -> indexList($uid));
        $this -> assign('orderlist',model('Order') -> c2clist());
        return $this -> fetch();
    }
	
	/**
	 * controller 点击取消委托
	 */
	public function cancel_commission(){
		if(Request::instance() -> isPost()){
			return json(model('Currency') -> cancelCommission(input('post.')));
		}
	}
	
	/**
	 * controller 修改登陆密码
	 */
	public function mod_login_pwd(){
		if(Request::instance() -> isPost()){
			return json(model('Currency') -> modLoginPwd(input('post.')));
		}
	}
	
	/**
	 * controller 修改交易密码
	 */
	public function mod_transaction_pwd(){
		if(Request::instance() -> isPost()){
			return json(model('Currency') -> modTransactionPwd(input('post.')));
		}
	}
	
	/**
	 * controller 修改个人信息
	 */
	public function mod_personal_info(){
		if(Request::instance() -> isPost()){
			return json(model('Currency') -> modPersonalInfo(input('post.')));
		}
	}
	
	// 上传图片
    public function upload_pic(){
    	$type = trim(input('type'));
    	$uid = input('uid');
    	if(!$type || !$uid){
    		$ret = ['code' => 0,'msg' => '参数错误!','en_msg' => 'Parameter error!'];
    	}else{
    		$file = request() -> file('file');
    		if($file){
    			$info = $file -> move(ROOT_PATH . 'public' . DS . 'upload/' . $type . '/' . $uid,true,true,2);
    			if($info){
    				$link = '/upload/' . $type . '/' . $uid . '/' . $info -> getSaveName();
    				$ret = ['code' => 1,'msg' => '上传成功!','en_msg' => 'Successful upload!','url' => $link];
    			}else{
    				$ret = ['code' => 0,'msg' => $file -> getError()];
    			}
    		}else{
    			$ret = ['code' => 0,'msg' => '未上传!','en_msg' => 'Not uploaded!'];
    		}
    	}
    	return json($ret);
    }
    
    /**
     * controller 点击保存实名认证
     */ 
    public function mod_ID_img(){
    	if(Request::instance() -> isPost()){
    		return json(model('Currency') -> modIDImg(input('post.')));
    	}
    }
    
    /**
     * controller 点击提交充值信息
     */
    public function recharge(){
    	if(Request::instance() -> isPost()){
    		return json(model('Currency') -> userRecharge(input('post.')));
    	}
    }
	
	/**
	 * controller 点击添加提币地址
	 */
	public function add_withdraw_addr(){
		if(Request::instance() -> isPost()){
			return json(model('Currency') -> addWithdrawAddr(input('post.')));
		}
	}
	
	/**
	 * controller 点击删除提币地址
	 */
	public function del_addr($id){
		return json(model('Currency') -> delAddr($id));
	}
	
	/**
	 * controller 获取提现手续费后的金额
	 */
	public function withdraw_service($cur_num){
		return json(model('Currency') -> withdrawService($cur_num));
	}
	
	/**
	 * controller 点击提现
	 */
	public function withdraw(){
		if(Request::instance() -> isPost()){
			return json(model('Currency') -> withdraw(input('post.')));
		}
	}
	
	/**
	 * controller 点击绑定手机
	 */
	public function band_tel(){
		if(Request::instance() -> isPost()){
			return json(model('Currency') -> bandTel(input('post.')));
		}
	}
	
	/**
	 * controller 点击绑定私人钱包
	 */
	public function band_wallet(){
		if(Request::instance() -> isPost()){
			return json(model('Currency') -> bandWallet(input('post.')));
		}
	}
	
	/**
	 * controller 点击绑定银行卡
	 */
	public function band_bank(){
		if(Request::instance() -> isPost()){
			return json(model('Currency') -> bandBank(input('post.')));
		}
	}
	
	/**
	 * controller 绑定支付宝
	 */
	public function band_alipay(){
		if(Request::instance() -> isPost()){
			return json(model('Currency') -> bandAlipay(input('post.')));
		}
	}
	
	/**
	 * controller 绑定支付宝
	 */
	public function band_wechat(){
		if(Request::instance() -> isPost()){
			return json(model('Currency') -> bandWechat(input('post.')));
		}
	}
	
	/**
	 * controller 点击显示消息详情
	 */
	public function msg_detail($id){
		return json(model('Currency') -> msgDetail($id));
	}
	
	/**
	 * controller 点击购买c2c订单
	 */
    public function pay()
    {
    	if(Request::instance() -> isPost()){
			return json(model('Order') -> payActive(input('post.')));
		}else{
			$this->assign('datas',model('order')->pay(input('id'),1));
	        return $this->fetch();	
		}

    }
	
	/**
	 * 点击取消购买c2c订单
	 */
    public function revokeActive($value='')
    {
    	if(Request::instance() -> isPost()){
			return json(model('Order') -> revokeActive(input('post.')));
		}
    }
    
    /**
	 * controller 点击显示已付款
	 */
    public function success_pay()
    {
    	if(Request::instance() -> isPost()){
			return json(model('Order') -> recActive(input('post.')));
		}else{
	    	$this->assign('datas',model('order')->pay(input('id'),2));
	        return $this->fetch();
	    }
    }
    
    /**
	 * controller 点击显示已完成
	 */
    public function completed_pay($id)
    {
    	$this -> assign('order',model('Order') -> complatedPay($id));
        return $this->fetch();
    }
    
    /**
     * controller 财务日志 (layui分页)
     */
    public function log_list(){
    	if(Request::instance() -> isPost()){
    		return json(model('Currency') -> logList(input('post.')));
    	}
    }
    
    /**
     * controller 委托管理(layui分页)
     */
    public function entrust_list(){
    	if(Request::instance() -> isPost()){
    		return json(model('Currency') -> entrustList(input('post.')));
    	}
    }
    
    /**
     * controller 我的成交(layui分页)
     */
    public function deal_list(){
    	if(Request::instance() -> isPost()){
    		return json(model('Currency') -> dealList(input('post.')));
    	}
    }
    
    /**
     * controller 委托历史(layui分页)
     */
    public function history_list(){
    	if(Request::instance() -> isPost()){
    		return json(model('Currency') -> historyList(input('post.')));
    	}
    }
    
    /**
     * controller 充值记录(layui分页)
     */
    public function recharge_list(){
    	if(Request::instance() -> isPost()){
    		return json(model('Currency') -> rechargeList(input('post.')));
    	}
    }
    
    /**
     * controller 提现记录(layui分页)
     */
    public function withdraw_list(){
    	if(Request::instance() -> isPost()){
    		return json(model('Currency') -> withdrawList(input('post.')));
    	}
    }
    
    /**
     * controller 系统消息 (layui分页)
     */
    public function msg_list(){
    	if(Request::instance() -> isPost()){
    		return json(model('Currency') -> msgList(input('post.')));
    	}
    }
}
