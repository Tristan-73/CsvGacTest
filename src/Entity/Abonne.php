<?php

namespace App\Entity;

use App\Repository\AbonneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AbonneRepository::class)
 */
class Abonne
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero;

    /**
     * Un abonné a un compte facturé
     * @ORM\ManyToOne(targetEntity="CompteFacture" )
     * @ORM\JoinColumn(name="compte_id" , referencedColumnName="id")
     */
    private $compte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCompte(): ?CompteFacture
    {
        return $this->compte;
    }

    public function setCompte(?CompteFacture $compte): self
    {
        $this->compte = $compte;

        return $this;
    }
}
