<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Commentaire;
use App\Entity\User;
use App\Form\CommentaireType;
use App\Form\SearchType;
use App\Repository\AnnonceRepository;
use App\Repository\CommentaireRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgenceController extends AbstractController
{
    #[Route('/nos-paternaires', name: 'allpaternaire')]
    public function all(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {



            $pagination = $paginator->paginate(
                $userRepository->findBy(['isPartner'=>true]),
                $request->query->getInt('page', 1),
                2
            );



        return $this->render('agence/index.html.twig', [

            'pagination' => $pagination,
        ]);
    }

    #[Route('/detailagence/?{id}', name:'detailagence')]
    public function detail(User $user, UserRepository $userRepository,CommentaireRepository $commentaireRepository,Request $request,
    ){
        $partenaire = $userRepository->find($user);

        $commentaire = new Commentaire();


        $commentaires = $commentaireRepository->findBy(['commentannonce' =>$user]);

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        return $this->render('agence/detail.html.twig', [
            'commentaires' => $commentaires,
            'user' => $partenaire,
        ]);

    }


}
