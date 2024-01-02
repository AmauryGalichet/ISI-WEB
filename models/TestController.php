<?php
// src/Controller/TestController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function test(): Response
    {
        $name = "Twig";
        $items = ["Item 1", "Item 2", "Item 3"];

        return $this->render('test.twig', [
            'name' => $name,
            'items' => $items,
        ]);
    }
}
