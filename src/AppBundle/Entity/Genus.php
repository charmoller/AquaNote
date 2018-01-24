<?php
/**
 * Created by Nicolas LEFEVRE
 * Mail: weblefevre@gmail.com
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GenusRepository")
 * @ORM\Table(name="genus")
 */
class Genus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $subFamily;

    /**
     * @ORM\Column(type="integer")
     */
    private $speciesCount;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $funFact;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished = true;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\GenusNote", mappedBy="genus")
     * @ORM\OrderBy({"createdAt"="DESC"})
     */
    private $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSubFamily(): string
    {
        return $this->subFamily;
    }

    public function setSubFamily(string $subFamily)
    {
        $this->subFamily = $subFamily;
    }

    public function getSpeciesCount(): int
    {
        return $this->speciesCount;
    }

    public function setSpeciesCount(int $speciesCount)
    {
        $this->speciesCount = $speciesCount;
    }

    public function getFunFact(): ?string
    {
        return '**TEST** ' . $this->funFact;
    }

    public function setFunFact(string $funFact)
    {
        $this->funFact = $funFact;
    }

    public function getUpdatedAt(): \DateTime
    {
        return new \DateTime('-'.rand(0, 100).' days');
    }

    public function setIsPublished(bool $isPublished)
    {
        $this->isPublished = $isPublished;
    }

    /**
     * @return ArrayCollection|GenusNote[]
     */
    public function getNotes()
    {
        return $this->notes;
    }
}
