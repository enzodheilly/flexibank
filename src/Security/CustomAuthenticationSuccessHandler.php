<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Response; // ✅ Bon import
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class CustomAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $token->getUser();
    
        if (in_array('ROLE_JURY', $user->getRoles())) {
            // Redirection vers la page de choix (pas switch direct)
            return new RedirectResponse($this->router->generate('jury_choose'));
        }
    
        return new RedirectResponse($this->router->generate('app_home'));
    }    
}
