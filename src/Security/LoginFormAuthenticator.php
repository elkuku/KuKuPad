<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use UnexpectedValueException;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $urlGenerator,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private string $appEnv
    ) {
    }

    public function supports(Request $request): bool
    {
        return '/login' === $request->getPathInfo()
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        if (false === in_array($this->appEnv, ['dev', 'test'])) {
            throw new UnexpectedValueException('GTFO!');
        }

        $credentials = $this->getCredentials($request);

        return new SelfValidatingPassport(
            new UserBadge($credentials['email'] ?? ''),
            [
                new CsrfTokenBadge('login', $credentials['csrf_token'] ?? ''),
                new RememberMeBadge(),
            ]
        );
    }

    #[ArrayShape(['email' => "string", 'csrf_token' => "string"])]
    public function getCredentials(Request $request): array
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }


    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): RedirectResponse {
        if ($targetPath = $this->getTargetPath(
            $request->getSession(),
            $firewallName
        )
        ) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('default'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }
}
