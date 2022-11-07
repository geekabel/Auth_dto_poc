<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface {

    private $urlGenerator;
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
    ) {
        $this->urlGenerator = $urlGenerator;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response {
        $previous = $authException ? $authException->getPrevious() : null;

        // Parque le composant security est un peu bête et ne renvoie pas un AccessDenied pour les utilisateur connecté avec un cookie
        // On redirige le traitement de cette situation vers le AccessDeniedHandler
        if (in_array('application/json', $request->getAcceptableContentTypes())) {
            return new JsonResponse(
                ['title' => "Vous n'avez pas les permissions suffisantes pour effectuer cette action"],
                Response::HTTP_FORBIDDEN
            );
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}