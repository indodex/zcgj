<?php
namespace app\index\controller;

use app\common\controller\Base;
use think\Request;
use think\Session;
use think\Db;

class Trade extends Base
{
    /**
     * 买入页面
     */
    public function Index(){
        $this->assign('curdatas',model('Trade')->getCurData());
        return $this -> fetch();
//    }
    }

     /**
     * 交易页面
     */
    public function sell()
    {        
        if (Request::instance()->isPost()) {
            return model('Trade')->buySell(input('post.'));
        } else {
            $id = input('id');
            $this -> assign('cur_id',$id);
            $this -> assign('cur_name',db('currency') -> where('id',$id) -> value('name'));
            $this->assign('list',model('Currency')->currencyInfo($id));
            $this->assign('tradelist',model('Currency')->tradelist($id));
            
            return $this->fetch();
        }
    }
	
	/**
	 * 币种行情/我的usdt币/我的pea币
	 */
	public function my_cur_detail(){
		return json(model('Currency') -> currencyInfo(input('post.cur_id')));
	}
	
	/**
	 * 我的最新成交/我的委托
	 */
	public function tradelist(){
		return json(model('Currency') -> tradelist(input('post.cur_id')));
	}
	
	/**
	 * K线图
	 */
	public function kline(){
		$cur_id = input('cur_id');
		$range = input('range');
		//$this -> kLineGraph();
		return json(model('Trade') -> klineList($cur_id,$range));
	}
	
	/**
	 * 趋势图详情
	 */
	public function detail(){
		$id = input('id');
		$this -> assign('cur_id',$id);	// 曲线图币种信息
		$this -> assign('list',model('Currency') -> currencyInfo($id));	// 行情
//		$this -> assign('new_deal',model('Trade') -> newDeal());	// 最新成交
//		$this -> assign('new_deal2',model('Trade') -> newDeal2());	// 最新成交(区分求购/出售)
		return $this -> fetch();
	}
	
	/**
	 * 详情左最新成交
	 */
	public function new_deal(){
		return json(model('Trade') -> newDeal(input('post.cur_id')));	// 最新成交
	}
	
	/**
	 * 详情右最新成交
	 */
	public function new_deal2(){
		return json(model('Trade') -> newDeal2(input('post.cur_id')));	// 最新成交(区分求购/出售)
	}
	
	/**
	 * 委托信息
	 */
	public function all_trade(){
		return json(model('Trade') -> allTrade(input('post.cur_id')));
	}
	
     /**
     * 我的委托
     */
    public function entrust()
    {
      if (Request::instance()->isPost()) {
        return model('Trade')->revoke(input('post.'));
      }
    }

    public function transactionActive()
    {
        if (Request::instance()->isPost()) {
        return model('Trade')->transactionActive(input('post.'));
      }
    }

     /**
     * 我的成交
     */
    public function deal()
    {
        if (Request::instance()->isPost()) {
            return model('Trade')->orderPayment(input('post.'));
        } else {
            $this->assign('datas',model('Trade')->dealPage($id));
            return $this->fetch();
        }
    }

    //确认收款
    public function receive()
    {
        if (Request::instance()->isPost()) {
            return model('Trade')->receive(input('post.'));
        }
    }
    
    
    
    /**
     * K线图生成
     */
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
	
	// 执行生成K线图生成
	public function kLineGraph(){
        $curData = db('currency')->where('id','neq',1)->select();
        // 判断可执行交易时间域
        $time_zone = $this -> get_curr_time_section();
        foreach ($curData as $k => $v) {
            // 查询K线图表中在4秒内是否存在生成数据,如果存在则不生成新数据
//          $past_time = time() - 4;
//          $time = time();
//          $exist_data = Db::name('kline') -> whereTime('time','between',[$past_time,$time]) -> find();
			$time = time();
			$exist_data = Db::name('kline') ->where('cur_id',$v['id'])-> where('time',$time) -> find();
            if(!$exist_data){	// 判断4秒内是否有数据
	            if($time_zone === true || empty(config('START_TRADE')) || empty(config('END_TRADE'))){	// 判断是否开启交易
	            	// 查询今天最后一笔交易
	            	$trade_where['trade_status'] = 3;
	                $trade_where['trade_mold'] = 0;
	                $trade_where['cur_id'] = $v['id'];
	                $open_trade = db('trade')->where($trade_where)->whereTime('end_time', 'today')->order('end_time DESC , start_time DESC')->find();
	                if($open_trade){
	                	// 获取K线图数据中最后一条记录的 收盘价
	                    $up_kline = db('kline')->where('cur_id',$v['id'])->order('time desc')->value('close_price');
	                    // 判断获取开盘价
	                    if($up_kline == 0){
	//                      $data['open_price'] = db('trade')->where($trade_where)->whereTime('end_time', 'today')->order('end_time asc')->value('price');
	                        // 如果最后一笔交易为0的话:
	                    	// 如果为单条数据则显示最后一条数据的价格,如果为多条数据则显示最后一笔交易的上一笔交易价格(实际成交价)
	                        $data['open_price'] = db('trade')->where($trade_where)->where('order_id',$open_trade['order_id'])->whereTime('end_time', 'today')->order('id asc')->value('price');
	                    }else{
	                        $data['open_price'] = $up_kline;
	                    }
	                    // 获取收盘价
	                    $order_where['order_status'] = 3;
	                    $order_where['trade_mold'] = 0;
	                	$order_where['cur_id'] = $v['id'];
	                    $data['close_price'] = db('order')->where($order_where)->whereTime('done_time', 'today')->order('id DESC')->value('price');
	                    // 获取最大价
	                    $data['max_price'] = db('trade')->where($trade_where)->whereTime('end_time','today')->max('price');
	                    // 获取最小价
	                    $data['min_price'] = db('trade')->where($trade_where)->whereTime('end_time','today')->min('price');
	                    // 获取最近1分钟之内的交易量
	//                  $avg = db('trade')->where($trade_where)->whereTime('end_time', time())->avg('price');
						$time_up = time() - 60;
						$time_down = time();
	                    // 判断是否有最新交易,如果有最新交易则 vol 为1分钟之前的记录,如果没有 vol 为0
	                    $last_trade = db('trade')->where($trade_where)->whereTime('end_time','between', [$past_time,$time])->find();
	                    if($last_trade){
	                    	$count = Db::name('order') -> where($order_where) -> whereTime('done_time','between',[$time_up,$time_down]) -> sum('order_number');
	                    }else{
	                    	$count = 0;
	                    }
	                    
	                    $data['vol'] = $count;
	                    $data['time'] = time();
	                    $data['cur_id'] = $v['id'];
	                }else{
	                    $data['open_price'] = 0;
	                    $data['close_price'] = 0;
	                    $data['max_price'] = 0;
	                    $data['min_price'] = 0;
	                    $data['vol'] = 0;
	                    $data['cur_id'] = $v['id'];
	                    $data['time'] = time();
	                }
	            }else{
	                $data['open_price'] = 0;
	                $data['close_price'] = 0;
	                $data['max_price'] = 0;
	                $data['min_price'] = 0;
	                $data['vol'] = 0;
	                $data['cur_id'] = $v['id'];
	                $data['time'] = time();
	            }
	            db('kline')->insert($data);
            }
        }
    }
    
    
}
