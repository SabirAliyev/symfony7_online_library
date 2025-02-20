<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReviewController extends AbstractController
{
    #[Route('/book-review/{id}', name: 'app_book_review')]
    public function add(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($id);
        $user = $this->getUser();

        if (!$user) {
            $request->getSession()->getFlashBag()->clear();
            $this->addFlash('error', 'You need to login to review a book.');
            return $this->redirectToRoute('app_book_show', ['id' => $id]);
        }

        $reviewRepository = $entityManager->getRepository(Review::class);
        $existingReview = $reviewRepository->findOneBy([
            'user' => $this->getUser(),
            'book' => $book
        ]);

        if ($existingReview) {
            $this->addFlash('error', 'You have already reviewed this book.');
            return $this->redirectToRoute('app_book_show', ['id' => $id]);
        }

        $review = new Review();
        $review->setBook($book);
        $review->setUser($user);

        $form = $this->createFormBuilder($review)
            ->add('rating')
            ->add('comment', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 5,
                    'style' => 'white-space: pre-wrap; overflow-wrap: break-word;']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Add Review',
                'attr' => [
                    'class' => 'btn btn-primary']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $review = $form->getData();

                $entityManager->persist($review);
                $entityManager->flush();

                $this->addFlash('success', 'New Review added successfully!');
                return $this->redirectToRoute('app_book_show', ['id' => $id]);
            } else {
                $this->addFlash('error', 'Something went wrong!');
            }
        }

        return $this->render('review.html.twig', [
            'form' => $form,
            'book' => $book
        ]);
    }

    #[Route('/review-delete/{id}', name: 'app_review_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $review = $entityManager->getRepository(Review::class)->find($id);
        $user = $this->getUser();

        if (!$review) {
            throw $this->createNotFoundException('The review does not exist');
        }

        if ($user !== $review->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'You are not allowed to delete this review.');
            return $this->redirectToRoute('app_book_show', ['id' => $review->getBook()->getId()]);
        }

        try {
            $entityManager->remove($review);
            $entityManager->flush();
            $this->addFlash('success', 'Review deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Something went wrong!');
        }

        return $this->redirectToRoute('app_book_show', ['id' => $review->getBook()->getId()]);
    }
}