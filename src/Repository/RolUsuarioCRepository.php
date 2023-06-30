<?php

namespace App\Repository;

use App\Entity\RolUsuarioComunidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RolUsuarioComunidad>
 *
 * @method RolUsuarioComunidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method RolUsuarioComunidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method RolUsuarioComunidad[]    findAll()
 * @method RolUsuarioComunidad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RolUsuarioCRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RolUsuarioComunidad::class);
    }

    public function save(RolUsuarioComunidad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RolUsuarioComunidad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return RolUsuarioComunidad[] Returns an array of RolUsuarioComunidad objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RolUsuarioComunidad
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
