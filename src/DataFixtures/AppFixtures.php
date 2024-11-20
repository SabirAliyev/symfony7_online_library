<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Book;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create Users
        $user1 = new User();
        $user1->setEmail('user1@example.com');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'password123'));
        $user1->setRoles(['ROLE_USER']);
        $user1->setName('Alice Smith');
        $user1->setRegisteredAt(new \DateTime());
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('admin@example.com');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'admin123'));
        $user2->setRoles(['ROLE_ADMIN']);
        $user2->setName('Bob Johnson');
        $user2->setRegisteredAt(new \DateTime());
        $manager->persist($user2);

        // Create Books
        $book1 = new Book();
        $book1->setTitle('The Great Gatsby');
        $book1->setAuthor('F. Scott Fitzgerald');
        $book1->setDescription('A novel about the American Dream.');
        $book1->setYear(1925);
        $book1->setGenre('Fiction');
        $book1->setPageCount(180);
        $book1->setCoverImage('path/to/gatsby.jpg');
        $book1->setAddedBy($user2);
        $book1->setCreatedAt(new \DateTime());
        $manager->persist($book1);

        $book2 = new Book();
        $book2->setTitle('1984');
        $book2->setAuthor('George Orwell');
        $book2->setDescription('A dystopian novel about totalitarianism.');
        $book2->setYear(1949);
        $book2->setGenre('Science Fiction');
        $book2->setPageCount(328);
        $book2->setCoverImage('path/to/1984.jpg');
        $book2->setAddedBy($user2);
        $book2->setCreatedAt(new \DateTime());
        $manager->persist($book2);

        // Create Reviews
        $review1 = new Review();
        $review1->setRating(5);
        $review1->setComment('An amazing portrayal of the American Dream!');
        $review1->setCreatedAt(new \DateTime());
        $review1->setBook($book1);
        $review1->setUser($user1);
        $manager->persist($review1);

        $review2 = new Review();
        $review2->setRating(4);
        $review2->setComment('A chilling dystopian vision of the future.');
        $review2->setCreatedAt(new \DateTime());
        $review2->setBook($book2);
        $review2->setUser($user1);
        $manager->persist($review2);

        // Flush all changes to the database
        $manager->flush();
    }
}

