<?php

// src/Security/ApiKeyAuthenticator.php
namespace App\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class JwtAuthenticator extends AbstractAuthenticator {
    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool {
        return $request->cookies->get("jwt") ? true : false;
    }

    public function authenticate(Request $request) {
        $cookie = $request->cookies->get("jwt");
        // Default error message
        $error = "Unable to validate session.";

        try
        {
            $decodedJwt = JWT::decode($cookie, getenv("JWT_SECRET"), ['HS256']);

            $payload = [
                'user_id'               => $decodedJwt->user_id,
                'identifiantPartenaire' => $decodedJwt->identifiantPartenaire,
            ];

            return $payload;
        } catch (ExpiredException $e) {
            $error = "La Session a expiré.";
        } catch (SignatureInvalidException $e) {
            // In this case, you may also want to send an email to yourself with the JWT
            // If someone uses a JWT with an invalid signature, it could be a hacking attempt.
            $error = "Tentative d'accès à une session invalide.";
        } catch (\Exception$e) {
            // Use the default error message
            $e->getMessage();
        }

        throw new CustomUserMessageAuthenticationException($error);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response{
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}