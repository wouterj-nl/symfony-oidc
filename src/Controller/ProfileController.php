<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Controller to test if a user is properly authenticated by Symfony.
 */
#[Route('/profile', name: 'app_profile')]
class ProfileController extends AbstractController
{
    public function __invoke(?UserInterface $user): Response
    {
        return $this->json($user);
    }
}
