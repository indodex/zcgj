<?php
namespace app\index\controller;
use app\common\controller\Base;
use think\Request;
use think\Session;
class Index extends Base
{   

    /**
     * controller 首页
     */
    public function index(){   
        $this -> assign('index',model('Index') -> indexDatas());
        // 币种交易
        $this->assign('curdatas',model('Trade')->getCurData());
        return $this -> fetch();
    }
    
    
    
    
    
    
    
    
    //查看更多
    public function more($p=1)
    {
        $news_type = input('news_type');
        $newsTypeArr = model('Common/Dict')->showKey('news_type');
        $this->assign('news_type',$newsTypeArr[$news_type]);
        $this->assign('list',model('News')->more($news_type,$p));
        $this->assign('currency',model('Index')->getCurData());
        $this->assign('pagename','查看更多');
        return $this->fetch();  
    }

    //新闻详情
    public function newsDetail()
    {
        $id = input('id');
        $news_type = db('news')->where('id',$id)->value('news_type');
        $newsTypeArr = model('Common/Dict')->showKey('news_type');
        $this->assign('news_type',$newsTypeArr[$news_type]);
        $this->assign('news_type_id',$news_type);
        $this->assign('newsinfo',model('News')->newsDetail($id));
        $this->assign('newest_news',db('news')->where('news_type',$news_type)->order('create_time desc')->limit(8)->select());
        return $this->fetch();
    }

    //币种交易
    public function currencyTrans()
    {
        if (Request::instance()->isPost()) {
            return model('Trade')->buySell(input('post.'));
        } else {
            $id = input('id');
            $this->assign('list',model('Currency')->currencyInfo($id));
            return $this->fetch();
        }
    }

    //K线图
    public function klinegraph()
    {
        $id = input('id');
        return json(model('Currency')->klinegraph($id));
    }

        public function bb()
    {
        $time = 5;
        $create_time = db('kline')->where('cur_id',1)->order('time desc')->value('time');
        for ($i=0; $i < 5 ; $i++) { 
            $start_time = $create_time - $i * 60 * $time;
            $end_time =$start_time - 60 * $time;
            $map['cur_id'] = 1;
            $data = db('kline')->where($map)->where('time','between',"$end_time,$start_time")->select();
            // foreach ($data as $key => $value) {
            //     # code...
            // }
            $info = [];
            // foreach ($data as $kk => $vv) {
            //     $
            // }
            foreach ($data as $k => $v) {
                $info[] = $start_time;
                if($k == 0){
                    $info[] = $v['open_price'];
                }
                $info[] = getArrayMax($data,'max_price',1);
                $info[] = getArrayMax($data,'min_price',2);
                if($k == 0){
                    $info[] = $v['close_price'];
                }
                $result[$k][4] = (float)$v['close_price'];
                $result[$k][5] = (float)$v['vol'];
            }
             pre($data);
        }
       
    }
}