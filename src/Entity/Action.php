<?php

namespace App\Entity;

use App\Repository\ActionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActionRepository::class)
 */
class Action
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Plusieurs action ont un type
     * @ORM\ManyToOne(targetEntity="TypeAction")
     * @ORM\JoinColumn(name="typeAction_id" , referencedColumnName="id")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Abonne")
     * @ORM\JoinColumn(name="abonne_id" , referencedColumnName="id")
     */
    private $abonne;

    /**
     * @ORM\GeneratedValue
     * @ORM\Column(type="date" ,nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="time" , nullable=true)
     */
    private $heure;

    /**
     * @ORM\Column(type="time" , nullable=true)
     */
    private $dureeVolumeReelEnHeure;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $dureeVolumeFactureEnHeure;

    /**
     * @ORM\Column(type="integer" , nullable=true)
     */
    private $dureeVolumeReelData;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dureeVolumeFactureData;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }



    public function getDureeVolumeReelEnHeure(): ?\DateTimeInterface
    {
        return $this->dureeVolumeReelEnHeure;
    }

    public function setDureeVolumeReelEnHeure(?\DateTimeInterface $dureeVolumeReelEnHeure): self
    {
        $this->dureeVolumeReelEnHeure = $dureeVolumeReelEnHeure;

        return $this;
    }

    public function getDureeVolumeFactureEnHeure(): ?\DateTimeInterface
    {
        return $this->dureeVolumeFactureEnHeure;
    }

    public function setDureeVolumeFactureEnHeure(?\DateTimeInterface $dureeVolumeFactureEnHeure): self
    {
        $this->dureeVolumeFactureEnHeure = $dureeVolumeFactureEnHeure;

        return $this;
    }

    public function getDureeVolumeReelData(): ?int
    {
        return $this->dureeVolumeReelData;
    }

    public function setDureeVolumeReelData(?int $dureeVolumeReelData): self
    {
        $this->dureeVolumeReelData = $dureeVolumeReelData;

        return $this;
    }

    public function getDureeVolumeFactureData(): ?int
    {
        return $this->dureeVolumeFactureData;
    }

    public function setDureeVolumeFactureData(?int $dureeVolumeFactureData): self
    {
        $this->dureeVolumeFactureData = $dureeVolumeFactureData;

        return $this;
    }

    public function getType(): ?TypeAction
    {
        return $this->type;
    }

    public function setType(?TypeAction $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(?\DateTimeInterface $heure): self
    {
        $this->heure = $heure;

        return $this;
    }

}
