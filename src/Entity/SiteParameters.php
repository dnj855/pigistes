<?php

namespace App\Entity;

use App\Repository\SiteParametersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SiteParametersRepository::class)
 */
class SiteParameters
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $tarifsUpdate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarifsUpdate(): ?\DateTimeInterface
    {
        return $this->tarifsUpdate;
    }

    public function setTarifsUpdate(?\DateTimeInterface $tarifsUpdate): self
    {
        $this->tarifsUpdate = $tarifsUpdate;

        return $this;
    }
}
