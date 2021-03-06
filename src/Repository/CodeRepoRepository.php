<?php

namespace App\Repository;

use App\Entity\CodeRepo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CodeRepo|null find($id, $lockMode = null, $lockVersion = null)
 * @method CodeRepo|null findOneBy(array $criteria, array $orderBy = null)
 * @method CodeRepo[]    findAll()
 * @method CodeRepo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeRepoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodeRepo::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CodeRepo $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(CodeRepo $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findAll()
    {
        return $this->findBy(array(), array('creation_date' => 'DESC'));
    }
}
