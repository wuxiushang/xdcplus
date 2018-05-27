<?php

namespace Custom\Acl;

class Permission
{
    public function hasPermission($menuId)
    {
       return true;
    }
    public function hasPermission2($permits)
    {
        // 只要有一个有权限，就可以进入这个请求
        if(!is_null($permits)&&is_array($permits)){
            foreach ($permits as $permit) {
                if ($permit == '*') {
                    return true;
                }
                $roleIds=session('clientRoleIds');
                $permissions=config('xdcpermission.'.$permit);
                if(!empty($roleIds)&&!empty($permissions)&&array_intersect($permissions,$roleIds)){
                    return true;
                }
            }
        }
        return false;
    }

    public function hasResAdmin()
    {
        $roleIds = session('clientRoleIds');
        if (array_intersect([1], $roleIds)) {
            return true;
        } else {
            return false;
        }
    }

    public function hasClientAdmin($clientId)
    {

    }
    public function canCreateKpi(){
        return true;
    }
    public function canAuditKpi(){
        return true;
    }
}