<?php

namespace App\Tests\Unit;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function getEntity()
    {

            return (new User())
                ->setName('name1')
                ->setPhoto('photo1')
                ->setPassword('password1')
                ->setEmail('email1')
                ->setDescription('description1')
                ->setPhone('phone1')
                ->setPostal('postal1')
                ->setAdresse('adresse2')
                ->setRoles(['ROLE_client'])
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdateAt(new \DateTimeImmutable());
    }


    public function assertHasErrors(User $user, int $number = 0)
    {

        $kernel = self::bootKernel();
        $container = static::getContainer();
        $errors = $container->get('validator')->validate($user);

        return $this->assertCount($number, $errors);
    }
    public function testEntityIsValid(): void
    {

        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidName()
    {
        $this->assertHasErrors($this->getEntity()->setName(''), 0);
    }


    public function testGetTransports()
    {

        // $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 1);


        $user = $this->getEntity();

        $tmp = $user->setName('brice');

        return $this->assertTrue($user === $tmp);
    }
}