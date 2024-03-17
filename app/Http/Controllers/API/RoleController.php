<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Exception;


class RoleController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $with_responsibility = $request->input('with_responsibility', false);

        $roleQuery = Role::query();

        // get single data
        if($id) {
            $role = $roleQuery->with('responsibility')->find($id);

            if($role) {
                return ResponseFormatter::success($role, 'Role found');
            }

            return ResponseFormatter::error('Role not found', 404);
        }

        // get multiple data 
        $roles = $roleQuery->where('company_id', $request->company_id);

        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
        }

        // get data with responsibility
        if($with_responsibility) {
            $roles->with('responsibility');
        }

        return ResponseFormatter::success(
            $roles->paginate($limit), 'Role found'
        );
    }

    public function create(CreateRoleRequest $request)
    {
        try {
            // create role
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            if(!$role) {
                throw new Exception('Role not created');
            }

            return ResponseFormatter::success($role, 'Role created');
        } catch (Exception $e) {
            return ResponseFormatter::error('Failed create role', 500);
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            // find role id
            $role = Role::find($id);

            if(!$role) {
                throw new Exception('Role not found');
            }

            // update role
            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success($role, 'Role updated');
        } catch (Exception $e) {
            return ResponseFormatter::error('Failed update role', 500);
        }
    }

    public function destroy($id)
    {
        try {
            // get role
            $role = Role::find($id);

            if(!$role) {
                throw New Exception('Role not found');
            }

            // delete role
            $role->delete();

            return ResponseFormatter::success('Role deleted');
        }
        catch (Exception $e) {
            return ResponseFormatter::error('Failed delete role', 500);
        }
    }
}
