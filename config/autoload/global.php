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
                'hostname' => getenv('DB_HOST'),
                'port'     => '',
                'charset'  => 'utf8',
            ),
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Db\Adapter\AdapterAbstractServiceFactory'
        )
    ),

    // ================= KHU VỰC THÊM MỚI ĐỂ KÉO DÀI SESSION =================
    'session_config' => array(
        // Thời gian sống của cookie session tại trình duyệt (86400 giây = 24 tiếng)
        'cookie_lifetime' => 86400,

        // Thời gian tối đa dữ liệu session tồn tại trên server (86400 giây = 24 tiếng)
        'gc_maxlifetime'  => 86400,
    ),
    'session_manager' => array(
        'validators' => array(
            'Zend\Session\Validator\RemoteAddr',
            'Zend\Session\Validator\HttpUserAgent',
        ),
    ),
    'session_storage' => array(
        'type' => 'Zend\Session\Storage\SessionArrayStorage',
    ),
    // ======================================================================
);
?>
