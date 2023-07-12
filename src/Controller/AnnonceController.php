<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Annonce;
use App\Entity\Commentaire;
use App\Entity\Favoris;
use App\Entity\Image;
use App\Entity\User;
use App\Form\AnnonceType;
use App\Form\CommentaireType;
use App\Form\SearchType;
use App\Repository\AnnonceRepository;
use App\Repository\CommentaireRepository;
use App\Repository\FavorisRepository;
use App\Repository\PlaceRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Service\UploadeService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AnnonceController extends AbstractController
{
    #[Route('/creer-annonce', name: 'addannonce')]
    public function add(Request $request, UploadeService $uploadeservice, EntityManagerInterface $em): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            // upload des images
            $this->uploadDesImages($form, $uploadeservice, $annonce, $user, $em);

            toastr()->addSuccess('Votre annonce a été créée ');

            return $this->redirectToRoute('listannonce');
        } else {
            return $this->render('annonce/add.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    #[Route('/mes-annonces', name: 'listannonce')]
    #[IsGranted('ROLE_PATERNAIRE')]
    public function index(AnnonceRepository $repository, PlaceRepository $placeRepository): Response
    {
        $annonces = $repository->findBy(['user' => $this->getUser()], ['created_at' => 'ASC']);

        $places = $placeRepository->findAll();


        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,

        ]);
    }

    #[Route('/mes-clients-{id}', name: 'listclient')]
    #[IsGranted('ROLE_PATERNAIRE')]
    public function mesclients(
        Annonce               $annonce,
        ReservationRepository $reservationRepository,
        PlaceRepository       $placeRepository
    ): Response
    {
        $reservation = $reservationRepository->findBy(['annoncereservation' => $annonce, 'statut' => 0, 'isPaid' => true]);

        $places = $placeRepository->findBy(['reservation' => $reservation]);


        return $this->render('annonce/client.html.twig', [
            'places' => $places,

        ]);
    }

    #[Route('/annonces', name: 'allannonce')]
    public function all(AnnonceRepository $annonceRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $data = new SearchData();

        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);

        [$min, $max] = $annonceRepository->findMinMax();


        if ($form->isSubmitted()) {
            $pagination = $paginator->paginate(
                $annonceRepository->FindSearch($data),
                $request->query->getInt('page', 1),
                6
            );
        } else {
            $pagination = $paginator->paginate(
                $annonceRepository->AllAnnonce(),
                $request->query->getInt('page', 1),
                6
            );
        }


        return $this->render('annonce/all.html.twig', [

            'pagination' => $pagination,
            'min' => $min,
            'max' => $max,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/detail/?{id}', name: 'detailannonce')]
    public function detail(
        Annonce                $annonce,
        AnnonceRepository      $annonceRepository,
        ReservationRepository  $reservationRepository,
        CommentaireRepository  $commentaireRepository,
        Request                $request,
        EntityManagerInterface $em,
        UserRepository         $userRepository
    ): Response
    {

        $users = $userRepository->findAll();

        $iduser = $userRepository->find($this->getUser());

        $annonces = $annonceRepository->find($annonce);
        $commentaire = new Commentaire();

        $reservations = $reservationRepository->findOneBy(['userreservation' => $this->getUser(),
            'annoncereservation' => $annonce, 'isPaid' => true]);

        $user = $userRepository->find($annonce->getUser());

        $id = $reservationRepository->findOneBy([
            'isPaid' => false]);

        if ($id) {
            $Isreservation = $reservationRepository->find($id->getId());
            $em->remove($Isreservation);
            $em->flush();
        }
        $commentaires = $commentaireRepository->findBy(['commentannonce' => $annonce]);

        // calculer du pourcentage d'avis

        $nbrecoment = count($commentaires);
        if ($nbrecoment != 0) {
            $a = count($commentaireRepository->findBy(['commentannonce' => $annonce, 'note' => 1],));
            $b = count($commentaireRepository->findBy(['commentannonce' => $annonce, 'note' => 2],));
            $c = count($commentaireRepository->findBy(['commentannonce' => $annonce, 'note' => 3],));
            $d = count($commentaireRepository->findBy(['commentannonce' => $annonce, 'note' => 4],));
            $e = count($commentaireRepository->findBy(['commentannonce' => $annonce, 'note' => 5],));

            $a1 = ($a / $nbrecoment) * 100;
            $b1 = ($b / $nbrecoment) * 100;
            $c1 = ($c / $nbrecoment) * 100;
            $d1 = ($d / $nbrecoment) * 100;
            $e1 = ($e / $nbrecoment) * 100;

            $sommetotal = (1 * $a) + (2 * $b) + (3 * $c) + (4 * $d) + (5 * $e);
            $moyen = sprintf("%.1f", $sommetotal / $nbrecoment);
        } else {
            $a1 = 0;
            $b1 = 0;
            $c1 = 0;
            $d1 = 0;
            $e1 = 0;


            $moyen = 0;
        }

        return $this->render('annonce/detail.html.twig', [
            'annonces' => $annonces,
            'commentaires' => $commentaires,
            'reservations' => $reservations,
            'user' => $user,
            'users' => $users,
            'iduser'=>$iduser,
            'a' => $a1,
            'b' => $b1,
            'c' => $c1,
            'd' => $d1,
            'e' => $e1,
            'moyen' => $moyen,

        ]);
    }


    #[Route('/detail/deletecommentaire/{id}', name: 'detailcomment')]
    public function detailcomment(
        Commentaire     $commentaire,
        ManagerRegistry $managerRegistry
    ): JsonResponse
    {
        $em = $managerRegistry->getManager();


        $em->remove($commentaire);

        $em->flush();
        return new JsonResponse();
    }

    #[Route('commentaire/{id}', name: 'commentaire')]
    public function commentaire(
        Annonce                $annonce,
        Request                $request,
        EntityManagerInterface $em,
        CommentaireRepository  $commentaireRepository,
        UserRepository         $userRepository
    ): Response
    {
        $note = $request->get('note');
        $comment = $request->get('commente');
        $user = $userRepository->find($this->getUser());


        $commentaire = new Commentaire();


        $commentaire->setNom($comment);
        $commentaire->setNote($note);
        $commentaire->setCommentannonce($annonce);
        $commentaire->setCommentuser($user);
        $em->persist($commentaire);
        $em->flush();

        $ide = $commentaireRepository->findOneBy(['commentuser' => $this->getUser(), 'commentannonce' => $annonce]);

        //$ide = $commentaireRepository->findAll();

        toastr()->addSuccess('Votre commentaire a été ajouté');

        return $this->redirectToRoute('detailannonce', ['id' => $annonce]);
    }

    #[Route('deletecommentaire/{id}', name: 'deletecommentaire')]
    public function deletecomment(
        Commentaire           $commentaire,
        ManagerRegistry       $managerRegistry,
        CommentaireRepository $commentaireRepository,
        Request               $request
    ): JsonResponse
    {

        $em = $managerRegistry->getManager();

        $em->remove($commentaire);

        $em->flush();


        return new JsonResponse();
    }


    #[Route('delete/{id}', name: 'deleteAnnonce')]
    #[IsGranted('ROLE_PATERNAIRE')]
    public function delete(Annonce $annonce, ManagerRegistry $doctrine): Response
    {

        $images = $annonce->getImages();

        if ($images) {
            foreach ($images as $image) {
                $nomimage = $this->getParameter("produit_directory") . '/' . $image->getName();

                if (file_exists($nomimage)) {
                    unlink($nomimage);
                }
            }
        }

        $em = $doctrine->getManager();
        $em->remove($annonce);
        $em->flush();
        //     $this->addFlash('success', "L'annone a été suprimer avec succes");

        toastr()->addSuccess('Votre annonce a été suprimer avec succes ');
        return $this->redirectToRoute('listannonce');
    }

    #[Route('mes-annonces/update?{id}', name: 'updateannonce')]
    public function update($id, Request $request, UploadeService $uploadeservice, EntityManagerInterface $em,)
    {


        $annonce = $em->getRepository(Annonce::class)->findOneBy(['id' => $id]);
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            // upload des images
            $photo = $form->get('images')->getData();

            if ($photo) {
                $images = $annonce->getImages();

                if ($images) {
                    foreach ($images as $image) {
                        $nomimage = $this->getParameter("produit_directory") . '/' . $image->getName();
                        $annonce->removeImage($image);
                        if (file_exists($nomimage)) {
                            unlink($nomimage);
                        }
                    }
                }


                $this->uploadDesImages($form, $uploadeservice, $annonce, $user, $em);

            } else {
                $annonce->setUser($user);
                $em->persist($annonce);
                $em->flush();
            }


            //  $this->addFlash('success', 'Votre annonce a bien été mis à jour ');
            toastr()->addSuccess('Votre annonce a bien été mis à jour ');
            return $this->redirectToRoute('listannonce');
        } else {
            $form->remove('reduction');

            return $this->render('annonce/update.html.twig', [
                'form' => $form->createView(),
                'annonce' => $annonce,

            ]);
        }
    }


    #[Route('/vos-favoris', name: 'favoris')]
    #[IsGranted('ROLE_USER')]
    public function favoris(AnnonceRepository $annonceRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $annoncese = $annonceRepository->Favorisannonce($this->getUser());

        $nbre = count($annoncese);


        $pagination = $paginator->paginate(
            $annoncese = $annonceRepository->Favorisannonce($this->getUser()),
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('annonce/favoris.html.twig', [

            'pagination' => $pagination,
            'nbre' => $nbre
        ]);
    }


    #[Route('/like/annonce/{id}', name: 'like')]
    #[IsGranted('ROLE_USER')]
    public function like(Annonce $annonce, EntityManagerInterface $manager): JsonResponse
    {
        $user = $this->getUser();

        if ($annonce->isLikeByUser($user)) {
            $annonce->removeLike($user);
            $manager->flush();

            return new JsonResponse(['message' => 'le lie a été ajouté',
                'likes' => count($annonce->getLikes())]);
        }

        $annonce->addLike($user);
        $manager->flush();


        return new JsonResponse(['message' => 'le lie a été ajouté',
            'likes' => count($annonce->getLikes())]);
    }

    #[Route('/deletefavoris/{id}', name: 'deletefavoris')]
    #[IsGranted('ROLE_USER')]
    public function deletefavoris(Annonce $annonce, EntityManagerInterface $manager): JsonResponse
    {
        $user = $this->getUser();


        $annonce->removeLike($user);
        $manager->flush();


        return new JsonResponse();
    }


    #[Route('/profil/{id}', name: 'profil')]
    #[IsGranted('ROLE_USER')]
    public function profil(User $user, EntityManagerInterface $manager, UserRepository $userRepository): Response
    {

        $user = $userRepository->find($user);

        return $this->render('annonce/profil.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/updateprofil/{id}', name: 'updateprofil')]
    #[IsGranted('ROLE_USER')]
    public function updateprofil(
        User                   $user,
        EntityManagerInterface $manager,
        UserRepository         $userRepository,
        Request                $request,
        SluggerInterface       $slugger
    ): Response
    {

        $photo = $request->files->get('image');
        $nom = $request->request->get('nom');
        $phone = $request->request->get('phone');
        $adresse = $request->request->get('adresse');
        $postal = $request->request->get('postal');
        $ville = $request->request->get('ville');
        $description = $request->request->get('description');

        $users = $userRepository->find($user);

        // this condition is needed because the 'brochure' field is not required
        // so the PDF file must be processed only when a file is uploaded
        if ($photo) {
            $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $photo->move(
                    $this->getParameter('photo_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $users->setPhoto($newFilename);
        }

        $users->setName($nom);
        $users->setAdresse($adresse);
        $users->setPostal($postal);
        $users->setVille($ville);
        $users->setPhone($phone);
        $users->setDescription($description);

        $manager->persist($users);
        $manager->flush();

        toastr()->addSuccess('Mise à jour du profil');

        //   $user = $userRepository->find($user)   ;

        return $this->redirectToRoute('profil', ['id' => $users->getId()]);
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @param UploadeService $uploadeservice
     * @param Annonce $annonce
     * @param \Symfony\Component\Security\Core\User\UserInterface|null $user
     * @param EntityManagerInterface $em
     * @return void
     */
    public function uploadDesImages(\Symfony\Component\Form\FormInterface $form, UploadeService $uploadeservice, Annonce $annonce, ?\Symfony\Component\Security\Core\User\UserInterface $user, EntityManagerInterface $em): void
    {
        $photo = $form->get('images')->getData();

        foreach ($photo as $i => $photos) {
            if ($photos) {
                $directory = $this->getParameter('produit_directory');
                $image = new Image();


                $image->setName($uploadeservice->uploadFile($photos, $directory, $i));

                $annonce->addImage($image);
            }
        }
        $annonce->setUser($user);
        $em->persist($annonce);
        $em->flush();
    }
}
