<?php
// 常量定义
const WEB_VERSION = '1.0.170303';

/*
 * 二位数组取最大值最小值pv
 */
function getArrayMax($arr,$field,$type)
{
    foreach ($arr as $k=>$v){
        $temp[]=$v[$field];
    }
    if($type == 1){
      return max($temp);
    }else{
      return min($temp);
    }
    
}

/*
 * 截取字符串
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
  if(function_exists("mb_substr")){
    if($suffix)
       return mb_substr($str, $start, $length, $charset)."...";
    else
       return mb_substr($str, $start, $length, $charset);
  }
  elseif(function_exists('iconv_substr')) {
    if($suffix)
       return iconv_substr($str,$start,$length,$charset)."...";
    else
       return iconv_substr($str,$start,$length,$charset);
  }
  $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
  $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
  $re['gbk']  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
  $re['big5']  = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
  preg_match_all($re[$charset], $str, $match);
  $slice = join("",array_slice($match[0], $start, $length));
  if($suffix) return $slice."…";
  return $slice;
}
function week_slot($sdefaultDate){
         //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期  
         $first=3;  
         //获取当前周的第几天 周日是 0 周一到周六是 1 - 6  
         $w=date('w',strtotime($sdefaultDate));  
         //获取本周开始日期，如果$w是0，则表示周日，减去 6 天  
         $week_start=date('Y-m-d',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days'));  
         $week_start1=date('m.d',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days'));  
         //本周结束日期  
         $week_end=date('m.d',strtotime("$week_start +6 days"));
         $data['a'] = $week_start1.'-'.$week_end;
         $data['b'] = strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days');  
         $data['c'] =strtotime("$week_start +6 days");
         return $data;
}
//判断银行卡
 function check_bankCard($card_number){
        $arr_no = str_split($card_number);
        $last_n = $arr_no[count($arr_no)-1];
        krsort($arr_no);
        $i = 1;
        $total = 0;
        foreach ($arr_no as $n){
            if($i%2==0){
                $ix = $n*2;
                if($ix>=10){
                    $nx = 1 + ($ix % 10);
                    $total += $nx;
                }else{
                    $total += $ix;
                }
            }else{
                $total += $n;
            }
            $i++;
        }
        $total -= $last_n;
        $x = 10 - ($total % 10);
        if($x == $last_n){
            return 'true';
        }else{
            return 'false';
        }
    }
//计算时间
function format_date($time){
    $t=time()-$time;
    $f=array(
        '31536000'=>'年',
        '2592000'=>'个月',
        '604800'=>'星期',
        '86400'=>'天',
        '3600'=>'小时',
        '60'=>'分钟',
        '1'=>'秒'
    );
    foreach ($f as $k=>$v)    {
        if (0 !=$c=floor($t/(int)$k)) {
            return $c.$v.'前';
        }
    }
}
/**
*自动生成钱包地址
*/
function get_hash(){
  $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()+-';
  $random = $chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)];//Random 5 times
  $content = uniqid().$random;  // 类似 5443e09c27bf4aB4uT
  return sha1($content); 
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login()
{
    $uid = session('uid');
    if (empty($uid) or $uid < 1) {
        echo '<script>alert("请先登陆!");window.location.href="/index/Publics/login"</script>';
    } else {
        return $uid;
    }
}

/**
 * 获取当前页面URL
 * @return string 返回url
 */
function this_action()
{
    parse_str($_SERVER['QUERY_STRING'], $arr);
    unset($arr['p']);
    unset($arr['s']);
    if ($arr) {
        return http_build_query($arr) . '&';
    } else {
        return false;
    }
}

/**
 * boot分页函数
 * @param string $count , $item, $list, $p ,$action 总页数， 一页多少个， 显示多少个分页, 当前页数
 * @return string 返回分页字符串
 */
function boot_page($count, $item, $list, $p,$action)
{// 最大页数
  //echo($action);exit;
    $max = ceil($count / $item);
    if ($max <= 1) {
        $page = "";
    } else {// 首页
        $page = '<li><a href="?&p=1" class="bac_red">首页</a></li>';
        if($p != 1){
          $page .= '<li><a href="?&p='.($p-1).'">上一页</a></li>';
        }
        // 显示的第一个
        $start = $p - floor($list / 2);
        if ($start <= 0) {
            $start = 1;
        }
        // 显示的最后一个
        $stop = $p + floor($list / 2);
        if ($stop > $max) {
            $stop = $max;
        }
        for ($i = $start; $i <= $stop; $i++) {
            if ($i == $p) {
                // 选中当前页
                $page .= '<li><a class="bac_red">' . $i . '</a></li>';
            } else {
                $page .= '<li ><a href="?p=' . $i . '">' . $i . '</a></li>';
            }
        }
        if($p != $max){
          $page .= '<li><a href="?&p='.($p+1).'">下一页</a></li>';
        }
        // 末页
        $page .= '<li ><a href="?p=' . $max . '" class="bac_red">末页</a></li>';
        $page .= '<li class="page-all"><a class="bac_red">共'.$max.'页</a></li>';
    }
    return $page;
}