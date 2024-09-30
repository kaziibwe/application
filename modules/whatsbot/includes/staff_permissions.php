<?php
/*
 * Inject permissions Feature and Capabilities for whatsbot module
 */
hooks()->add_filter('staff_permissions', function ($permissions) {
    $viewGlobalName      = _l('permission_view');

    $allPermissionsArray = [
        'view'   => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    $permissions['wtc_connect_account'] = [
        'name'         => _l('whatsbot_connect_account'),
        'capabilities' => [
            'connect' => _l('connect'),
        ],
    ];

    $permissions['wtc_message_bot'] = [
        'name'         => _l('whatsbot_message_bot'),
        'capabilities' => $allPermissionsArray,
    ];

    $permissions['wtc_message_bot']['capabilities']['clone_bot'] = _l('permission_bot_clone');

    $permissions['wtc_template_bot'] = [
        'name'         => _l('whatsbot_template_bot'),
        'capabilities' => $allPermissionsArray,
    ];

    $permissions['wtc_template_bot']['capabilities']['clone_bot'] = _l('permission_bot_clone');

    $permissions['wtc_template'] = [
        'name'         => _l('whatsbot_template'),
        'capabilities' => [
            'view'          => $viewGlobalName,
            'load_template' => _l('load_template'),
        ],
    ];

    $permissions['wtc_campaign'] = [
        'name'         => _l('whatsbot_campaigns'),
        'capabilities' => array_merge($allPermissionsArray, [
            'show' => _l('show_campaign'),
        ]),
    ];

    $permissions['wtc_chat'] = [
        'name'         => _l('whatsbot_chat'),
        'capabilities' => [
            'view' => $viewGlobalName,
        ],
    ];

    $permissions['wtc_log_activity'] = [
        'name'         => _l('whatsbot_log_activity'),
        'capabilities' => [
            'view'      => $viewGlobalName,
            'clear_log' => _l('clear_log'),
        ],
    ];

    $permissions['wtc_settings'] = [
        'name'         => _l('whatsbot_settings'),
        'capabilities' => [
            'view' => _l('view'),
        ],
    ];

    return $permissions;
});
