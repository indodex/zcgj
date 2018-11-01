<?php
namespace app\admin\model;

use app\common\model\Base;
use think\Request;
use think\Db;
use think\Validate;
use think\Model;

class Page extends Model
{
	const PATH_LIMIT = 10;	// 分页显示条数限制
	const PATH_SHOW = 10;	// 分页显示页码最大数
	
	/**
	 * model 网站单页列表
	 */
	public function pageList($map,$p){
		$request = Request::instance();
		
		$list = $this -> where($map) -> order('create_time DESC') -> select();
		$count = $this -> where($map) -> count();
		
		$return['list'] = $list;
		$return['count'] = $count;
		$return['page'] = boot_page($count,self::PATH_LIMIT,self::PATH_SHOW,$p,$request -> action());
		return $return;
	}
	
	/**
	 * model 添加单页
	 */
	public function addPage($data){
		// 通过是否传送有ID来判断添加或修改
		if(array_key_exists('id',$data)){
			$id = $data['id'];
			if(empty($id)){
				$where = false;
			}else{
				$where = true;
			}
		}else{
			$where = false;
		}
		
		$Page = new Page;
		$result = $Page -> allowField(true) -> validate(true) -> isUpdate($where) -> save($data);
		if($result){
			return ['code' => 1,'msg' => '操作成功!','url' => url('page')];
		}else{
			return ['code' => 0,'msg' => $Page -> getError()];
		}
	}
	
	/**
	 * model 显示修改单页
	 */
	public function modPage($id){
		$info = $this -> where('id',$id) -> find() -> toArray();
		return $info;
	}
	
	/**
	 * model 删除显示首页
	 */
	public function deletePage($id){
		$del = $this ->where('id',$id) -> delete();
		if($del){
			return ['code' => 1,'msg' => '删除成功!'];
		}else{
			return ['code' => 0,'msg' => '删除失败!'];
		}
	}
}
