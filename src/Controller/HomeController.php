<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {
    private $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    #[Route('/', name:'app_home')]
public function index(): Response {

    return $this->render('home/index.html.twig', [
        'controller_name' => 'Homepage',
    ]);
}

#[Route('/profile', name:'app_profile')]
public function profile(): Response {
    $this->denyAccessUnlessGranted('ROLE_USER');
    return $this->render('home/profile.html.twig', [

        'profile' => 'Welcome,' . 'Godwin',
    ]);
}
}
