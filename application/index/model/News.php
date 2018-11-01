<?php
namespace  app\index\model;
use app\common\model\Base;
use think\Request;
use think\db;
use think\Validate;
use think\Session;
class News extends Base
{
   const PAGE_LIMIT = '6';//用户表分页限制
   const PAGE_SHOW = '4';//显示分页菜单数量

    /**
     * model 公告列表
     */
    public function newsList($p){
        $request= Request::instance();
        $list = $this -> where('state=1') -> order('create_time DESC') -> page($p, self::PAGE_LIMIT) -> select();
        $count = $this -> where('state=1') -> count();
        $page = boot_page($count, self::PAGE_LIMIT, self::PAGE_SHOW, $p,$request -> action());
        
        $return['list'] = $list;
        $return['count'] = $count;
        $return['page'] = $page;
        return $return;
    }

    /**
     * model 新闻详情
     */
    public function newsInfo($id)
    {
        $info = $this -> where('id',$id) -> find();
        return $info;
    }
    
    /**
     * model LUCKY KEY 团队
     */
    public function teamInfo(){
    	$info = Db::name('page') -> where('id=8') -> field('name,en_name,content,en_content,create_time,update_time') -> find();
    	if(isset($info['update_time'])){
    		$info['date'] = date('Y-m-d H:i:s',$info['update_time']);
    	}else{
    		$info['date'] = date('Y-m-d H:i:s',$info['create_time']);
    	}
    	return $info;
    }
   
}