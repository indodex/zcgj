<?php
namespace app\index\controller;

use app\common\controller\Base;
use think\Request;
use think\Session;
use think\Db;

class C2c extends Base
{
	/**
	 * controller c2c首页
	 */
	public function index(){
		if (Request::instance()->isPost()) {
            return model('Trade')->buySellEth(input('post.'));
        } else {
			$map['uid'] = session('uid');
			$map['cur_id'] = 1;
			$user_eth = sprintf('%.4f',db('user_cur')->where($map)->value('number'));
			if(!$user_eth){
				$user_eth = 0;
			}
			$this->assign('user_eth',$user_eth);
			$this->assign('list',model('C2c')->buySellList());
			return $this -> fetch();
		}
	}

	public function transactionActive()
    {
        if (Request::instance()->isPost()) {
        return model('C2c')->transactionActive(input('post.'));
      }
    }
    
    /**
     * 自动修改订单状态
     */
    public function mod_order(){
    	$half_hour = time() - 60 * 30;	// 半个小时前
    	// 查询订单
    	$order_where['order_status'] = 1;
    	$order_where['trade_type'] = 2;
    	$order = Db::name('order') -> where($order_where) -> whereTime('addtime','<',$half_hour) -> select();
    	// 买家半小时未打款 取消订单
    	foreach($order as $k => $v){
    		// 取消C2C订单
    		Db::name('order')->where('id',$v['id'])->update(['order_status'=>4]);
    		Db::name('trade')->where('id',$v['trade_id'])->update(['trade_status'=>1]);
        	// 返回买方支付的预定金额
        	$user_cur_where['uid'] = $v['seller_id'];
        	$user_cur_where['cur_id'] = 1;
        	$result = db('user_cur') -> where($user_cur_where) -> setInc('number',$v['order_number']);
    	}
    	echo '成功';
    }
}
