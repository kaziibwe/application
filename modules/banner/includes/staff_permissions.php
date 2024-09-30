<?php

/*
 * Inject permissions Feature and Capabilities for banner module
 */
hooks()->add_filter('staff_permissions', function ($permissions) {
    $viewGlobalName      = _l('permission_view').'('._l('permission_global').')';
    $allPermissionsArray = [
        'view'   => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    $permissions['banner'] = [
        'name'         => _l('banner'),
        'capabilities' => $allPermissionsArray,
    ];

    return $permissions;
});
