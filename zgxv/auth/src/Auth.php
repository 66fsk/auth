<?php

namespace zgxv\auth;

use think\Response;
use think\Session;
use zgxv\auth\rbac\Member;
use zgxv\auth\rbac\Node;

/**
 * Class Auth
 * @package zgxv\auth
 * @author gaoxiang_z@163.com
 */

class Auth{

    /**
     * @desc 权限认证配置
     * @var array
     */
    protected $config = [
        'module_init' => [
            'Acl' => ['checkLogin'],
            'Member' => ['checkAuth']
        ]
    ];

    /**
     * @desc 超级管理员
     * @var int
     */
    protected $superAdminRole = 1;

    /**
     * @desc 模块初始化标签位
     * @param $param request实例
     * @return null
     */
    public function module_init(&$param){
        $member = json_decode(Session::get('user'),true);
        $path = $param->path();

        //acl检查是否登录
        if(empty($member) && $path!='login'){
            redirect('/login')->send();
        }
        //超级管理员
        $roles = Member::getMemberRole($member['id']);
        foreach($roles as $role){
            if($role->id == $this->superAdminRole){
                return true;
                break;
            }
        }

        //现有权限
        $hasNodeIds = Member::hasNodes($member['id']);
        $id = Node::getNodeIdByPath($path);

        if(empty($id) || !in_array($id,$hasNodeIds)){
            redirect('/no-auth')->send();
        }
    }
}