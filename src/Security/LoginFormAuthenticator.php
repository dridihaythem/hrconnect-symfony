<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    /**
     * Authenticate the user based on the request.
     *
     * @param Request $request
     * @return \Symfony\Component\Security\Http\Authenticator\Passport\Passport
     */
    public function authenticate(Request $request): \Symfony\Component\Security\Http\Authenticator\Passport\Passport
    {
        $credentials = $this->getCredentials($request);

        return new \Symfony\Component\Security\Http\Authenticator\Passport\Passport(
            new \Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge($credentials['email'], function ($userIdentifier) {
                return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
            }),
            new \Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials($credentials['password']),
            [new \Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge('authenticate', $credentials['csrf_token'])]
        );
    }

    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager,PasswordHasherFactoryInterface $passwordHasherFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordHasher = $passwordHasherFactory->getPasswordHasher(User::class);
    }

    /**
     * Should this authenticator be used for this request?
     *
     * @param Request $request
     * @return bool|void
     */
    public function supports(Request $request): bool
{
    return $request->isMethod('POST') && $request->getPathInfo() === '/login';
}


    /**
     * Gather the credentials which need to be checked in order to authenticate.
     *
     * @param Request $request
     * @return array
     */
    public function getCredentials(Request $request) # FIRST
    {
        $credentials = [
            'email'       => $request->request->get('email'),
            'password'    => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['email']);

        return $credentials;
    }

    /**
     * Return a UserInterface object based on the credentials.
     *
     * @param array $credentials
     * @param UserProviderInterface $userProvider
     * @return object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider) # SECOND
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
    }

    /**
     * Check credentials
     *
     * Check csrf token is valid
     * Check password is valid
     *
     * @param array $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user) # THIRD
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        return $this->passwordHasher->verify($user->getPassword(), $credentials['password']);
    }


    /**
     * What should happen once the user is authenticated?
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return Response|void|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): Response # FOURTH
    {
        // 1. Try to redirect the user to their original intended path
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {

            return new RedirectResponse($targetPath);
        }
        return new RedirectResponse($this->urlGenerator->generate('app_homepage'));
    }

    /**
     * On failure
     *
     * @return string
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}