<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Entity\Review;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookListController extends AbstractController
{
    #[Route('/', name: 'app_book_list')]
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
            ->add('submit', SubmitType::class, [
                'label' => 'Add Book',
                'attr' => [
                    'class' => 'btn btn-primary']
                ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $book = $form->getData();
                $book->setCreatedAt(new \DateTimeImmutable());

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

    #[Route('/book-edit/{id}', name: 'app_book_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        $book->setUpdatedAt(new \DateTimeImmutable());
        $form = $this->createFormBuilder($book)
            ->add('title')
            ->add('author')
            ->add('genre')
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 5,
                    'style' => 'white-space: pre-wrap; overflow-wrap: break-word;']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Edit Book',
                'attr' => [
                    'class' => 'btn btn-primary']
                ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();

                $this->addFlash('success', 'The book updated successfully!');
                return $this->redirectToRoute('app_book_list');
            } else {
                $this->addFlash('error', 'Something went wrong!');
            }
        }

        return $this->render('add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
