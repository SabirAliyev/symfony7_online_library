<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return Book[] Find books by keyword
     */
    public function searchByKeyword($keyword): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.title LIKE :keyword')
            ->orWhere('b.author LIKE :keyword')
            ->orWhere('b.genre LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
            ->orderBy('b.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchBooks($title, $author, $genre)
    {
        $qb = $this->createQueryBuilder('b');

        if ($title) {
            $qb->andWhere('b.title LIKE :title')
                ->setParameter('title', '%' . $title . '%');
        }
        if ($author) {
            $qb->andWhere('b.author LIKE :$author')
                ->setParameter('author', '%' . $author . '%');
        }
        if ($genre) {
            $qb->andWhere('b.genre LIKE :$genre')
                ->setParameter('genre', '%' . $genre);
        }

        return $qb->getQuery()->getResult();
    }

//        public function findOneBySomeField($value): ?Book
//        {
//            return $this->createQueryBuilder('b')
//                ->andWhere('b.exampleField = :val')
//                ->setParameter('val', $value)
//                ->getQuery()
//                ->getOneOrNullResult()
//            ;
//        }
}
