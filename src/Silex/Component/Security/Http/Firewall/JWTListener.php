<?php

namespace Silex\Component\Security\Http\Firewall;

use Silex\Component\Security\Http\Token\JWTToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class JWTListener implements ListenerInterface {

    protected $securityContext;
    protected $authenticationManager;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $requestToken = $request->headers->get('X-KB-Access-Token','');

        if (!empty($requestToken)) {
            try {
                $decoded = \JWT::decode($requestToken, 'very_secret_token');

                $token = new JWTToken();
                $token->setUser($decoded->name);

                $authToken = $this->authenticationManager->authenticate($token);
                $this->securityContext->setToken($authToken);

                return;
            } catch (AuthenticationException $e) {

            }
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        $event->setResponse($response);
    }
}
