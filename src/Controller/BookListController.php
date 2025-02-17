<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Review;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookListController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BookRepository $books) : Response
    {
        return $this->render('index.html.twig', [
            'books' => $books->findAll(),
        ]);
    }

    #[Route('/book-info/{id}', name: 'app_book_show')]
    public function findOne(int $id, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book not found');
        }

        $reviews = $book->getReviews();

        return $this->render('show.html.twig', [
            'book' => $book,
            'reviews' => $reviews
        ]);
    }

    private function getBookReview(int $id, EntityManagerInterface $entityManager) : array
    {
        return $entityManager->getRepository(Review::class)->findBy(['book' => $id]);
    }

    #[Route('/catalog', name: 'app_book_list')]
    public function list(BookRepository $bookRepository) : Response
    {
        return $this->render('catalog.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

}
