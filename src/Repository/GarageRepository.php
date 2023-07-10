<?php

namespace App\Repository;

use App\Entity\Garage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Garage>
 *
 * @method Garage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Garage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Garage[]    findAll()
 * @method Garage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GarageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Garage::class);
    }

    public function add(Garage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Garage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find all Garage by name
     *
     * @param string $keyword
     * @return array|null
     */
    public function findGarageByName(string $keyword): ?array
    {
        return $this->createQueryBuilder('g')
            ->where("g.name LIKE :keyword")
            ->setParameter('keyword', "%{$keyword}%")
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all Garage by Address
     *
     * @param float $lat
     * @param float $lon
     * @param integer $rad
     * @return array|null
     */
    public function findGarageByAddress(float $lat, float $lon, int $rad): ?array
    {
        $sql = "
            SELECT
                garage.id, garage.address_id, garage.name, garage.register_number, garage.phone, garage.email, garage.rating, garage.created_at, garage.updated_at,
                address.number, address.type, address.name AS address_name, address.postal_code, address.created_at, address.updated_at,
                (
                6371 * acos (
                    cos ( radians( :lat ) )
                    * cos( radians( latitude ) )
                    * cos( radians( longitude ) - radians( :lon ) )
                    + sin ( radians( :lat ) )
                    * sin( radians( latitude ) )
                )
                ) AS distance
            FROM garage
            LEFT JOIN address ON garage.address_id = address.id
            HAVING distance < 100
            ORDER BY distance
            LIMIT 10;
        ";

        $connexion = $this->getEntityManager()->getConnection();
        $stmt = $connexion->prepare($sql);
        $result = $stmt->executeQuery(["lat" => $lat, "lon" => $lon, "rad" => $rad]);

        return $result->fetchAllAssociative();
    }


//    public function findOneBySomeField($value): ?Garage
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
