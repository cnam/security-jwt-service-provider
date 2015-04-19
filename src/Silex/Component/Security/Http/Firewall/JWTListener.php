<?php

namespace Silex\Component\Security\Http\Firewall;

use HttpEncodingException;
use Silex\Component\Security\Core\Encoder\TokenEncoderInterface;
use Silex\Component\Security\Http\Token\JWTToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class JWTListener implements ListenerInterface {

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @var TokenEncoderInterface
     */
    protected $encode;

    /**
     * @var array
     */
    protected $options;

    public function __construct(SecurityContextInterface $securityContext,
                                AuthenticationManagerInterface $authenticationManager,
                                TokenEncoderInterface $encoder,
                                array $options)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->encode = $encoder;
        $this->options = $options;
    }

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $requestToken = $request->headers->get($this->options['header_name'],'');

        if (!empty($requestToken)) {
            try {
                $decoded = $this->encode->decode($requestToken);

                $token = new JWTToken();
                $token->setTokenContext($decoded);

                $authToken = $this->authenticationManager->authenticate($token);
                $this->securityContext->setToken($authToken);

            } catch (HttpEncodingException $e) {
            } catch (\UnexpectedValueException $e) {
            }
        }
    }
}
