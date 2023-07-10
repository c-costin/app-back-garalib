<?php

namespace App\Repository;

use App\Entity\Address;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Address>
 *
 * @method Address|null find($id, $lockMode = null, $lockVersion = null)
 * @method Address|null findOneBy(array $criteria, array $orderBy = null)
 * @method Address[]    findAll()
 * @method Address[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Address::class);
    }

    public function add(Address $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Address $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find Address by User ID
     *
     * @param integer $id
     * @return array|null
     */
    public function findAddressByUserId(int $id): ?array
    {
        $connexion = $this->getEntityManager()->getConnection();

        $sql  = "
            SELECT address.id, address.number, address.type, address.name, address.town, address.postal_code, address.created_at, address.updated_at
            FROM address
            LEFT JOIN user ON address.id = user.address_id
            where user.id = :id
        ";

        $result = $connexion->executeQuery($sql, ['id' => $id]);

        return $result->fetchAllAssociative();
    }

    /**
     * Undocumented function
     *
     * @param integer $id
     * @return array|null
     */
    public function findAddressByGarageId(int $id): ?array
    {
        $connexion = $this->getEntityManager()->getConnection();

        $sql  = "
            SELECT address.id, address.number, address.type, address.name, address.town, address.postal_code, address.created_at, address.updated_at
            FROM address
            LEFT JOIN garage ON address.id = garage.address_id
            where garage.id = :id
        ";

        $result = $connexion->executeQuery($sql, ['id' => $id]);

        return $result->fetchAllAssociative();
    }

//    /**
//     * @return Address[] Returns an array of Address objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Address
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
