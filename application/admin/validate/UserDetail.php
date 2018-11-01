<?php
namespace app\admin\validate;
use think\Validate;
class Userdetail extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [    	'real_name'	=>	'require',
    	'identity'	=>	'require',
    	'tel'		=>	'require',
        'status'	=>	'require',
    ];
    /**
     * 提示消息
     */
    protected $message = [
        'real_name.require'	=>	'请输入真实姓名',
        'identity.require'	=>	'请输入证件号码',
        'tel.require'		=>	'请输入手机号',
        'status.require'	=>	'请选择用户状态',
    ];
        
}
