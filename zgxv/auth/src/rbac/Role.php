<?php

namespace zgxv\auth\rbac;
use zgxv\auth\model\RoleNode;

/**
 * Class Role
 * @package zgxv\auth\rbac
 */
class Role{

    /**
     * @desc 添加角色
     * @param $data
     * @return bool
     */
    public static function addRole($data){
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:d:s',time());
        $role = new \zgxv\auth\model\Role($data);
        if($role->save()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 编辑菜单
     * @param $data
     * @param $roleId
     * @return bool
     */
    public static function editRole($data,$roleId){
        $menu = new \zgxv\auth\model\Role();
        $data['updated_at'] = date('Y-m-d H:d:s',time());

        if($menu->save($data,['id'=>$roleId])){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 获取菜单信息
     * @param $menuId
     * @return static
     */
    public static function getRoleInfo($menuId){
        return \zgxv\auth\model\Role::get(['id'=>$menuId]);
    }

    /**
     * @desc 菜单列表，用于表格
     * @param array $where
     * @param string $sort
     * @param string $order
     * @param int $offset
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getRoleList($where=[],$sort='id',$order='asc',$offset=0,$limit=10){
        $sort = empty($sort)?'id':$sort;

        $menu = new \zgxv\auth\model\Role();
        if(empty($where)) {
            $menuList = $menu->order($sort . ' ' . $order)
                ->limit($offset . ',' . $limit)
                ->select();
        }else{
            $menuList = $menu->where($where)
                ->order($sort . ' ' . $order)
                ->limit($offset . ',' . $limit)
                ->select();
        }
        return $menuList;
    }

    /**
     * @desc 获取所有角色
     */
    public static function getAllRoles(){
        return \zgxv\auth\model\Role::all();
    }

    /**
     * @desc 获取菜单总数
     * @return int
     */
    public static function getRoleCount(){
        $menu = new \zgxv\auth\model\Role();
        $count = $menu->count();
        return $count;
    }

    /**
     * @desc 删除目录
     * @param $where
     * @return bool
     * @throws \think\Exception
     */
    public static function delRole($where){
        $menu = new \zgxv\auth\model\Role();
        if($menu->where($where)->delete()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 获取角色下所有节点
     */
    public static function getRoleAllNodes($roleId){
        return RoleNode::where('role_id','=',$roleId)->column('node_id');
    }

    /**
     * @desc 添加多个角色下节点
     * @param $data
     * @return bool
     */
    public static function addRoleNodes($data){
        $roleNode = new \zgxv\auth\model\RoleNode();
        if($roleNode->saveAll($data)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 删除角色下节点
     * @param $roleId
     * @param $nodeId
     * @return bool
     * @throws \think\Exception
     */
    public static function delRoleNodes($roleId,$nodeId){
        if(RoleNode::where('role_id','=',$roleId)->where('node_id','=',$nodeId)->delete()){
            return true;
        }else{
            return false;
        }
    }
}