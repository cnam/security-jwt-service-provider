<?php

namespace Silex\Component\Security\Http\Token;


use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class JWTToken extends AbstractToken implements TokenInterface
{
    /**
     * @var string token context from JWT tokens
     */
    protected $tokenContext;

    /**
     * @var string username claim for JWT token
     */
    protected $usernameClaim;

    /**
     * Set username claim for JWT token
     *
     * @param $usernameClaim
     */
    public function setUsernameClaim($usernameClaim)
    {
        $this->usernameClaim = $usernameClaim;
    }

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

    /**
     * Returns the user username.
     *
     * @return mixed The user username
     */
    public function getUsername()
    {
        return (isset($this->tokenContext->{$this->usernameClaim})) ?
            $this->tokenContext->{$this->usernameClaim} : null;
    }
}
