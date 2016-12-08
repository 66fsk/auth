<?php

namespace zgxv\auth\rbac;
use think\helper\Hash;
use think\Request;
use zgxv\auth\model\MemberRole;
use zgxv\auth\model\Node;

/**
 * Class Member
 * @package zgxv\auth\rbac
 */
class Member{

    /**
     * @desc 添加后台人员
     * @param $data
     * @return bool
     */
    public static function addMember($data){
        $data['last_login_time'] = $data['created_at'] = $data['updated_at'] = date('Y-m-d H:d:s',time());
        $data['last_login_ip'] = Request::instance()->ip();
        $data['password'] = Hash::make($data['password']);
        $member = new \zgxv\auth\model\Member($data);

        if($member->save()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 编辑
     * @param $data
     * @param $memberId
     * @return bool
     */
    public static function editMember($data,$memberId){
        $member = new \zgxv\auth\model\Member();
        $data['password'] = Hash::make($data['password']);

        if($member->save($data,['id'=>$memberId])){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 获取菜单信息
     * @param $memberId
     * @return static
     */
    public static function getMemberInfo($memberId){
        return \zgxv\auth\model\Member::get(['id'=>$memberId]);
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
    public static function getMemberList($where=[],$sort='id',$order='asc',$offset=0,$limit=10){
        $sort = empty($sort)?'id':$sort;

        $member = new \zgxv\auth\model\Member();
        if(empty($where)) {
            $memberList = $member->order($sort . ' ' . $order)
                ->limit($offset . ',' . $limit)
                ->select();
        }else{
            $memberList = $member->where($where)
                ->order($sort . ' ' . $order)
                ->limit($offset . ',' . $limit)
                ->select();
        }
        return $memberList;
    }

    /**
     * @desc 获取菜单总数
     * @return int
     */
    public static function getMemberCount(){
        $member = new \zgxv\auth\model\Member();
        $count = $member->count();
        return $count;
    }

    /**
     * @desc 删除目录
     * @param $where
     * @return bool
     * @throws \think\Exception
     */
    public static function delMember($where){
        $member = new \zgxv\auth\model\Member();
        if($member->where($where)->delete()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 帐号权限
     * @param $memberId
     * @return mixed
     */
    public static function getMemberRole($memberId){
        $member = new \zgxv\auth\model\Member();

        $memberObj = $member->get($memberId);
        $roles = $memberObj->roles;
        return $roles;
    }

    /**
     * @desc 角色id数组
     * @param $memberId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function hasRoleIds($memberId){
        return MemberRole::where('member_id','=',$memberId)->select();
    }

    /**
     * @desc 添加角色关系
     * @param $memberId
     * @param $roleId
     * @return bool
     */
    public static function addRole($memberId,$roleId){
        $data = ['member_id'=>$memberId,'role_id'=>$roleId];
        $memberRole = new MemberRole();
        $has = $memberRole->get($data);
        if(empty($has)){
            if($memberRole->save($data)){
                return true;
            }else{
                return false;
            }
        }else{
            return $data;
        }
    }

    /**
     * @desc 删除角色
     * @param $memberId
     * @param $roleId
     * @return bool
     * @throws \think\Exception
     */
    public static function delRole($memberId,$roleId){
        $data = ['member_id'=>$memberId,'role_id'=>$roleId];
        if(MemberRole::where($data)->delete()){
            return true;
        }else{
            return false;
        }
    }

    public static function hasNodes($memberId){
        $hasRoleIds = MemberRole::where('member_id','=',$memberId)->column('role_id');
        $nodeIdArr = [];
        foreach($hasRoleIds as $roleId){
            $tmpNodeIdArr = Role::getRoleAllNodes($roleId);
            $nodeIdArr = array_merge($nodeIdArr,$tmpNodeIdArr);
        }

        //不需要验证权限节点
        $noAuthNodes = Node::where('is_validate','=','0')->column('id');
        $nodeIdArr = array_merge($nodeIdArr,$noAuthNodes);

        return $nodeIdArr;
    }

}