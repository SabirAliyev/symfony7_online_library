<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookListController extends AbstractController
{
    #[Route('/book-list', name: 'book-list')]
    public function index(BookRepository $books) : Response
    {
        dd($books->findAll());
//        return $this->render('book_list/index.html.twig', [
//            'controller_name' => 'BookListController',
//        ]);
    }

    #[Route('/book-get/{id}', name: 'book-get/{id}')]
    public function getOne(BookRepository $book): Response
    {
        dd($book->findAll(['id' => '1']));
    }

    #[Route('/book-info/{title}', name: 'book-info/{title}')]
    public function findOne(BookRepository $book): Response
    {
        dd($book->findOneBy(['title' => 'The Great Gatsby']));
    }
}