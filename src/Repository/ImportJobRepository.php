<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Repository;

use Camelot\ApiImporter\Entity\ImportJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImportJob>
 *
 * @method null|ImportJob find($id, $lockMode = null, $lockVersion = null)
 * @method null|ImportJob findOneBy(array $criteria, array $orderBy = null)
 * @method ImportJob[]    findAll()
 * @method ImportJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportJob::class);
    }

    public function save(ImportJob $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ImportJob $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
