<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route('/book-add', name: 'app_book_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $user = $entityManager->getRepository(User::class)->find(1);
        $book->setAddedBy($user);

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $book = $form->getData();

                $coverFile = $form->get('coverImage')->getData();
                if ($coverFile) {
                    $coverFileName = md5(uniqid()) . '.' . $coverFile->guessExtension();

                    try {
                        $coverFile->move(
                            $this->getParameter('cover_image_directory'),
                            $coverFileName
                        );
                        $book->setCoverImage($coverFileName);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Upload image file failed!');
                    }
                }

                $entityManager->persist($book);
                $entityManager->flush();

                $this->addFlash('success', 'New Book added successfully!');
                return $this->redirectToRoute('app_book_list');
            } else {
                $this->addFlash('error', 'Something went wrong!');
            }
        }

        return $this->render('add.html.twig', [
            'form' => $form,
            'book' => $book
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route('/book-edit/{id}', name: 'app_book_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverFile = $form->get('coverImage')->getData();
            if ($coverFile) {
                $coverFileName = md5(uniqid()) . '.' . $coverFile->guessExtension();

                try {
                    $coverFile->move(
                        $this->getParameter('cover_image_directory'),
                        $coverFileName
                    );
                    $book->setCoverImage($coverFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Upload image file failed!');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'The book updated successfully!');

            return $this->redirectToRoute('app_book_list');
        }

        return $this->render('add.html.twig', [
            'form' => $form->createView(),
            'book' => $book
        ]);
    }

    #[Route('/book-delete/{id}', name: 'app_book_delete')]
    public function delete(BookRepository $bookRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book not found');
        }

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('app_book_list');
    }
}