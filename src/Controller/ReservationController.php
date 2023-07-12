<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Place;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\AnnonceRepository;
use App\Repository\PlaceRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

//#[IsGranted('ROLE_CLIENT')]
class ReservationController extends AbstractController
{
    public function getPaypalClient(): PayPalHttpClient
    {
        $clientid = "AaEhQkYpIMCJMlmgZAwgSSGIf6WuBxO6jUJKU-CPtj5WESVnkH8tNgis2WGVOWZ8H7Rqu-XtRgl_oTaQ";
        $clientsecret = "EAdRcmKmxhwHp8fCje7W24vHc_8KZeRIJC22oZ7mj7aZEcpsYdKEsgIRa_aOTl7Phcu-a7dyu72JqFFm";
        $env = new SandboxEnvironment($clientid, $clientsecret);
        return new PayPalHttpClient($env);
    }


    #[Route('/reservation/panier/annonce={id}', name: 'panier')]
    public function index(Annonce $annonce, AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->find($annonce);

        return $this->render('reservation/cart.html.twig', [

            'annonces' => $annonces,
            //      'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/calculate-total-price", name="calculate", methods={"POST"})
     */
    public function calculateTotalPrice(Request $request, AnnonceRepository $annonceRepository): JsonResponse
    {
        $quantity = $request->request->getInt('quantity');
        $productId = $request->request->getInt('productId');
        $annonce = new Annonce();


        // Effectuer le calcul du prix total en fonction de la quantité et de l'identifiant du produit
        $product = $annonceRepository->find($productId);
        $totalPrice = $product->getPrix() * $quantity;


        $session = $request->getSession();


        // on sauvergarde dans le panier

        $session->set("pannier", $quantity);

        $session->set("total", $totalPrice);
        // Retourner la réponse JSON avec le prix total calculé
        return new JsonResponse(['totalPrice' => $totalPrice, 'place' => $quantity]);
    }

    #[Route('/reservation/informations-passagers/annonce={id}', name: 'paie')]
    public function paie(
        Annonce $annonce,
        AnnonceRepository $annonceRepository,
        SessionInterface $session,
        Request $request,
        EntityManagerInterface $em,
        CartService $cartService
    ): Response {
        $session = $request->getSession();

        $annonces = $annonceRepository->find($annonce);

        $session->set("nbre", $annonces->getId());

        $session->remove("nbre");

        return $this->render('reservation/payment.html.twig', [

            'annonces' => $annonces,
            'ok' => $session->get('pannier')
        ]);
    }

    #[Route('/reservation/paiement/annonce={id}', name: 'paieplace')]
    public function paiees(
        Annonce $annonce,
        AnnonceRepository $annonceRepository,
        SessionInterface $session,
        Request $request,
        EntityManagerInterface $em,
        CartService $cartService,
        ReservationRepository $reservationRepository
    ): Response {

        $nom = $request->get('name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $ref = $cartService->rand(6);
        $reservation = new Reservation();
        $reservation->setUserreservation($this->getUser());
        $reservation->setAnnoncereservation($annonce);
        $reservation->setNbreplace($session->get('pannier'));
        $reservation->setPrix($session->get('total'));
        $reservation->setStatut(0);
        $reservation->setRef($ref);
        $reservation->setIsPaid(false);

        $em->persist($reservation);
        $em->flush();
        $idreservation = $reservationRepository->findOneBy(['ref' => $ref]);


        foreach ($nom as $key => $value) {
            $place = new Place();
            $place->setNom($value);
            $place->setEmail($email[$key]);
            $place->setPhone($phone[$key]);
            $place->setNumplace($cartService->place($annonce->getPlaceDispo()));
            $place->setReservation($idreservation);
            $em->persist($place);
            $em->flush();
        }

        //  $method = $request->request->get('method');

        return $this->redirectToRoute('recapitulatif', ['ref' => $ref]);
    }


    #[Route('Recapitulatif/{ref}', name: 'recapitulatif')]
    public function recap($ref, ReservationRepository $reservationRepository, PlaceRepository $placeRepository, AnnonceRepository $annonceRepository): Response
    {
        $reservation = $reservationRepository->findOneBy(['ref' => $ref]);
        $annonces = $annonceRepository->find($reservation->getAnnoncereservation());
        $place = $placeRepository->findBy(['reservation' => $reservation->getId()]);

        return $this->render('reservation/recap.html.twig', [
            'reservation' => $reservation,
            'place' => $place,
            'annonces' => $annonces
        ]);
    }

    #[Route('/paiement/stripe/{ref}', name: 'paie_stripe')]
    public function stripe(
        $ref,
        ReservationRepository $reservationRepository,
        AnnonceRepository $annonceRepository,
        EntityManagerInterface $em
    ): RedirectResponse {
        $productstripe = [];
        $reservation = $reservationRepository->findOneBy(["ref" => $ref]);
        $id = $reservationRepository->find($reservation->getId());
        $anonce = $annonceRepository->find($reservation->getAnnoncereservation());

        $id->setMethod('stripe');

        $em->persist($id);
        $em->flush();
//pk_test_51NJd3iIirVi57Olg9YE056NqQk0LLmNb4fwZMdn2ZsXj58Bb8fdY7tm7qvmrcT7l0SUlR7wc0slimgRSw7ac7e9200eJHBnw7y

        $stripeSecretKey = 'sk_test_51NJd3iIirVi57Olgzs9N0aojHE9MAoHrkg7NMJMGeXAoIjY4XwKtCObbUBfzAIBYjJpWUF0p9j7l4npSBsozPYXa00EY0jN82O';
        Stripe::setApiKey($stripeSecretKey);

        $productstripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => ($reservation->getPrix() / 2) * 100,

                'product_data' => [
                    'name' => 'Billet de voyage',
                ]
            ],

            'quantity' => $reservation->getNbreplace(),
        ];

        $productstripe [] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => 0,
                'product_data' => [
                    'name' => 'Frais',
                ],
            ],

            'quantity' => 1,
        ];


        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $productstripe,
            ],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('stripesucess', [
                'ref' => $ref
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('stripecancel', [
                'ref' => $ref
            ], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);


        //  dd($checkout_session);
        return new RedirectResponse($checkout_session->url);
    }


    #[Route('/paiement/paypal/{ref}', name: 'paie_paypal')]
    public function paypal(
        $ref,
        ReservationRepository $reservationRepository,
        AnnonceRepository $annonceRepository,
        EntityManagerInterface $em
    ): RedirectResponse {
        $reservation = $reservationRepository->findOneBy(["ref" => $ref]);
        $id = $reservationRepository->find($reservation->getId());
        $anonce = $annonceRepository->find($reservation->getAnnoncereservation());

        $id->setMethod('paypal');

        $em->persist($id);
        $em->flush();

        $items = [];

        $items[] = [

            'name' => 'Billet de voyage',
            'quantity' => $reservation->getNbreplace(),

            'unit_amount' => [
                'value' => $anonce->getPrix(),
                'currency_code' => 'EUR',
            ]
        ];

        $itemTotal = $anonce->getPrix() * $reservation->getNbreplace();
        $request = new OrdersCreateRequest();
        $request->prefer("return=representation");
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "EUR",
                        "value" => $itemTotal,
                        "breakdown" => [
                            'item_total' => [
                                "currency_code" => "EUR",
                                "value" => $itemTotal,
                            ],
                            "shipping" => [
                                "currency_code" => "EUR",
                                "value" => 0,
                            ]
                        ]
                    ],
                    "items" => $items
                ]
            ],
            "application_context" => [
                "cancel_url" => $this->generateUrl('stripecancel', [
                    'ref' => $ref
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                "return_url" => $this->generateUrl('stripesucess', [
                    'ref' => $ref
                ], UrlGeneratorInterface::ABSOLUTE_URL)
            ]
        ];


        $client = $this->getPaypalClient();
        $response = $client->execute($request);

        $approvalink = ' ';

        foreach ($response->result->links as $links) {
            if ($links->rel === 'approve') {
                $approvalink = $links->href;
                break;
            }
        }

        if (empty($approvalink)) {
            return $this->redirectToRoute('recapitulatif', ['ref' => $ref]);
        }

        return new RedirectResponse($approvalink);
    }

    #[Route('/paiement/sucess/{ref}', name: 'stripesucess')]
    public function finishsucess(
        $ref,
        ReservationRepository $reservationRepository,
        AnnonceRepository $annonceRepository,
        EntityManagerInterface $em,
        SessionInterface $session
    ): RedirectResponse {
        $reservation = $reservationRepository->findOneBy(["ref" => $ref]);
        $id = $reservationRepository->find($reservation->getId());


        $id->setIsPaid(1);
        $em->persist($id);
        $em->flush();

        $annonces = $annonceRepository->find($reservation->getAnnoncereservation());
        $annonces->setPlaceprise($annonces->getPlaceprise() + $session->get('pannier'));
        $em->persist($annonces);
        $em->flush();

        $session->remove("pannier");


        return $this->redirectToRoute('finish');
    }

    #[Route('/paiement/cancel/{ref}', name: 'stripecancel')]
    public function finishcancel($ref): Response
    {


        return $this->render('reservation/cancel.html.twig');
    }


    #[Route('/confirmation', name: 'finish')]
    public function paiee(): Response
    {


        return $this->render('reservation/finish.html.twig');
    }

    #[Route('/mes-reservations', name: 'reservation')]
    public function reservation(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findBy(['userreservation' => $this->getUser(), 'isPaid' => true]);
        $currentDate = new \DateTime();

        return $this->render('reservation/mesreservation.html.twig', [

            'reservations' => $reservations,
            'currentdate' => $currentDate
        ]);
    }

    #[Route('/detail/reservation={id}', name: 'detailreservation')]
    public function voir(Reservation $reservation, ReservationRepository $reservationRepository, UserRepository $userRepository, PlaceRepository $placeRepository, AnnonceRepository $annonceRepository): Response
    {
        $reservations = $reservationRepository->find($reservation);
        $user = $userRepository->find($reservation->getUserreservation());
        $place = $placeRepository->findBy(['reservation' => $reservation]);
        $annonce = $annonceRepository->find($reservation->getAnnoncereservation());

        $pdfoptions = new Options();

        $pdfoptions->setIsRemoteEnabled(true);
        $pdfoptions->setIsHtml5ParserEnabled(true);
        $pdfoptions->setTempDir('temp');
        $dompdf = new Dompdf();
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        $dompdf->setHttpContext($context);

        // On génère le html
        $html = $this->renderView('reservation/recu.html.twig', [

            'reservation' => $reservations,
            'annonce' => $annonce,
            'user' => $user,
            'places' => $place
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // On génère un nom de fichier
        $fichier = 'reservation-' . $reservation->getRef() . '.pdf';

        // On envoie le PDF au navigateur
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);


        return new Response();
    }


    #[Route('/voir/annonce={id}', name: 'client')]
    public function listclient(Annonce $annonce, ReservationRepository $reservationRepository, UserRepository $userRepository, PlaceRepository $placeRepository, AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->find($annonce);
        $reservations = $reservationRepository->findBy(['annoncereservation' => $annonce, 'isPaid' => true, 'statut' => 0]);
        $place = $placeRepository->findBy(['reservation' => $reservations]);
        ;

        $pdfoptions = new Options();

        $pdfoptions->setIsRemoteEnabled(true);
        $pdfoptions->setIsHtml5ParserEnabled(true);
        $pdfoptions->setTempDir('temp');
        $dompdf = new Dompdf();
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        $dompdf->setHttpContext($context);

        // On génère le html
        $html = $this->renderView('reservation/listclient.html.twig', [

            'reservations' => $reservations,
            'annonce' => $annonces,
            'places' => $place
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // On génère un nom de fichier
        $fichier = 'Liste-des-clients.pdf';

        // On envoie le PDF au navigateur
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);


        return new Response();
    }


    #[Route('/annuler/{id}', name: 'cancelreservation')]
    public function cancel(Reservation $reservation, ReservationRepository $reservationRepository, EntityManagerInterface $manager, AnnonceRepository $annonceRepository): Response
    {

        $reservations = $reservationRepository->find($reservation);
        $annonce = $annonceRepository->find($reservation->getAnnoncereservation());
        $annonce->setPlaceprise($annonce->getPlaceprise() - $reservations->getNbreplace());
        $annonce->setPlaceDispo($annonce->getPlaceDispo() + $reservations->getNbreplace());
        $reservations->setStatut(1);
        $manager->persist($reservations);
        $manager->flush();
        $manager->persist($annonce);
        $manager->flush();

        toastr()->addSuccess('Votre reservation a été annulée');

        return $this->redirectToRoute('reservation');
    }
}
