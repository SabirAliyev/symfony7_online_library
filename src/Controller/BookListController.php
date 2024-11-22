<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookListController extends AbstractController
{
    #[Route('/book-list', name: 'app_book-list')]
    public function index(BookRepository $books) : Response
    {
        return $this->render('index.html.twig', [
            'books' => $books->findAll(),
        ]);
    }

    #[Route('/book-info/{book}', name: 'app_book_show')]
    public function findOne(Book $book): Response
    {
        return $this->render('show.html.twig', [
            'book' => $book,
        ]);
    }
}