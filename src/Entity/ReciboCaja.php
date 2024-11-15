<?php

namespace App\Entity;

use App\Repository\ReciboCajaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReciboCajaRepository::class)
 */
class ReciboCaja
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
    private $numero_recibo;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity=Municipio::class, inversedBy="creada_por")
     */
    private $municipio;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reciboCajas")
     */
    private $creada_por;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reciboCajas")
     */
    private $pagada_a;

    /**
     * @ORM\OneToMany(targetEntity=ReciboCajaItem::class, mappedBy="reciboCaja")
     */
    private $reciboCajaItems;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $sub_total;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $total;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firma;

    public function __construct()
    {
        $this->reciboCajaItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroRecibo(): ?int
    {
        return $this->numero_recibo;
    }

    public function setNumeroRecibo(int $numero_recibo): self
    {
        $this->numero_recibo = $numero_recibo;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getMunicipio(): ?Municipio
    {
        return $this->municipio;
    }

    public function setMunicipio(?Municipio $municipio): self
    {
        $this->municipio = $municipio;

        return $this;
    }

    public function getCreadaPor(): ?User
    {
        return $this->creada_por;
    }

    public function setCreadaPor(?User $creada_por): self
    {
        $this->creada_por = $creada_por;

        return $this;
    }

    public function getPagadaA(): ?User
    {
        return $this->pagada_a;
    }

    public function setPagadaA(?User $pagada_a): self
    {
        $this->pagada_a = $pagada_a;

        return $this;
    }

    /**
     * @return Collection<int, ReciboCajaItem>
     */
    public function getReciboCajaItems(): Collection
    {
        return $this->reciboCajaItems;
    }

    public function addReciboCajaItem(ReciboCajaItem $reciboCajaItem): self
    {
        if (!$this->reciboCajaItems->contains($reciboCajaItem)) {
            $this->reciboCajaItems[] = $reciboCajaItem;
            $reciboCajaItem->setReciboCaja($this);
        }

        return $this;
    }

    public function removeReciboCajaItem(ReciboCajaItem $reciboCajaItem): self
    {
        if ($this->reciboCajaItems->removeElement($reciboCajaItem)) {
            // set the owning side to null (unless already changed)
            if ($reciboCajaItem->getReciboCaja() === $this) {
                $reciboCajaItem->setReciboCaja(null);
            }
        }

        return $this;
    }

    public function getSubTotal(): ?string
    {
        return $this->sub_total;
    }

    public function setSubTotal(string $sub_total): self
    {
        $this->sub_total = $sub_total;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getFirma(): ?string
    {
        return $this->firma;
    }

    public function setFirma(?string $firma): self
    {
        $this->firma = $firma;
        return $this;
    }
}
