<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function test(): Response
    {
        return $this->json(['msg' => 'foo']);
    }

    #[Route('/api/test', name: 'test')]
    public function testApi(): Response
    {
        return $this->json(['msg' => 'foo']);
    }
}
