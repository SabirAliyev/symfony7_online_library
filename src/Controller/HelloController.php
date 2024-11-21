<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HelloController
{
    #[Route('/', name: 'hello')]
    public function index() : Response
    {
        return new Response("Hello World!");
    }



}