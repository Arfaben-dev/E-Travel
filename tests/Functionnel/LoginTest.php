<?php

namespace App\Tests\Functionnel;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends WebTestCase
{
    public function testIfLoginIsSuccessful(): void
    {
        $client = static::createClient();

        //Get route by urlgenerator

        /** @var UrlGeneratorInterface $urlGenerator*/
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $urlGenerator->generate('app_login'));
        //Form

        $form = $crawler->filter("form[name=login]")->form([
            "email" => "test@gmail.com",
            "password" => "password12",

        ]);


        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

    }
}
