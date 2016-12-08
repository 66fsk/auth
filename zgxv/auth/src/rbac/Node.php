<?php

namespace zgxv\auth\rbac;

/**
 * Class Menu
 * @package zgxv\auth\rbac
 */
class Node{

    //树形菜单
    protected static $menuHtml = '';
    //html代码
    protected static $htmlCode = '';

    /**
     * @desc 添加菜单
     * @param $data
     * @return bool
     */
    public static function addNode($data){
        $node = new \zgxv\auth\model\Node($data);
        if($node->save()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 编辑菜单
     * @param $data
     * @param $nodeId
     * @return bool
     */
    public static function editNode($data,$nodeId){
        $node = new \zgxv\auth\model\Node();

        if($node->save($data,['id'=>$nodeId])){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 获取菜单信息
     * @param $nodeId
     * @return static
     */
    public static function getNodeInfo($nodeId){
        return \zgxv\auth\model\Node::get(['id'=>$nodeId]);
    }

    public static function getNodeIdByPath($path){
        return \zgxv\auth\model\Node::where('route_path','=',$path)->value('id');
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
    public static function getNodeList($where=[],$sort='id',$order='asc',$offset=0,$limit=10){
        $sort = empty($sort)?'id':$sort;

        $node = new \zgxv\auth\model\Node();
        if(empty($where)) {
            $nodeList = $node->order($sort . ' ' . $order)
                ->limit($offset . ',' . $limit)
                ->select();
        }else{
            $nodeList = $node->where($where)
                ->order($sort . ' ' . $order)
                ->limit($offset . ',' . $limit)
                ->select();
        }
        return $nodeList;
    }

    /**
     * @desc 获取所有权限
     */
    public static function getAllNodes(){
        return \zgxv\auth\model\Node::all();
    }

    /**
     * @desc 获取菜单总数
     * @return int
     */
    public static function getNodeCount(){
        $node = new \zgxv\auth\model\Node();
        $count = $node->count();
        return $count;
    }

    /**
     * @desc 删除目录
     * @param $where
     * @return bool
     * @throws \think\Exception
     */
    public static function delNode($where){
        $node = new \zgxv\auth\model\Node();
        if($node->where($where)->delete()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 菜单html
     * @return mixed
     */
    public static function menuHtml(){

        $menus = \zgxv\auth\model\Node::where('is_show','=',1)->order(["sort" => "desc",'id'=>'asc'])->column('*','id');

        $tree = new \zgxv\auth\Tree();
        foreach ($menus as $key => $menu) {
            $menus[$key]['level']    = $tree->get_level($menu['id'], $menus);
            $menus[$key]['width']    = 100-$menus[$key]['level'];
        }

        $tree->init($menus);
        $tree->text =[
            'detail' => "<li>
                            <a class='J_menuItem' href='\$route_path'>\$name</a>
                        </li>",
            'parent' => [
                '0' =>"<li>
                    <a href='#'>
                    <i class='fa fa-list'></i>
                        <span class='nav-label'>\$name</span>
                        <span class='fa arrow'></span>
                    </a>
                    <ul class='nav nav-second-level'>",
                '1' => " </ul>
                </li>",
            ],

        ];

        return $tree->get_authTree(0);
    }

    /**
     * @desc 节点html
     * @return mixed
     */
    public static function nodeHtml(){

        $menus = \zgxv\auth\model\Node::where('is_validate','=',1)->order(["sort" => "desc",'id'=>'asc'])->column('*','id');

        $tree = new \zgxv\auth\Tree();
        foreach ($menus as $key => $menu) {
            $menus[$key]['level']    = $tree->get_level($menu['id'], $menus);
            $menus[$key]['width']    = 100-$menus[$key]['level'];
        }

        $tree->init($menus);
        $tree->text =[
            'detail' => "<li id='\$id'>\$name</li>",
            'parent' => [
                '0' =>"<li id='\$id'>\$name<ul>",
                '1' => "</ul></li>",
            ],

        ];

        return $tree->get_authTree(0);
    }

}