<?php

namespace App\Entity;

use App\Repository\ReciboCajaItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReciboCajaItemRepository::class)
 */
class ReciboCajaItem
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
    private $codigo;

    /**
     * @ORM\Column(type="text")
     */
    private $descripcion;

    /**
     * @ORM\Column(type="integer")
     */
    private $cantidad;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $valor_unitario;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $sub_total;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $total;

    /**
     * @ORM\OneToMany(targetEntity=EnviosNacionales::class, mappedBy="reciboItems")
     */
    private $enviosNacionales;

    /**
     * @ORM\OneToMany(targetEntity=Envio::class, mappedBy="reciboCajaItem")
     */
    private $envios;

    /**
     * @ORM\ManyToOne(targetEntity=ReciboCaja::class, inversedBy="reciboCajaItems")
     */
    private $reciboCaja;

    public function __construct()
    {
        $this->enviosNacionales = new ArrayCollection();
        $this->envios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getValorUnitario(): ?string
    {
        return $this->valor_unitario;
    }

    public function setValorUnitario(string $valor_unitario): self
    {
        $this->valor_unitario = $valor_unitario;

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

    /**
     * @return Collection<int, EnviosNacionales>
     */
    public function getEnviosNacionales(): Collection
    {
        return $this->enviosNacionales;
    }

    public function addEnviosNacionale(EnviosNacionales $enviosNacionale): self
    {
        if (!$this->enviosNacionales->contains($enviosNacionale)) {
            $this->enviosNacionales[] = $enviosNacionale;
            $enviosNacionale->setReciboItems($this);
        }

        return $this;
    }

    public function removeEnviosNacionale(EnviosNacionales $enviosNacionale): self
    {
        if ($this->enviosNacionales->removeElement($enviosNacionale)) {
            // set the owning side to null (unless already changed)
            if ($enviosNacionale->getReciboItems() === $this) {
                $enviosNacionale->setReciboItems(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Envio>
     */
    public function getEnvios(): Collection
    {
        return $this->envios;
    }

    public function addEnvio(Envio $envio): self
    {
        if (!$this->envios->contains($envio)) {
            $this->envios[] = $envio;
            $envio->setReciboCajaItem($this);
        }

        return $this;
    }

    public function removeEnvio(Envio $envio): self
    {
        if ($this->envios->removeElement($envio)) {
            // set the owning side to null (unless already changed)
            if ($envio->getReciboCajaItem() === $this) {
                $envio->setReciboCajaItem(null);
            }
        }

        return $this;
    }

    public function getReciboCaja(): ?ReciboCaja
    {
        return $this->reciboCaja;
    }

    public function setReciboCaja(?ReciboCaja $reciboCaja): self
    {
        $this->reciboCaja = $reciboCaja;

        return $this;
    }
}
