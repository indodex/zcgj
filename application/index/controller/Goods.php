<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 14:36
 */

namespace app\index\controller;


use app\common\controller\Base;
use app\index\model\GoodsClassify;
use think\Db;
use app\index\model\GoodsDetail;
use PHPMailer\PHPMailer\Exception;

class Goods extends Base
{
    /**
     *
     * 返回商品类别信息
     * @return false|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_goods_type()
    {
        try{
            $goods_classify = Db::name('goods_classify')->select();
            $r = msg_handle('商品信息表',1,$goods_classify);
        }catch (\Exception $e){
            $r = msg_handle("获取商品类型失败",-1);
        }
        return json_encode($r);
    }
    /**
     *
     * 根据商品类别，分页显示商品信息
     * @param int $cid
     * @param int $page_size
     * @throws \think\exception\DbException
     */
    public function get_goods_by_type($cid = 1,$page_size = 10)
    {
        try{
            $goods = Db::name('goods_detail')->where(['cid'=> $cid,])->paginate($page_size);
            $pages = $goods->render();
            $goods_array = [
                'goods'=>$goods,
                'page'=>$pages,
            ];
            $r = msg_handle('商品信息',1,$goods_array);
        }catch (\Exception $e){
            $r = msg_handle("获取商品信息失败",-1);
        }
        return json_encode($r);
    }

    /**
     *
     * 按照商品id查询商品详细信息，返回json
     * @param int $gid
     * @return false|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function goods_detail($gid=1)
    {
        try{
            $good_detail = Db::name('goods_detail')->where(['gid'=>$gid])->find();
            $r = msg_handle('商品信息',1,$good_detail);
        }catch (\Exception $e){
            $r = msg_handle("获取商品信息失败",-1);
        }
        return json_encode($r);
    }

    /**
     * 返回优惠专区信息
     * @return false|string
     */
    public function preferential($area_type = 1)
    {
        try{
            $preferential_goods = Db::name('goods')->where(['area_type'=>$area_type])->select();
            $r = msg_handle('优惠专区',1,$preferential_goods);
        }catch (\Exception $e){
            $r = msg_handle("获取优惠专区失败！",-1);
        }
        return json_encode($r);
    }
}