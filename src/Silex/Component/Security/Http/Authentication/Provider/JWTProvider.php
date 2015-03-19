<?php

namespace Silex\Component\Security\Http\Authentication\Provider;


use Silex\Component\Security\Http\Token\JWTToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class JWTProvider implements AuthenticationProviderInterface {

    /**
     * @var UserProviderInterface
     */
    protected $userProvider;

    public function __construct($userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * Attempts to authenticate a TokenInterface object.
     *
     * @param TokenInterface $token The TokenInterface instance to authenticate
     *
     * @return TokenInterface An authenticated TokenInterface instance, never null
     *
     * @throws AuthenticationException if the authentication fails
     */
    public function authenticate(TokenInterface $token)
    {

        if ($token instanceof JWTToken) {
            $userName = $token->getTokenContext()->name;
        } else {
            $userName = $token->getUsername();
        }

        $user = $this->userProvider->loadUserByUsername($userName);

        if (null != $user) {
            $lastContext = $token->getTokenContext();
            
            $token = new JWTToken($user->getRoles());
            $token->setTokenContext($lastContext);
            $token->setUser($user);

            return $token;
        }

        throw new AuthenticationException('JWT auth failed');
    }

    /**
     * Checks whether this provider supports the given token.
     *
     * @param TokenInterface $token A TokenInterface instance
     *
     * @return bool    true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof JWTToken;
    }
}
