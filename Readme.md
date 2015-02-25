# Silex security jwt service provider

This provider usage with silex security

Simple example

```php

$app['security.firewalls'] = array(
    'login' => [
        'pattern' => 'login|register|oauth',
        'anonymous' => true,
    ],
    'secured' => array(
        'pattern' => '^.*$',
        'logout' => array('logout_path' => '/logout'),
        'users' => [],
        'jwt' => array(
            'use_forward' => true,
            'require_previous_session' => false,
            'stateless' => true,
        )
    ),
);


$app['security.jwt'] = [
     'secret_key' => 'you_secret_key_for_generation token',
     'life_time'  => 86400, //life time token
     'options' => [
         'header_name' => 'AUTH-HEADER-TOKEN' //header name for authorisation
     ]
];

```