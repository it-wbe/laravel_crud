<?php

namespace Wbe\Crud\Controllers\User\Auth;

use Wbe\Crud\Models\Roles\Role;
use Wbe\Crud\Models\hrbac\HierarchicalRBAC\Authorization;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\Roles\Permissions;


/**
 *  This is example of hierarchical RBAC authorization configiration.
 */

class AuthorizationClass extends Authorization
{
    public function getPermissions() {
        return [
            'admin/crud/grid/1',

            'edit-crud-system-content-type' => [
                'description' => 'Access to CRUD system content types',
            ],
            'access-content-type' => [
                'description' => 'Access to specified for role content type'
            ],
            'access-field-descriptor' => [
                'description' => 'Access to Field Descriptor'
            ]
        ];
    }

    public function getRoles() {
//     return   dd(Permissions::select()->get());
//        return Role::select('name')->with('permission')->get();
        return [
            'moderator' => [
                //'edit-crud-system-content-type',
                'access-content-type',
                //'access-field-descriptor',
            ],
        ];
    }


    /**
     * Methods which checking permissions.
     * Methods should be present only if additional checking needs.
     */

    public function editCrudSystemContentType($user, $content_type_id) {
        $ct = ContentType::where('id', $content_type_id)->first();
        return (!empty($ct) && $ct->is_system == 1) ? true : false;
    }

    public function accessContentType($user, $content_type_id) {
        $ct = ContentType::where('id', $content_type_id)->first();
        if(!empty($ct) && $ct->is_system == 1) return false;
        return true;
    }
}
