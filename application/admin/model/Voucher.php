<?php
namespace app\admin\model;

use app\common\model\Base;
use think\Request;
use think\Db;

class Voucher extends Base
{
	
	const PATH_LIMIT = 10;	// 分页显示条数限制
	const PATH_SHOW = 10;	// 分页显示页码最大数
	
	/**
	 * model 券列表
	 */
	public function voucherList($map,$p){
		$request = Request::instance();
		$list = $this -> where($map) -> order('id DESC') -> page($p,self::PATH_LIMIT) -> select() -> toArray();
		$count = $this -> where($map) -> count();
		foreach($list as $k => $v){
			switch($v['is_sell']){
				case 1:
					$list[$k]['is_sell_text'] = '可购买';
					$list[$k]['is_sell_color'] = 'is_green';
					break;
				case 2:
					$list[$k]['is_sell_text'] = '不可购买';
					$list[$k]['is_sell_color'] = 'is_red';
					break;
			}
		}
		
		$return['list'] = $list;
		$return['count'] = $count;
		$return['page'] = boot_page($count,self::PATH_LIMIT,self::PATH_SHOW,$p,$request -> action());
		
		return $return;
	}
	
	/**
	 * model 新增/修改券
	 */
	public function saveInfo($data){
    	
    	// 判断币名称是否为空
		if(!$data['name']){
			return array('status' => 0,'info' => '请填写券称!');
		}
    	
    	// 通过是否传送有id来判断添加或修改
    	if(array_key_exists('id',$data)){
    		$id = $data['id'];
    		if(empty($id)){
    			if($this -> addVur($data) === false){
    				return ['status' => 0,'info' => '添加失败!'];
    			}else{
    				return ['status' => 1,'info' => '添加成功!','url' => url('index')];
    			}
    		}else{
    			$where = true;
    			$Voucher = new Voucher;
    			$result = $Voucher -> allowField(true) -> isUpdate($where) -> save($data);
    		}
    	}else{
    		if($this -> addVur($data) === false){
				return ['status' => 0,'info' => '添加失败!'];
			}else{
				return ['status' => 1,'info' => '添加成功!','url' => url('index')];
			}
    	}
    	
    	if($result === false){
            return ['status' => 0,'info' => $AuthGroup -> getError()];
		}else{
			return array('status' => 1,'info' => '保存成功', 'url' => url('index'));
		}
    }
    
    // 添加券
    protected function addVur($data){
    	$data['create_time'] = time();
    	$vid = $this -> insertGetId($data);
    	
    	// 在 user_vou 表中添加数据
    	$condition = 0;
    	Db::startTrans();
    	try{
    		$uid = Db::name('user') -> field('id') -> select();
    		foreach($uid as $k => $v){
    			$in['uid'] = $v['id'];
    			$in['vid'] = $vid;
    			Db::name('user_vou') -> insert($in);
    		}
    		
    		$condition = 1;
    		Db::commit();
    	}catch(\Exception $e){
    		Db::rollback();
    	}
    	
		if($condition === 0){
			return false;
		}else{
			return true;
		}
   }
    
    /**
     * model 显示修改券信息
     */
    public function modInfo($id){
    	$info = $this -> where('id',$id) -> find();
    	return $info;
    }
	
	/**
	 * model 删除券
	 */
	public function deleteInfo($id){
		if(!$id){
			return ['code' => 0,'msg' => '未获取到要删除的券信息!'];
		}
		
		$condition = 0;
		try{
			$this -> where(array('id' => $id)) -> delete();
			
			// 删除 user_vou 表中的券信息
			Db::name('user_vou') -> where('vid',$id) -> delete();
			
			Db::commit();
			$condition = 1;
		}catch(\PDOException $e){
			Db::rollback();
		}
			
		if($condition = 1){
			return ['code' => 1,'msg' => '删除券成功!'];
		}else{
			return ['code' => 0,'msg' => '删除券失败,请重试!'];
		}
	}
	
	
}
