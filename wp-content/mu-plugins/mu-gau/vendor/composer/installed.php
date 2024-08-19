<?php return array(
    'root' => array(
        'name' => 'mu-plugins/mu-gau',
        'pretty_version' => 'dev-master',
        'version' => 'dev-master',
        'reference' => '86c973e768d4dafb4d5cab55ba00e9cdb57749ba',
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'mu-plugins/mu-gau' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => '86c973e768d4dafb4d5cab55ba00e9cdb57749ba',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'roave/security-advisories' => array(
            'pretty_version' => 'dev-latest',
            'version' => 'dev-latest',
            'reference' => '251a4f1fefcc6e6cc90d50514fee6b6e3745cb3e',
            'type' => 'metapackage',
            'install_path' => null,
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => true,
        ),
        'roots/wp-password-bcrypt' => array(
            'pretty_version' => '1.1.0',
            'version' => '1.1.0.0',
            'reference' => '15f0d8919fb3731f79a0cf2fb47e1baecb86cb26',
            'type' => 'library',
            'install_path' => __DIR__ . '/../roots/wp-password-bcrypt',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
