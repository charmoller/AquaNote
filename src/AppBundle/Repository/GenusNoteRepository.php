<?php
/**
 * Created by Nicolas LEFEVRE
 * Mail: weblefevre@gmail.com
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Genus;
use Doctrine\ORM\EntityRepository;

class GenusNoteRepository extends EntityRepository
{
    public function findAllRecentNotesForGenus(Genus $genus)
    {
        return $this->createQueryBuilder('genus_note')
            ->where('genus_note.genus = :genus')
            ->andWhere('genus_note.createdAt < :recentDate')
            ->setParameter('genus', $genus)
            ->setParameter('recentDate', new \DateTime('-3 months'))
            ->getQuery()
            ->execute();
    }
}
