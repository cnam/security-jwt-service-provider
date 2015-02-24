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
    protected $secretKey;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $secretKey)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->secretKey = $secretKey;
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
                $decoded = \JWT::decode($requestToken, $this->secretKey);

                $token = new JWTToken();
                $token->setUser($decoded->name);

                $authToken = $this->authenticationManager->authenticate($token);
                $this->securityContext->setToken($authToken);

                return;

            } catch (\UnexpectedValueException $e) {

            } catch (AuthenticationException $e) {

            }
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        $event->setResponse($response);
    }
}
