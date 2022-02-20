<?php

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return float
     */
    public function sumHT($startDate,$endDate): float
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT SUM(e.valueTe)
            FROM App\Entity\Expense e
            WHERE e.issuedOn >= :startDate AND e.issuedOn <= :endDate'
        )->setParameters([
            "startDate"=>$startDate,
            "endDate"=>$endDate,
        ]);

        // returns the sum
        $sum = $query->getResult()[0][1];

        if (empty($sum)) {
            $sum = 0;
        }

        return $sum;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return float
     */
    public function sumTTC($startDate,$endDate): float
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT SUM(e.valueTi)
            FROM App\Entity\Expense e
            WHERE e.issuedOn >= :startDate AND e.issuedOn <= :endDate'
        )->setParameters([
            "startDate"=>$startDate,
            "endDate"=>$endDate,
        ]);

        // returns the sum
        $sum = $query->getResult()[0][1];

        if (empty($sum)) {
            $sum = 0;
        }

        return $sum;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function sumHtByCategory($startDate,$endDate): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e.category,SUM(e.valueTe)
            FROM App\Entity\Expense e
            WHERE e.issuedOn >= :startDate AND e.issuedOn <= :endDate
            GROUP BY e.category'
        )->setParameters([
            "startDate"=>$startDate,
            "endDate"=>$endDate,
        ]);

        // returns the sum by category
        return $query->getResult();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function sumTtcByCategory($startDate,$endDate): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e.category,SUM(e.valueTi)
            FROM App\Entity\Expense e
            WHERE e.issuedOn >= :startDate AND e.issuedOn <= :endDate
            GROUP BY e.category'
        )->setParameters([
            "startDate"=>$startDate,
            "endDate"=>$endDate,
        ]);

        // returns the sum by category
        return $query->getResult();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws Exception
     */
    public function sumHtByVehicle($startDate,$endDate): array
    {
        $entityManager = $this->getEntityManager();
        $conn = $entityManager->getConnection();

        $sql = '
            SELECT e.vehicle_id,v.plate_number,v.brand,v.model,SUM(e.value_te) AS sum FROM expense e
            JOIN vehicle v on v.vehicle_id = e.vehicle_id
            WHERE e.issued_on >= :startDate AND e.issued_on <= :endDate
            GROUP BY e.vehicle_id
            ORDER BY SUM(e.value_te) DESC
            ';
        $stmt = $conn->prepare($sql);


        // returns the sum by vehicle
        return $stmt->execute([
            'startDate'=>$startDate,
            'endDate'=>$endDate
        ])->fetchAllAssociative();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws Exception
     */
    public function sumTtcByVehicle($startDate,$endDate): array
    {
        $entityManager = $this->getEntityManager();
        $conn = $entityManager->getConnection();

        $sql = '
            SELECT e.vehicle_id,v.plate_number,v.brand,v.model,SUM(e.value_ti) AS sum FROM expense e
            JOIN vehicle v on v.vehicle_id = e.vehicle_id
            WHERE e.issued_on >= :startDate AND e.issued_on <= :endDate
            GROUP BY e.vehicle_id
            ORDER BY SUM(e.value_ti) DESC
            ';
        $stmt = $conn->prepare($sql);


        // returns the sum by vehicle
        return $stmt->execute([
            'startDate'=>$startDate,
            'endDate'=>$endDate
        ])->fetchAllAssociative();
    }
}