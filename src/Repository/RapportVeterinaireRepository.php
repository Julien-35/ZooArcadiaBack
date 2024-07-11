<?php

namespace App\Repository;

use App\Entity\RapportVeterinaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

// if (isset($_SERVER['HTTP_ORIGIN'])) {
//     // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
//     // you want to allow, and if so:
//     header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
//     header('Access-Control-Allow-Credentials: true');
//     header('Access-Control-Max-Age: 86400');    // cache for 1 day
// }

// // Access-Control headers are received during OPTIONS requests
// if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    
//     if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
//         // may also be using PUT, PATCH, HEAD etc
//         header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    
//     if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
//         header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

//     exit(0);
// }

/**
 * @extends ServiceEntityRepository<RapportVeterinaire>
 *
 * @method RapportVeterinaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method RapportVeterinaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method RapportVeterinaire[]    findAll()
 * @method RapportVeterinaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RapportVeterinaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RapportVeterinaire::class);
    }

//    /**
//     * @return RapportVeterinaire[] Returns an array of RapportVeterinaire objects
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

//    public function findOneBySomeField($value): ?RapportVeterinaire
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
