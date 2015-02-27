<?php

namespace Silex\Component\Security\Http\Token;


use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class JWTToken extends AbstractToken implements TokenInterface
{
    protected $tokenContext;

    /**
     * Set token context from JWT tokens
     *
     * @param $tokenContext
     */
    public function setTokenContext($tokenContext)
    {
        $this->tokenContext = $tokenContext;
    }

    /**
     * Return token context
     *
     * @return mixed
     */
    public function getTokenContext()
    {
        return $this->tokenContext;
    }

    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
        return '';
    }
}
