<?php

namespace App\Repositories;

use App\Models\Role;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository as EloquentBaseRepository;
use App\Models\Permission;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Hashids\Hashids;

/**
 * Class AdminRepository
 * @package App\Repositories
 */
class PermissionRepository extends EloquentBaseRepository implements RepositoryInterface
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Permission::class;
    }

    protected $roleRepository;

    public function __construct(\App\Repositories\RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param $data
     */
    public function createMultiplePermission($data)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        DB::transaction(function () use ($data) {
            Permission::insert($data);
        });
    }

    public function getAllPermission($mode)
    {
        if ($mode == 1) {
            return Permission::whereNotIn('name', ['agent.link', 'user.link'])->get();
        }
        if($mode == 4){
            return Permission::whereNotIn('name', ['agent.link', 'user.link', 'role.*', 'role.show', 'role.create', 'role.edit', 'role.delete'])->get();
        }
        $permissions = ['user.show', 'user.link', 'account.show', 'deposit.show', 'withdrawal.show', 'report.*'];
        if ($mode == 2) {
            $permissions = array_merge($permissions, ['agent.link', 'agent.show']);
        }
        return Permission::whereIn('name', $permissions)->get();
    }

    /**
     * @param $roleData
     * @return mixed
     */
    public function createOrUpdateSuperAdminRole($roleData)
    {
        return $this->roleRepository->firstOrCreate($roleData);
    }

    public function syncPermisionAndRoleForUsers($users, $permissions, $roleName)
    {
        foreach ($users as $user) {
            $user->syncPermissions($permissions);
            $user->syncRoles($roleName);
        }
    }

    public function syncPermissionForUserByRoleName($user, $roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $permissions = $role->permissions;
            $user->syncPermissions($permissions);
            $user->syncRoles($roleName);
        }
    }

    public function getAllRole()
    {
        $roles = Role::where('allowed_scope', 2)->get();
        foreach ($roles as $role) {
            $role->amount = $role->users->count();
        }
        return $roles;
    }
    public function getPermissionInTreeStructure(){
        $permissions = Permission::all()->filter(function($item){
            return !in_array($item->name, ['role.*', 'role.create', 'role.edit', 'role.delete', 'role.show']);
        });
        $html ='';
        $i = 1;
        foreach ($permissions as $permission) {
            if (is_null($permission->parent_id)) {
                if ($i%4 == 1) {
                    $html .='<div class="treeview-group">';
                }
                $extendedClass = '';
                if ($i%4 == 0) {
                    $extendedClass = 'last-item';
                }
                $html .='<ul class="nested tree '.$extendedClass.'">';

                $html .= '<li class="level-1">';
                $html .= '<i class="fa fa-caret-right" fold-button="1" style="visibility: visible; color:black;
                     margin-right:7px"></i>';
                $html .= '<input type="checkbox" name="permissions[]"
                    class="permission level-' . $permission->level . '"
                    value="' . $permission->id . '" level ="' . $permission->level . '"
                     id="' . $permission->id . '"/>';
                $html .='<label>'.$permission->display_name.'</label>';
                if (count($permission->childs)) {
                    $html .='<ul class="nested">';
                    $html .= $this->generateTreeNode($permission->childs);
                    $html .= '</ul>';
                }
                $html .='</li>';
                $html .='</ul>';
                if ($i%4 == 0) {
                    $html .='</div>';
                }
                $i++;
            }
        }
        return $html;
    }

    /**
     * @param $childs
     * @param $mode
     * @return string
     */
    protected function generateTreeNode($childs)
    {
        $html = '';
        foreach ($childs as $child) {
            $html .= '<li>';
            if (count($child->childs)) {
                $html .= '<i class="fa fa-caret-right" fold-button="1" style="visibility: visible;
                 color:black; margin-right:7px"></i>';
            }
            $html .='<input type="checkbox" name="permissions[]"
                class="permission level-'.$child->level.'"
                value="'.$child->id.'"
                id="'.$child->id.'" parent-id="'.$child->parent_id.'"/>';
            $html .='<label>'.$child->display_name.'</label>';
            if (count($child->childs)) {
                $html .='<ul class="nested">';
                $html .= $this->generateTreeNode($child->childs);
                $html .= '</ul>';
            }
            $html .='</li>';
        }
        return $html;
    }


    /**
     * @param $data
     * @return mixed
     */
    public function createAndSyncPermissions($data)
    {
        return DB::transaction(function () use ($data) {
            $role = $this->roleRepository->create([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
                'allowed_scope' => 2,
                'guard_name' => 'web',
            ]);
            $hashIds = new Hashids('', 12);
            $name = $hashIds->encodeHex($role->id);
            $this->roleRepository->update(['name' => $name], $role->id);
            $permissions = Permission::whereIn('id', $data['permissions'])->get();
            $role->syncPermissions($permissions);
        });
    }

    public function getRoleDataById($roleId)
    {
        $role = $this->roleRepository->find($roleId);
        if (!$role) {
            return false;
        }
        $permissionIds = $role->permissions()->pluck('id')->toArray();
        rsort($permissionIds);
        return [
            'id' => $role->id,
            'name' => $role->name,
            'allowed_scope' => $role->allowed_scope,
            'display_name' => $role->display_name,
            'permissionIds' => $permissionIds
        ];
    }


    /**
     * @param $roleId
     * @param $data
     * @return bool
     */
    public function updateRoleById($roleId, $data)
    {
        $role = $this->roleRepository->find($roleId);
        return DB::transaction(function () use ($data, $role) {
            $role->update([
                'display_name' => $data['display_name'],
            ]);
            $permissions = Permission::whereIn('id', $data['permissions'])->get();
            $role->syncPermissions($permissions);
            $users = $role->users;
            foreach ($users as $user){
                $user->syncPermissions($permissions);
            }
        });
    }


}