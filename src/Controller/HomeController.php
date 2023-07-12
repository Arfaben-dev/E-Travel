<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: '/')]
    public function index(Request $request, AnnonceRepository $annonceRepository, UserRepository $userRepository): Response
    {
        $annonce = $annonceRepository->TenAnnonce();

        $user = $userRepository->FourUser();


        return $this->render('components/main.html.twig', [
            'annonce' => $annonce,
            'users' => $user,
        ]);
    }
    #[Route('à-propos-de-nous', name: 'about')]
    public function about(): Response
    {

        return $this->render('components/about.html.twig');
    }



           // connexion with google and facebook

    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("connect/google", name="connect_google_start")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // on Symfony 3.3 or lower, $clientRegistry = $this->get('knpu.oauth2.registry');

        // will redirect to Facebook!
        return $clientRegistry
            ->getClient('google') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect();
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("connect/google/check", name="connect_google_check")
     */
    public function connectCheckAction(Request $request)
    {
        if (!$this->getUser()) {
            return new JsonResponse(array('status' => false , 'message' => "user not found"));
        } else {
            return $this->redirectToRoute('/');
        }
    }
}
