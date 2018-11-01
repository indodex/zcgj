<?php
namespace app\admin\model;

use app\common\model\Base;
use think\Request;
use think\Db;
use think\Validate;

class Menu extends Base
{
	const PATH_LIMIT = 10;	// 分页显示条数限制
	const PATH_SHOW = 10;	// 分页显示页码最大数
	
	/**
	 * model 网站导航列表
	 */
	public function menuList($map,$p){
		$request = Request::instance();
		
		$list = $this -> where($map) -> order('sort ASC') -> page($p,self::PATH_LIMIT) -> select() -> toArray();
		foreach($list as $k => $v){
			// 查询显示状态
			$show['type'] = 'is_show';
			$show['value'] = $v['is_show'];
			$list[$k]['is_show_text'] = Db::name('dict') -> where($show) -> value('key');
			if($v['is_show'] == 1){
				$list[$k]['is_show_btn'] = 'is_show_green';
			}else{
				$list[$k]['is_show_btn'] = 'is_show_red';
			}
		}
		$count = $this -> where($map) -> count();
		
		$return['list'] = $list;
		$return['count'] = $count;
		$return['page'] = boot_page($count,self::PATH_LIMIT,self::PATH_SHOW,$p,$request -> action());
		
		return $return;
	}
	
	/**
	 * model 设置显示状态
	 */
	public function is_show($data){
		$id = $data['id'];
		$is_show = $data['is_show'];
		if($is_show == 1){
			$mod['is_show'] = 2;
		}else{
			$mod['is_show'] = 1;
		}
		$info = $this -> where('id',$id) -> update($mod);
		if($info){
			return array('code' => 1,'msg' => '设置成功!');
		}else{
			return array('code' => 0,'msg' => '设置失败!');
		}
	}
	
	/**
	 * model 设置排序
	 */
	public function setSort($data){
		$id = $data['id'];
		$type = $data['type'];
		
		$obj = $this -> where('id',$id) -> find();
		
		if($type == 'up'){
			$up_obj = $this -> where('sort < ' . $obj['sort']) -> order('sort DESC') -> find();
			if(!$up_obj){
				return ['code' => 0,'msg' => '已升至最高!'];
			}
			$data['sort'] = $up_obj['sort'];
			
			$sort_id = $up_obj['id'];
			$sort['sort'] = $obj['sort'];
			$sss = $this -> where('id',$sort_id) -> update($sort);
		}
		
		if($type == 'down'){
			$down_obj = $this -> where('sort > ' . $obj['sort']) -> order('sort ASC') -> find();
			if(!$down_obj){
				return ['code' => 0,'msg' => '已降至最低!'];
			}
			$data['sort'] = $down_obj['sort'];
			
			$sort_id = $down_obj['id'];
			$sort['sort'] = $obj['sort'];
			$this -> where('id',$sort_id) -> update($sort);
		}
		
		unset($data['type']);
		$result = $this -> where('id',$id) -> update($data);

		if($result){
			return ['code' => 1,'msg' => '成功!'];
		}else{
			return ['code' => 0,'msg' => '失败!'];
		}
	}
	
	/**
	 * model 删除导航
	 */
	public function deleteInfo($id){
		if(!$id){
			return ['code' => 0,'msg' => '未获取到要删除的导航信息!'];
		}
		
		$del = $this -> where(array('id' => $id)) -> delete();
		if($del){
			return ['code' => 0,'msg' => '删除成功!'];
		}else{
			return ['code' => 0,'msg' => '删除失败!'];
		}
	}
	
	/**
	 * model 添加导航
	 */
	public function saveInfo($data){
		
		// 判断规则是否为空
		if(!$data['name']){
			return ['status' => 0,'info' => '请填写规则!'];
		}
		
		
		// 通过是否传送有ID来判断添加或修改
		if(array_key_exists('id',$data)){
			$id = $data['id'];
			if(empty($id)){
				$where = false;
				$data['sort'] = time();
			}else{
				$where = true;
			}
		}else{
			$where = false;
			$data['sort'] = time();
		}
		
		$result = $this -> allowField(true) -> isUpdate($where) -> save($data);
		if($result === false){
			return ['status' => 0,'info' => '操作失败!'];
		}else{
			return ['status' => 1,'info' => '操作成功!','url' => url('index')];
		}
	}
	
	/**
	 * model 显示修改导航信息
	 */
	public function modInfo($id){
		$info = $this -> where('id',$id) -> find();
		return $info;
	}
	
	
}
