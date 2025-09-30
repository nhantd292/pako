<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
return array(
    'db' => array(
        'adapters' => array(
            'dbConfig' => array(
                'driver'   => 'Pdo_Mysql',
                'database' => getenv('DB_NAME'),
                'username' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'hostname' => 'localhost',
                'port'     => '',
                'charset'  => 'utf8',
            ),
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Db\Adapter\AdapterAbstractServiceFactory'
        )
    )
);
?>
