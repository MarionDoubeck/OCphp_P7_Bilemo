<?php

namespace App\Repository;

use App\Entity\Consumer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Consumer>
 *
 * @method Consumer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Consumer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Consumer[]    findAll()
 * @method Consumer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsumerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consumer::class);

    }//end __construct()


    /**
     * Finds all consumers associated with a specific partner and applies pagination.
     *
     * @param int $partnerId The ID of the partner
     * @param int $page The page number
     * @param int $limit The maximum number of consumers per page
     * @return Consumer[] A paginated list of consumers associated with the partner
     */
    public function findAllByPartnerIdWithPagination(int $partnerId, int $page, int $limit): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.partner = :partnerId')
            ->setParameter('partnerId', $partnerId)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();

    }//end findAllByPartnerIdWithPagination()


}
