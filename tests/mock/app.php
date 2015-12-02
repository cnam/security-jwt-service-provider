<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\User;

require_once __DIR__ . '/../../vendor/autoload.php';

$app = new Silex\Application(['debug' => true]);

$app['security.jwt'] = [
    'secret_key' => 'Very_secret_key',
    'life_time'  => 86400,
    'algorithm'  => ['HS256'],
    'options'    => [
        'header_name'  => 'X-Access-Token',
        'token_prefix' => 'Bearer',
    ]
];
$app['users'] = function () use ($app) {
    $users = [
        'admin' => array(
            'roles' => array('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'),
            // raw password is foo
            'password' => '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==',
            'enabled' => true
        ),
    ];
    return new InMemoryUserProvider($users);
};
$app['security.firewalls'] = array(
    'login' => [
        'pattern' => 'login|register|oauth',
        'anonymous' => true,
    ],
    'secured' => array(
        'pattern' => '^.*$',
        'logout' => array('logout_path' => '/logout'),
        'users' => $app['users'],
        'jwt' => array(
            'use_forward' => true,
            'require_previous_session' => false,
            'stateless' => true,
        )
    ),
);
$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\SecurityJWTServiceProvider());
$app->post('/api/login', function(Request $request) use ($app){
    $vars = json_decode($request->getContent(), true);
    try {
        if (empty($vars['_username']) || empty($vars['_password'])) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $vars['_username']));
        }
        /**
         * @var $user User
         */
        $user = $app['users']->loadUserByUsername($vars['_username']);
        if (! $app['security.encoder.digest']->isPasswordValid($user->getPassword(), $vars['_password'], '')) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $vars['_username']));
        } else {
            $response = [
                'success' => true,
                'token' => $app['security.jwt.encoder']->encode(['name' => $user->getUsername()]),
            ];
        }
    } catch (UsernameNotFoundException $e) {
        $response = [
            'success' => false,
            'error' => 'Invalid credentials',
        ];
    }
    return $app->json($response, ($response['success'] == true ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST));
});

$app->get('/api/protected_resource', function() use ($app){
    $jwt = 'no';
    $token = $app['security.token_storage']->getToken();
    if ($token instanceof Silex\Component\Security\Http\Token\JWTToken) {
        $jwt = 'yes';
    }
    $granted = 'no';
    if($app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
        $granted = 'yes';
    }

    $granted_user = 'no';
    if($app['security.authorization_checker']->isGranted('ROLE_USER')) {
        $granted_user = 'yes';
    }

    $granted_super = 'no';
    if($app['security.authorization_checker']->isGranted('ROLE_SUPER_ADMIN')) {
        $granted_super = 'yes';
    }

    $user = $token->getUser();
    return $app->json([
        'hello' => $token->getUsername(),
        'username' => $user->getUsername(),
        'auth' => $jwt,
        'granted' => $granted,
        'granted_user' => $granted_user,
        'granted_super' => $granted_super,
    ]);
});

$app->run();
