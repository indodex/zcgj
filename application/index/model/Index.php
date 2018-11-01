<?php
namespace  app\index\model;
use app\common\model\Base;
use think\Request;
use think\db;
use think\Validate;
use think\Session;
class Index extends Base
{
    /**
     * model 首页数据
     */
    public function indexDatas(){
    	// 首页晃动公告
    	$notice = Db::name('page') -> where('id=4') -> field('name,en_name,content,en_content,create_time,update_time') -> find();
    	$notice['create_time'] = date('Y-m-d H:i:s',$notice['create_time']);
    	$notice['update_time'] = date('Y-m-d H:i:s',$notice['update_time']);
    	$result['notice'] = $notice;
    	
    	// 首页游戏信息
    	$game_info = Db::name('page') -> where('id=6') -> field('name,en_name,content,en_content') -> find();
    	$result['game_info'] = $game_info;
    	
    	// 首页LUCKY KEY币介绍
    	$lucky_key = Db::name('page') -> where('id=7') -> field('name,en_name,content,en_content') -> find();
    	$result['lucky_key'] = $lucky_key;
    	
    	// 友情链接
    	$links = Db::name('link') -> where('is_show=1') -> field('title,en_title,link,logo') -> order('sort ASC') -> select();
    	$result['links'] = $links;
    	
//      $banner = db('banner')->where('state',1)->order('sort desc,id asc')->select();
//      $news1 = db('news')->where('news_type',1)->order('create_time desc')->limit(5)->select();
//      $news2 = db('news')->where('news_type',2)->order('create_time desc')->limit(5)->select();
//      $news3 = db('news')->where('news_type',3)->order('create_time desc')->limit(5)->select();
//      $news4 = db('news')->where('news_type',4)->order('create_time desc')->limit(5)->select();
//      $currency = $this->getCurData();
//      $result['banner'] = $banner;
//      $result['news1'] = $news1;
//      $result['news2'] = $news2;
//      $result['news3'] = $news3;
//      $result['news4'] = $news4;
//      $result['currency'] = $currency;
//      //pre($result);
        return $result;
    }

    //获取虚拟币行情信息
    public function getCurData()
    {
        $currency = db('currency')->field('id,name,api,icon')->select();
        foreach ($currency as $k => $v) {
                $map['cur_id'] = $v['id'];
                $map['trade_status'] = 3;
                $currency[$k]['price_usd'] = db('trade')->where($map)->whereTime('end_time', 'today')->order('end_time desc')->value('price');
                $today_close_price = db('trade')->where($map)->whereTime('end_time', 'today')->order('end_time asc')->value('price');
                $yesterday_close_price = db('kline')->where('cur_id',$v['id'])->whereTime('time','yesterday')->value('close_price');
                if($yesterday_close_price != 0){
                   $currency[$k]['percent_change_24h'] = ($today_close_price - $yesterday_close_price)/$yesterday_close_price; 
                }
                if(!$currency[$k]['percent_change_24h']){
                    $currency[$k]['percent_change_24h'] = 0;
                }
            
            if(!$currency[$k]['price_usd']){
                $currency[$k]['price_usd'] = 0;
            }
        }
        return $currency;
    }

    //调用接口
    public function curl_get($url){  
        $html = file_get_contents($url); 
        $data = json_decode($html,true);
        $result['price_usd'] = $data['data']['coin']['price_usd'] * config('EXCHANGE_RATE');
        $result['percent_change_24h'] = $data['data']['coin']['percent_change_24h'];
        return $result;
     } 

   
}