<?php

namespace App\Entity;

use App\Repository\ContratsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContratsRepository::class)
 */
class Contrats
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $numero;

    /**
     * @ORM\Column(type="date")
     */
    private $date_debut;

    /**
     * @ORM\Column(type="date")
     */
    private $date_fin;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $centre_de_cout;

    /**
     * @ORM\ManyToOne(targetEntity=Pigistes::class, inversedBy="contrats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pigiste;

    /**
     * @ORM\ManyToOne(targetEntity=Tarifs::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $tarif;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Motif;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function __construct($numero)
    {
        $this->setNumero($numero);
        $this->setActive(true);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getCentreDeCout(): ?string
    {
        return $this->centre_de_cout;
    }

    public function setCentreDeCout(string $centre_de_cout): self
    {
        $this->centre_de_cout = $centre_de_cout;

        return $this;
    }

    public function getPigiste(): ?Pigistes
    {
        return $this->pigiste;
    }

    public function setPigiste(?Pigistes $pigiste): self
    {
        $this->pigiste = $pigiste;

        return $this;
    }

    public function getTarif(): ?Tarifs
    {
        return $this->tarif;
    }

    public function setTarif(?Tarifs $tarif): self
    {
        $this->tarif = $tarif;

        return $this;
    }

    public function getNbJours()
    {
        $date1 = $this->getDateDebut();
        $date2 = $this->getDateFin();
        $interval = $date1->diff($date2);
        return $interval->d + 1;
    }

    public function getMotif(): ?string
    {
        return $this->Motif;
    }

    public function setMotif(string $Motif): self
    {
        $this->Motif = $Motif;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
