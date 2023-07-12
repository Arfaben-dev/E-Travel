<?php

namespace App\Tests\Unit;

use App\Entity\Transport;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TransportTest extends KernelTestCase
{
    public function getEntity()
    {

        return     (new Transport())
            ->setName('BUS')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdateAt(new \DateTimeImmutable());
    }


    public function assertHasErrors(Transport $transport, int $number = 0)
    {

        $kernel = self::bootKernel();
        $container = static::getContainer();
        $errors = $container->get('validator')->validate($transport);

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


        $transport = $this->getEntity();

        $tmp = $transport->setName('BUS');

        return $this->assertTrue($transport === $tmp);
    }
}
