<?php

namespace Silex\Component\Security\Http\Token;


use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * Constructor.
     *
     * @param string|object            $user        The user
     * @param mixed                    $context The user credentials
     * @param string                   $providerKey The provider key
     * @param RoleInterface[]|string[] $roles       An array of roles
     */
    public function __construct($user, $context, $providerKey, array $roles = array()) {
        parent::__construct($roles);
        $this->setUser($user);
        $this->credentials = $context;
        $this->providerKey = $providerKey;

        parent::setAuthenticated(count($roles) > 0);
    }

    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }
}
