<?php

namespace Silex\Component\Security\Http\Authentication\Provider;


use Silex\Component\Security\Http\Token\JWTToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class JWTProvider implements AuthenticationProviderInterface
{

    /**
     * @var UserProviderInterface
     */
    protected $userProvider;
    protected $providerKey;

    /**
     * Constructor.
     *
     * @param UserProviderInterface $userProvider An UserProviderInterface instance
     * @param UserCheckerInterface  $userChecker  An UserCheckerInterface instance
     * @param string                $providerKey  The provider key
     */
    public function __construct(UserProviderInterface $userProvider, UserCheckerInterface $userChecker, $providerKey)
    {
        $this->userProvider = $userProvider;
        $this->userChecker = $userChecker;
        $this->providerKey = $providerKey;
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
        if (!$this->supports($token)) {
            return;
        }

        if (!$user = $token->getUser()) {
            throw new AuthenticationException('JWT auth failed');
        }

        $user = $this->userProvider->loadUserByUsername($user);
        $this->userChecker->checkPostAuth($user);
        $token = new JWTToken($user,
            $token->getCredentials(),
            $this->providerKey,
            $this->getRoles($user, $token)
        );

        return $token;
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

    /**
     * Retrieves roles from user and appends SwitchUserRole if original token contained one.
     *
     * @param UserInterface  $user  The user
     * @param TokenInterface $token The token
     *
     * @return array The user roles
     */
    private function getRoles(UserInterface $user, TokenInterface $token)
    {
        $roles = $user->getRoles();

        foreach ($token->getRoles() as $role) {
            if ($role instanceof SwitchUserRole) {
                $roles[] = $role;

                break;
            }
        }

        return $roles;
    }
}
