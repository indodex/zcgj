<?php
namespace app\admin\model;

use app\common\model\Base;
use think\Request;
use think\Db;

class News extends Base
{
	const PATH_LIMIT = 10;	// 分页显示条数限制
	const PATH_SHOW = 10;	// 分页显示页码最大数
	
	/**
	 * model 公告列表
	 */
	public function newsList($map,$p){
		$request = Request::instance();
		
		$list = $this -> where($map) -> order('id DESC') -> page($p,self::PATH_LIMIT) -> select() -> toArray();
		foreach($list as $k => $v){
			$where['type'] = 'is_show';
			$where['value'] = $v['state'];
			$list[$k]['is_show_text'] = Db::name('dict') -> where($where) -> value('key');
			switch($v['state']){
				case 1:
					$list[$k]['is_show_btn'] = 'state_green';
					break;
				case 2:
					$list[$k]['is_show_btn'] = 'state_red';
					break;
			}
		}
		$count = $this -> where($map) -> count();
		
		$return['list'] = $list;
		$return['count'] = $count;
		$return['page'] = boot_page($count,self::PATH_LIMIT,self::PATH_SHOW,$p,$request -> action());
		
		return $return;
	}
	
	/**
	 * model 修改公告显示状态
	 */
	public function changeIsShow($data){
		if(!$data['id']){
			return ['code' => 0,'msg' => '未获取公告信息!'];
		}
		if(!$data['is_show']){
			return ['code' => 0,'msg' => '未获取公告状态!'];
		}
		
		if($data['is_show'] == 1){
			$mod['state'] = 2;
		}else{
			$mod['state'] = 1;
		}
		
		$result = $this -> where('id',$data['id']) -> update($mod);
		if($result){
			return ['code' => 1,'msg' => '修改成功!'];
		}else{
			return ['code' => 0,'msg' => '修改失败!'];
		}
	}
	
	/**
	 * model 删除公告
	 */
	public function deleteInfo($id){
		if(!$id){
			return ['code' => 0,'msg' => '未获取到要删除的新闻信息!'];
		}
		
		$del = $this -> where(array('id' => $id)) -> delete();
		if($del){
			return array('code' => 1,'msg' => '删除新闻成功!');
		}else{
			return array('code' => 0,'msg' => '删除新闻失败!');
		}
	}
	
	/**
	 * model 添加公告
	 */
	public function saveInfo($data){
		
		// 判断新闻标题是否为空
		if(!$data['title']){
			return ['status' => 0,'info' => '请填写新闻标题!'];
		}
		
		// 判断新闻内容是否为空
		if(!$data['content']){
			return ['status' => 0,'info' => '请填写新闻内容!'];
		}
		
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
		
		$News = new News;
		$result = $News -> allowfield(true) -> isUpdate($where) -> save($data);
		if($result === false){
			return ['status' => 0,'info' => $AuthGroup -> getError()];
		}else{
			return ['status' => 1,'info' => '操作成功!','url' => url('index')];
		}
	}
	
	/**
	 * model 修改公告
	 */
	public function modInfo($id){
		$info = $this -> where('id',$id) -> find();
		return $info;
	}
	
}

