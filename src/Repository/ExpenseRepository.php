<?php

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    /**
     * @return float
     */
    public function sumHT(): float
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT SUM(e.valueTe)
            FROM App\Entity\Expense e'
        );

        // returns the sum
        $sum = $query->getResult()[0][1];

        if (empty($sum)) {
            $sum = 0;
        }

        return $sum;
    }

    /**
     * @return float
     */
    public function sumTTC(): float
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT SUM(e.valueTi)
            FROM App\Entity\Expense e'
        );

        // returns the sum
        $sum = $query->getResult()[0][1];

        if (empty($sum)) {
            $sum = 0;
        }

        return $sum;
    }

    /**
     * @return array
     */
    public function sumHtByCategory(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e.category,SUM(e.valueTe)
            FROM App\Entity\Expense e
            GROUP BY e.category'
        );

        // returns the sum by category
        return $query->getResult();
    }

    /**
     * @return array
     */
    public function sumTtcByCategory(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e.category,SUM(e.valueTi)
            FROM App\Entity\Expense e
            GROUP BY e.category'
        );

        // returns the sum by category
        return $query->getResult();
    }
}