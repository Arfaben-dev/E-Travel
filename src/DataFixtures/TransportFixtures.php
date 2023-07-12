<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\Transport;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TransportFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         $data=[

             'BUS',
             'AVION',
         ];

          for ($i=0;$i<count($data);$i++)
          {
               $role = new Transport();
               $role->setName($data[$i]);
               $manager->persist($role);
              $manager->flush();
          }




    }
}
