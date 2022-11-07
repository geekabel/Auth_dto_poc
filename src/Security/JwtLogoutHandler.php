<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class JwtLogoutHandler implements LogoutSuccessHandlerInterface {

    private $urlGenerator;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    // $response = new RedirectResponse($this->urlGenerator->generate('app_login'));
    //     $response->headers->clearCookie('jwt');

    //     return $response;
    public function onLogoutSuccess(Request $request) {
        $response = new RedirectResponse($this->urlGenerator->generate('app_login'));
        $response->headers->clearCookie('jwt');

        return $response;
    }

    // public function logout(Request $request, Response $response, TokenInterface $token) {
    //     //$this->eventDispatcher->dispatch(new LogoutEvent($request, $this->tokenStorage->getToken()));
    //     //$request->getSession()->invalidate();
    // }
}
