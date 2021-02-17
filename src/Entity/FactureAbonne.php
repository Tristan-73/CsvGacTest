<?php

namespace App\Entity;

use App\Repository\FactureAbonneRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FactureAbonneRepository::class)
 */
class FactureAbonne
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Abonne")
     * @ORM\JoinColumn(name = "abonne_id" , referencedColumnName="id")
     */
    private $abonne;

    /**
     * @ORM\ManyToOne(targetEntity="Facture" )
     * @ORM\JoinColumn(name = "facture_id" , referencedColumnName="id")
     */
    private $facture;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbonne(): ?Abonne
    {
        return $this->abonne;
    }

    public function setAbonne(?Abonne $abonne): self
    {
        $this->abonne = $abonne;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): self
    {
        $this->facture = $facture;

        return $this;
    }
}
