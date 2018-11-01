<?php
namespace  app\index\model;
use app\common\model\Base;
use think\Request;
use think\db;
use think\Session;
class Market extends Base
{
	/**
	 * model 关于我们
	 */
    public function aboutUs()
    {
        $about = Db::name('page') -> where('id=2') -> find();
        return $about;
    }
}
