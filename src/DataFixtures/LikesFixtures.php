<?php

namespace App\DataFixtures;

use App\Repository\AnnonceRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LikesFixtures extends Fixture
{
     public function __construct(private  AnnonceRepository $annonceRepository , private  UserRepository $userRepository)
     {
     }


    public function load(ObjectManager $manager): void
    {

           $user = $this->userRepository->findAll();
           $annonce = $this->annonceRepository->findAll();

           foreach ($annonce as $annonces)
           {
               for ($i= 0 ; $i<mt_rand(0, 15); $i++)
               {
                   $annonces->addLike(
                        $user[mt_rand(0, 2)]
                   );
               }
           }

           $manager->flush();
    }


}
