<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'superadministrator' => [
            'users' => 'c,r,u,d',
            'orders' => 'c,r,u,d',
            'items' => 'c,r,u,d',
            'profile' => 'r,u',
        ],
        'delivery_agent' => [
            'orders' => 'r,u',
            'items' => 'r,u',
        ],
        'merchant' => [
            'orders' => 'c,r,u,d',
            'items' => 'c,r,u,d',
        ],
    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],

];
