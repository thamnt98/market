<?php
return [
    'vps_ip' => env('VPS_IP', '103.124.95.94'),
    'vps_port' => env('VPS_PORT', 8000),
    // 'group' => [
    //     'demoCawada' => 'Standard',
    // ],
    'group' => [
        'CWD-STD-B' => 'Standard',
    ],
    'leverage' => [
        '1' => '1:1',
        '50' => '1:50',
        '100' => '1:100',
        '200' => '1:200',
        '300' => '1:300',
        '500' => '1:500',
    ],
    'cmd'=> [
        0 => 'buy',
        1 => 'sell',
        2 => 'buy limit',
        3 => 'sell limit',
        4 => 'buy stop',
        5 => 'sell stop',
        6 => 'balance',
        7 => 'credit'
    ],
    'payment' => [
        '2' => 'e-Banking',
    ]
];
