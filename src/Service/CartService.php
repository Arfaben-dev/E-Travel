<?php

namespace App\Service;

use App\Repository\ProduitRepository;
use KnpU\OAuth2ClientBundle\Client\Provider\PaypalClient;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\SandboxExtension;

class CartService
{
    public function rand($length)
    {

        $chars = "1234ABCDRUJLQOOCE56789";
        return substr(str_shuffle($chars), 0, $length);
    }
    public function place($length)
    {

        $number = mt_rand(1, $length);

        return $number;
    }
}
