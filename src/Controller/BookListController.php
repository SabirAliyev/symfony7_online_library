<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookListController extends AbstractController
{
    #[Route('/book-list', name: 'app_book_list')]
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

    #[Route('/book-add', name: 'app_book_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $user = $entityManager->getRepository(User::class)->find(1);
        $book->setAddedBy($user);

        $form = $this->createFormBuilder($book)
            ->add('title')
            ->add('author')
            ->add('genre')
            ->add('description')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $book = $form->getData();
                $book->setCreatedAt(new \DateTime());

                $entityManager->persist($book);
                $entityManager->flush();

                $this->addFlash('success', 'New Book added successfully!');
                return $this->redirectToRoute('app_book_list');
            } else {
                $this->addFlash('error', 'Something went wrong!');
            }
        }

        return $this->render('add.html.twig', [
            'form' => $form
        ]);
    }
}
