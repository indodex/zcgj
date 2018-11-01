<?php
namespace  app\admin\model;

use app\common\model\Base;
use think\Request;
use think\db;

class Shop extends Base
{

    /**
     * 更新时自动完成
     */
    protected $update = [];

    const PAGE_LIMIT = '10';	// 用户表分页限制
    const PAGE_SHOW = '10';		// 显示分页菜单数量
	
	/**
	 * model 分类列表
	 */
	public function classList(){
		$list = Db::name('goods_classify') -> select();
		foreach($list as $k => $v){
			$list[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
		}
		return $list;
	}
	
	/**
	 * model 添加/修改分类
	 */
	public function addClass($data){
		if(!$data['name']){
			return ['status' => 0,'info' => '请填写分类名!'];
		}
		
		if($data['id']){
			$id = $data['id'];
			$where = true;
		}else{
			$where = false;
			$data['create_time'] = time();
		}
		unset($data['id']);
		
		if($where){
			$result = Db::name('goods_classify') -> where('id',$id) -> update(['name' => $data['name']]);
		}else{
			$result = Db::name('goods_classify') -> insert($data);
		}
		
		if(!$result){
			return ['status' => 0,'info' => '保存失败！'];
		}else{
			return ['status' => 1,'info' => '保存成功！','url' => url('index')];
		}
	}
	
	/**
	 * model 删除分类
	 */
	public function deleteClass($id){
		if(!$id){
			return ['code' => 0,'msg' => '未获取分类信息！'];
		}
		$result = Db::name('goods_classify') -> where('id',$id) -> delete();
		if($result){
			return ['code' => 1,'msg' => '删除分类成功！'];
		}else{
			return ['code' => 0,'msg' => '删除分类失败！'];
		}
	}
	
	/**
	 * model 获取当前登陆店铺信息
	 */
	public function get_shop($aid){
		// 获取当前登陆管理员信息绑定的前台用户ID
		$uid = Db::name('admin') -> where('id',$aid) -> value('id');
		// 通过前台用户ID查询店铺ID
		$shop_id = Db::name('user_shop') -> where('uid',$uid) -> value('id');
		return $shop_id;
	}
	
    /**
     * model 优惠专区
     */
    public function preferential($map,$p){
    	
    	$list = Db::name('goods') -> alias('g') -> join('goods_detail d','g.id=d.gid') -> field('g.*,d.*,d.id as did') -> select();
    	$count = Db::name('goods') -> where($map) -> count();
    	$request= Request::instance();
    	$page = boot_page($count, self::PAGE_LIMIT, self::PAGE_SHOW, $p,$request -> action());
    	foreach($list as $k => $v){
    		// 商品分类
    		$list[$k]['class_name'] = Db::name('goods_classify') -> where('id',$v['class_id']) -> value('name');
    		
    		// 日期
    		$list[$k]['create_date'] = date('Y-m-d H:i:s',$v['create_time']);
    	}
    	
    	$return['list'] = $list;
    	$return['count'] = $count;
    	$return['page'] = $page;
    	return $return;
    }
    
    /**
     * model 向优惠专区添加商品
     */
    public function addPreferentialGoods($data){
    	
      	 if(!$data['shop_id']){
      	 	return ['code' => 0,'msg' => '未获取店铺信息!'];
      	 }
      	 if(!$data['class_id']){
      	 	return ['code' => 0,'msg' => '请选择商品分类!'];
      	 }
      	 if(!$data['name']){
      	 	return ['code' => 0,'msg' => '请输入商品名!'];
      	 }
      	 if(!$data['price']){
      	 	return ['code' => 0,'msg' => '请输入商品优惠价!'];
      	 }
      	 if(!$data['original_price']){
      	 	return ['code' => 0,'msg' => '请输入商品原价!'];
      	 }
      	 if(!$data['number']){
      	 	return ['code' => 0,'msg' => '请输入商品存库!'];
      	 }
      	 if(!$data['brand']){
      	 	return ['code' => 0,'msg' => '请输入商品品牌!'];
      	 }
      	 if(!$data['type']){
      	 	return ['code' => 0,'msg' => '请输入规格型号!'];
      	 }
      	 if(!$data['tel']){
      	 	return ['code' => 0,'msg' => '请输入联系方式!'];
      	 }
      	 if(!$data['picture']){
      	 	return ['code' => 0,'msg' => '请上传商品轮播图!'];
      	 }
      	 if(!$data['detail_pic']){
      	 	return ['code' => 0,'msg' => '请上传详情图!'];
      	 }
      	 if(!$data['service_pic']){
      	 	return ['code' => 0,'msg' => '请上传专享服务图!'];
      	 }
    	
    	if(!$data['id']){	// 插入数据
    		Db::startTrans();
    		$condition = 0;
    		try{
    			// 插入商品信息表
	    		$in_goods['area_type'] = 1;
	    		$in_goods['class_id'] = $data['class_id'];
	    		$in_goods['shop_id'] = $data['shop_id'];
	    		$in_goods['create_time'] = time();
	    		$goods_id = Db::name('goods') -> insertGetId($in_goods);
	    		
	    		// 插入商品详情表
	    		unset($data['class_id']);
	    		unset($data['shop_id']);
	    		$data['gid'] = $goods_id;
	    		$data['create_time'] = time();
	    		if($data['picture']){
	    			$pics_num = count($data['picture']);
	    			for($i=0;$i<$pics_num;$i++){
	    				$picture .= $data['picture'][$i].';';
	    			}
	    			$data['picture'] = trim($picture,';');
	    		}
	    		Db::name('goods_detail') -> insert($data);
	    		
    			$condition = 1;
    			Db::commit();
    		}catch(\exception $e){
    			Db::rollback();
    		}
    	}else{	// 修改数据
    		// 修改商品详情表
    		$id = $data['id'];
    		unset($data['id']);
    		unset($data['class_id']);
    		unset($data['shop_id']);
    		$result = Db::name('goods_detail') -> update($data);
    	}
    	
    	if($condition === 1 || $result){
    		return ['code' => 1,'msg' => '执行成功!','url' => url('preferential')];
    	}else{
    		return ['code' => 0,'msg' => '执行失败!'];
    	}
    }
    
    /**
     * model 特色专区
     */
    public function feature(){
    	
    }
    
}