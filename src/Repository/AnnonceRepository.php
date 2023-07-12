<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Client\Curl\User;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Annonce>
 *
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Annonce::class);
        $this->paginatore = $paginator;
    }

    public function save(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Annonce[] Returns an array of Annonce objects
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

    public function TenAnnonce(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.created_at', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function AllAnnonce(): array
    {
        $currentDate = new \DateTime();

        return $this->createQueryBuilder('a')
            ->andWhere('a.datestart >= :currentDate ')
            ->setParameter('currentDate', $currentDate)
            ->getQuery()
            ->getResult();
    }

    public function Favorisannonce($user): array
    {

        return $this->createQueryBuilder('a')
            ->select('a','u')
            ->join('a.likes', 'u')
            ->andWhere('u.id = :user')
            ->setParameter('user',$user)
            ->getQuery()
            ->getResult();
    }

    public function FindSearch(SearchData $search)
    {
        $query=  $this->createQueryBuilder('a')
            ->select('a','t')
            ->join('a.transport','t') ;

        if (!empty($search->q)){

            $query =$query
                ->andwhere('a.description LIKE :q')
                ->orwhere('a.citystart LIKE :q')
                ->orwhere('a.cityend LIKE :q')
                ->orwhere('a.prix LIKE :q')
                ->setParameter('q',"%{$search->q}%");
        }


        if (!empty($search->min)){

            $query =$query
                ->andwhere('a.prix >= :min')
                ->setParameter('min',$search->min);
        }
        if (!empty($search->max)){

            $query =$query
                ->andwhere('a.prix <= :max')
                ->setParameter('max',$search->max);
        }
        if (!empty($search->citystart)){

            $query =$query
                ->andwhere('a.citystart LIKE :citystart')
                ->setParameter('citystart',"%{$search->citystart}%");
        }
        if (!empty($search->cityend)){

            $query =$query
                ->andwhere('a.cityend LIKE :cityend')
                ->setParameter('cityend',"%{$search->cityend}%");
        }
        if (!empty($search->datestart)){

            $query =$query
                ->andwhere('a.datestart =:datestart')
                ->setParameter('datestart',$search->datestart);
        }
        if (!empty($search->transport)){

            $query =$query
                ->andwhere('t.id IN (:transport)')
                ->setParameter('transport',$search->transport);
        }

        return $query->getQuery();

    }


    /**
     * @return  integer[]
     */
    public function findMinMax(): array
    {
        return [0, 1500];
    }

//    public function findOneBySomeField($value): ?Annonce
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
