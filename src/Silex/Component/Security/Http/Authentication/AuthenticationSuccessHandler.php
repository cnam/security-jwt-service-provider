<?php
namespace Silex\Component\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        $response = [
            'id'        => $user->getId(),
            'username'  => $user->getUsername(),
            'api_token' => $token->serialize()
        ];

        return new JsonResponse($response);
    }

}