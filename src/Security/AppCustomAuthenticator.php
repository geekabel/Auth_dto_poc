<?php

namespace App\Security;

use App\Service\JwtService;
use App\Service\AuthenticationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator {
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(

        AuthenticationService $authenticationService,
        UserPasswordHasherInterface $passwordHasher,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        JwtService $jwtService,
        UserProviderInterface $userProvider
    ) {
        $this->authenticationservice = $authenticationService;
        $this->userPasswordEncoderInterface = $passwordHasher;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->jwtService = $jwtService;
        $this->userProvider = $userProvider;
    }

    public function authenticate(Request $request): Passport {
        $username = $request->request->get('username', '');
        $password = $request->request->get('password', '');
        $csrfToken = $request->request->get('csrf_token');
        // if ('' === $username && '' === $password) {
        //     return null;
        // }
        $request->getSession()->set(Security::LAST_USERNAME, $username);
        $auth = $this->authenticationservice->authentication($username, $password);
        //dd($auth);
        if ($auth === true) {
            $user = $this->userProvider->loadUserByIdentifier($username);
            //dd($user->getUserIdentifier());
            return new SelfValidatingPassport(new UserBadge($user->getUsername()));
        }

        throw new CustomUserMessageAuthenticationException('Username could not be found');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response{
        // service Jwt
        $this->jwtService->createJwtCookie([
            'user_id'               => $token->getUser()->getId(),
            'identifiantPartenaire' => $token->getUser()->getUserIdentifier(),
        ]);

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        // For example:
        return new RedirectResponse($this->urlGenerator->generate('app_profile'));
        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
