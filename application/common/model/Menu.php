<?php
namespace app\common\model;
use app\common\model\Base;
use think\Model;
class Menu extends Base
{
    const RULE_URL = 1;
    const RULE_MAIN = 2;
	
	/**
     * 权限菜单-超管
     */
    public function ruleMap()
    {

        $list = $this->where('is_show',1)->field('id,name,title')->order('sort ASC,id ASC')->select()->toArray();
        return $list;
    }
    
    /**
     * 是否有qrcode
     */
    public function isImg(){
    	$qq = db('config') -> where('key','QQ_QRCODE') -> value('value');
    	if(!$qq){
    		$qq_img = 2;
    	}else{
    		$qq_img = 1;
    	}
    	$qq_group = db('config') -> where('key','QQ_GROUP_QRCODE') -> value('value');
    	if(!$qq_group){
    		$qq_group_img = 2;
    	}else{
    		$qq_group_img = 1;
    	}
    	$wechat = db('config') -> where('key','WECHAT_QRCODE') -> value('value');
    	if(!$wechat){
    		$wechat_img = 2;
    	}else{
    		$wechat_img = 1;
    	}
    	$wechat_group = db('config') -> where('key','WECHAT_GROUP_QRCODE') -> value('value');
    	if(!$wechat_group){
    		$wechat_group_img = 2;
    	}else{
    		$wechat_group_img = 1;
    	}
    	
    	$return['qq_img'] = $qq_img;
    	$return['qq_group_img'] = $qq_group_img;
    	$return['wechat_img'] = $wechat_img;
    	$return['wechat_group_img'] = $wechat_group_img;
    	return $return;
    }
}