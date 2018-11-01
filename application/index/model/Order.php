<?php
namespace  app\index\model;
use app\common\model\Base;
use think\Request;
use think\db;
use think\Session;
class Order extends Base
{
        /**
     * 生成抢币订单
     */
    public function createOrder($data,$buyer_id,$seller_id)
    {
        $tradeinfo = db('trade')->where('id',$data['id'])->find();
        $map['order'] = generateOrderNumber();
        $map['order_number'] = $tradeinfo['number'];
        $map['price'] = $tradeinfo['price'];
        $map['order_status'] = 3;
        $map['buyer_id'] = $buyer_id;
        $map['seller_id'] = $seller_id;
        $map['addtime'] = time();
        $map['done_time'] = time();
        $map['trade_id'] = $data['id'];
        $map['trade_type'] = $tradeinfo['trade_type'];
        $map['cur_id'] = $tradeinfo['cur_id'];
        if(db('order')->insert($map)){
            return true;
        }
        return false;
    }

    public function c2clist()
    {
        $map1['order_status'] = 1;
        $map1['trade_mold'] = 1;
        $list1 = db('order')->where('buyer_id|seller_id',session('uid'))->where($map1)->select();
        foreach ($list1 as $k => $v) {
            if($v['buyer_id'] == session('uid')){
                $list1[$k]['order_type'] = '购买USDT';
                $list1[$k]['order_type1'] = 1;
                $id = $v['seller_id'];
            }else{
                $list1[$k]['order_type'] = '卖出USDT';
                $list1[$k]['order_type1'] = 0;
                $id = $v['buyer_id'];
            }
            $list1[$k]['name'] = db('user')->where('id',$id)->value('account');
        }
        $map2['order_status'] = 2;
        $map2['trade_mold'] = 1;
        $list2 = db('order')->where('buyer_id|seller_id',session('uid'))->where($map2)->select();
        foreach ($list2 as $k => $v) {
            if($v['buyer_id'] == session('uid')){
                $list2[$k]['order_type'] = '购买USDT';
                $list2[$k]['order_type1'] = 0;
                $id = $v['seller_id'];
            }else{
                $list2[$k]['order_type'] = '卖出USDT';
                $list2[$k]['order_type1'] = 1;
                $id = $v['buyer_id'];
            }
            $list2[$k]['name'] = db('user')->where('id',$id)->value('account');
        }
        $map3['order_status'] = 3;
        $map3['trade_mold'] = 1;
        $list3 = db('order')->where('buyer_id|seller_id',session('uid'))->where($map3)->select();
        foreach ($list3 as $k => $v) {
            if($v['buyer_id'] == session('uid')){
                $list3[$k]['order_type'] = '购买USDT';
                $id = $v['seller_id'];
            }else{
                $list3[$k]['order_type'] = '卖出USDT';
                $id = $v['buyer_id'];
            }
            $list3[$k]['name'] = db('user')->where('id',$id)->value('account');
        }
        $map4['order_status'] = 4;
        $map4['trade_mold'] = 1;
        $list4 = db('order')->where('buyer_id|seller_id',session('uid'))->where($map4)->select();
        foreach ($list4 as $k => $v) {
            if($v['buyer_id'] == session('uid')){
                $list4[$k]['order_type'] = '购买USDT';
                $id = $v['seller_id'];
            }else{
                $list4[$k]['order_type'] = '卖出USDT';
                $id = $v['buyer_id'];
            }
            $list4[$k]['name'] = db('user')->where('id',$id)->value('account');
        }
        $result['list1'] = $list1;
        $result['list2'] = $list2;
        $result['list3'] = $list3;
        $result['list4'] = $list4;
        return $result;
    }
	
	/**
	 * 用户查看确认收款页
	 */
    public function pay($id,$type)
    {
        $orderinfo = db('order')->where('id',$id)->find();
        $userinfo = db('user')->where('id',$orderinfo['seller_id'])->find();
        $result['order'] = $orderinfo['order'];
        $result['order_number'] = $orderinfo['order_number'];
        $result['price'] = $orderinfo['price'];
        $result['real_name'] = $userinfo['real_name'];
        $result['bank_number'] = $userinfo['bank_number'];
        $result['bank_name'] = $userinfo['bank_name'];
        $result['wechat_accout'] = $userinfo['wechat_accout'];
        $result['alipay_accout'] = $userinfo['alipay_accout'];
        $result['id'] = $orderinfo['id'];
        if($type == 2){
            $result['voucher'] = $orderinfo['voucher'];
            $result['payment_method'] = $orderinfo['payment_method'];
        }
        return $result;
    }
	
	/**
	 * 支付订单
	 */
    public function payActive($data)
    {
        $orderinfo = db('order')->where('id',$data['id'])->find();
        if($orderinfo['order_status'] == 1){
            if($orderinfo['buyer_id'] == session('uid')){
                $map['order_status'] = 2;
                $map['voucher'] = $data['voucher'];
                $map['payment_method'] = $data['payment_method'];
                $map['pay_time'] = time();
                db('order')->where('id',$data['id'])->update($map);
                return ['status'=>1, 'info'=>'提交成功'];
            }else{
                return ['status'=>0, 'info'=>'您不提交该订单'];
            }
        }else{
            return ['status'=>0, 'info'=>'该订单不能提交'];
        }
    }
	
	/**
	 * 取消订单
	 */
    public function revokeActive($data)
    {
        $orderinfo = db('order')->where('id',$data['id'])->find();
        if($orderinfo['order_status'] == 1){
            if($orderinfo['buyer_id'] == session('uid')){
                if(db('order')->where('id',$data['id'])->update(['order_status'=>4])){	// 修改订单状态为'订单取消'
                    if(db('trade')->where('id',$orderinfo['trade_id'])->update(['trade_status'=>1])){	// 修改挂卖状态为'挂卖中'
                    	if($orderinfo['trade_type'] === 2 ){
	                    	// 返回买方支付的预定金额
	                    	$user_cur_where['uid'] = $orderinfo['seller_id'];
	                    	$user_cur_where['cur_id'] = 1;
	                    	$result = db('user_cur') -> where($user_cur_where) -> setInc('number',$orderinfo['order_number']);
	                    	if($result){
	                    		return ['status'=>1, 'info'=>'撤销成功'];
	                    	}else{
	                    		return ['status'=>0, 'info'=>'撤销失败'];
	                    	}
                    	}else{
                    		return ['status'=>1, 'info'=>'撤销成功'];
                    	}
                    }else{
                        return ['status'=>0, 'info'=>'挂单状态更改失败'];
                    }
                }else{
                    return ['status'=>0, 'info'=>'撤销失败'];
                }
            }else{
                return ['status'=>0, 'info'=>'您不撤销该订单'];
            }
        }else{
            return ['status'=>0, 'info'=>'该订单不能撤销'];
        }
    }
	
    public function recActive($data)
    {
        $orderinfo = db('order')->where('id',$data['id'])->find();
        if($orderinfo['order_status'] == 2){
            if($orderinfo['seller_id'] == session('uid')){
                $map['order_status'] = 3;
                $map['done_time'] = time();
                if(db('order')->where('id',$data['id'])->update($map)){
                    $map2['uid'] = $orderinfo['buyer_id'];
                    $map2['cur_id'] = 1;
                    if(db('user_cur')->where($map2)->setInc('number',$orderinfo['order_number'])){
                        if(db('trade')->where('id',$orderinfo['trade_id'])->update(['trade_status'=>3,'end_time'=>time(),'order_id'=>$data['id']])){
                            return ['status'=>1, 'info'=>'提交成功'];
                        }else{
                            return ['status'=>0, 'info'=>'挂单状态更改失败']; 
                        }
                    }else{
                       return ['status'=>0, 'info'=>'金额更改失败'];  
                    }
                }else{
                   return ['status'=>0, 'info'=>'订单状态更改失败']; 
                }

                
            }else{
                return ['status'=>0, 'info'=>'您不提交该订单'];
            }
        }else{
            return ['status'=>0, 'info'=>'该订单不能提交'];
        }
    }
    
    /**
     * 查看已完成订单
     */
    public function complatedPay($id){
    	$orderinfo = db('order')->where('id',$id)->find();
        $userinfo = db('user')->where('id',$orderinfo['seller_id'])->find();
        $result['order'] = $orderinfo['order'];	// 订单号
        $result['order_number'] = $orderinfo['order_number'];	// 订单数量
        $result['price'] = $orderinfo['price'];	// 订单价格
        $result['real_name'] = $userinfo['real_name'];	// 真实姓名
        $result['bank_number'] = $userinfo['bank_number'];	// 银行卡号
        $result['bank_name'] = $userinfo['bank_name'];	// 开户行
        $result['wechat_accout'] = $userinfo['wechat_accout'];	// 微信账号
        $result['alipay_accout'] = $userinfo['alipay_accout'];	// 支付宝账号
        $result['id'] = $orderinfo['id'];	// 订单ID
        $result['payment_method'] = $orderinfo['payment_method'];	// 支付方式
        $result['voucher'] = $orderinfo['voucher'];	// 支付截图
        return $result;
    }
}
