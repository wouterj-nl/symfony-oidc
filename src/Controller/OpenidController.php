<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Basic OpenID login implementation to get a JWT token.
 */
class OpenidController extends AbstractController
{
    public function __construct(
        #[Autowire('%env(OIDC_CLIENT_ID)%')] private readonly string $clientId,
        #[Autowire('%env(OIDC_CLIENT_SECRET)%')] private readonly string $clientSecret,
    ) {
    }

    #[Route('/openid-login', name: 'app_login')]
    public function login(UrlGeneratorInterface $urlGenerator): Response
    {
        $qs = http_build_query([
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'response_type' => 'code',
            'scope' => 'openid roles profile email',
            'redirect_uri' => $urlGenerator->generate('app_openid_redirect', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new RedirectResponse('http://localhost:8080/realms/master/protocol/openid-connect/auth?'.$qs);
    }

    #[Route('/openid-target', name: 'app_openid_redirect')]
    public function redirectTarget(Request $request, UrlGeneratorInterface $urlGenerator, HttpClientInterface $httpClient, #[Autowire('%env(OIDC_CLIENT_SECRET)%')] string $secret): Response
    {
        $response = $httpClient->request('POST', 'http://localhost:8080/realms/master/protocol/openid-connect/token', [
            'body' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $urlGenerator->generate('app_openid_redirect', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                'code' => $request->query->get('code'),
            ],
        ]);

        return $this->redirectToRoute('home', ['token' => $response->toArray()['access_token']]);
    }
}
