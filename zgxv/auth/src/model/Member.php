<?php

namespace zgxv\auth\model;

use think\Model;

class Member extends Model
{
    protected $table = 'bi_member';

    protected $memberRoleTable = 'bi_auth_member_role';

    public function roles(){
        return $this->belongsToMany('Role',$this->memberRoleTable);
    }
}