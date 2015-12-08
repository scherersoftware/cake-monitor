<?php
return [
    'CakeMonitor' => [
        'accessToken' => null,
        'projectName' => null,
        'serverDescription' => 'Server: ' . env('SERVERDESCRIPTION'),
        'onSuccess' => function() {
            echo 'CHECK-OK';
            return;
        },
        'checks' => [
            'DATABASE' => [
                'callback' => function() {
                    try {
                        $connection = Cake\Datasource\ConnectionManager::get('default');
                        $tables = $connection->execute('SHOW TABLES;')->fetchAll('assoc');

                        $checks = null;
                        foreach ($tables as $table) {
                            $check = $connection->execute('CHECK TABLE ' . reset($table) . ';')->fetchAll('assoc');
                            if ($check[0]['Msg_type'] == 'status' && $check[0]['Msg_text'] != 'OK') {
                                $checks .= '<br>' .$check[0]['Table'] . 'status: ' . $check[0]['Msg_text'];
                            }
                        }
                        if (!empty($checks)) {
                            return $checks;
                        }
                    } catch (Exception $e) {
                        return $e->getMessage();
                    }
                    return true;
                },
                'error' => 'MYSQL CHECK TABLE ERROR'
            ]
        ]
    ]
];
