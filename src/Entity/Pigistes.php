<?php

namespace App\Entity;

use App\Repository\PigistesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PigistesRepository::class)
 */
class Pigistes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="date")
     */
    private $date_de_naissance;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lieu_de_naissace;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $code_postal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=21)
     */
    private $securite_sociale;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $matricule;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $carte_de_presse;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_carte_presse;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $code_emploi;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nationalite;

    /**
     * @ORM\OneToMany(targetEntity=Contrats::class, mappedBy="pigiste")
     */
    private $contrats;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;


    public function __construct()
    {
        $this->contrats = new ArrayCollection();
        $this->active = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->date_de_naissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $date_de_naissance): self
    {
        $this->date_de_naissance = $date_de_naissance;

        return $this;
    }

    public function getLieuDeNaissace(): ?string
    {
        return $this->lieu_de_naissace;
    }

    public function setLieuDeNaissace(string $lieu_de_naissace): self
    {
        $this->lieu_de_naissace = $lieu_de_naissace;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getSecuriteSociale(): ?string
    {
        return $this->securite_sociale;
    }

    public function setSecuriteSociale(string $securite_sociale): self
    {
        $this->securite_sociale = $securite_sociale;

        return $this;
    }

    public function getMatricule(): ?int
    {
        return $this->matricule;
    }

    public function setMatricule(int $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getCarteDePresse(): ?string
    {
        return $this->carte_de_presse;
    }

    public function setCarteDePresse(?string $carte_de_presse): self
    {
        $this->carte_de_presse = $carte_de_presse;

        return $this;
    }

    public function getDateCartePresse(): ?\DateTimeInterface
    {
        return $this->date_carte_presse;
    }

    public function setDateCartePresse(?\DateTimeInterface $date_carte_presse): self
    {
        $this->date_carte_presse = $date_carte_presse;

        return $this;
    }

    public function getCodeEmploi(): ?string
    {
        return $this->code_emploi;
    }

    public function setCodeEmploi(string $code_emploi): self
    {
        $this->code_emploi = $code_emploi;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(string $nationalite): self
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    /**
     * @return Collection|Contrats[]
     */
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrats $contrat): self
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats[] = $contrat;
            $contrat->setPigiste($this);
        }

        return $this;
    }

    public function removeContrat(Contrats $contrat): self
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getPigiste() === $this) {
                $contrat->setPigiste(null);
            }
        }

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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }
}
