<?php

namespace Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Component\Security\Http\Authentication;
use Silex\Component\Security\Http\Authentication\Provider\JWTProvider;
use Silex\Component\Security\Http\Firewall\JWTListener;
use Silex\Component\Security\Http\Logout\LogoutSuccessHandler;

class SecurityJWTServiceProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {
        $app['security.jwt'] = array(
            'secret_key' => 'default_secret_key'
        );

        $app['security.authentication.success_handler.secured'] = function () use ($app) {
            return new Authentication\AuthenticationSuccessHandler($app['security.http_utils'], []);
        };

        $app['security.authentication.failure_handler.secured'] = function () use ($app) {
            return new Authentication\AuthenticationFailureHandler($app, $app['security.http_utils'], []);
        };

        $app['security.authentication.logout_handler.secured'] = function () use ($app) {
            return new LogoutSuccessHandler($app['security.http_utils'], []);
        };


        $app['security.authentication_listener.factory.jwt'] = $app->protect(function ($name, $options) use ($app) {
            $app['security.authentication_listener.'.$name.'.jwt'] = function() use ($app) {
                return new JWTListener($app['security'], $app['security.authentication_manager'], $app['security.jwt']['secret_token']);
            };

            $app['security.authentication_provider.' . $name . '.jwt'] = function() use ($app) {
                return new JWTProvider($app['users']);
            };

            return array(
                'security.authentication_provider.'.$name.'.jwt',
                'security.authentication_listener.'.$name.'.jwt',
                null,
                'pre_auth'
            );
        });
    }
}