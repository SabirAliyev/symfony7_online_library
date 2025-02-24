<?php

namespace App\Controller\API;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/books', name: 'api_books_')]
class BookApiController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $books = $bookRepository->findAll();
        $data = $serializer->serialize($books, 'json', ['groups' => 'book:read']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request, BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $title = $request->query->get('title');
        $author = $request->query->get('author');
        $genre = $request->query->get('genre');

        $books = $bookRepository->searchBooks($title, $author, $genre);
        $data = $serializer->serialize($books, 'json', ['groups' => 'book:read']);

        return new JsonResponse($data, 200, [], true);
    }

}