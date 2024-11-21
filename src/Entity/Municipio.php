<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Municipio
 *
 * @ORM\Table(name="municipio", indexes={@ORM\Index(name="departamento_id_idx", columns={"departamento_id"})})
 * @ORM\Entity
 */
class Municipio
{  

    public function __toString() {
        return $this->nombre.' ('.$this->departamento->getNombre().')';
    }
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=20, nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var \Departamento
     *
     * @ORM\ManyToOne(targetEntity="Departamento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="departamento_id", referencedColumnName="id")
     * })
     */
    private $departamento;

    /**
     * @ORM\OneToMany(targetEntity=ReciboCaja::class, mappedBy="municipio")
     */
    private $recibos_caja;

    /**
     * @ORM\OneToMany(targetEntity=Envio::class, mappedBy="municipio")
     */
    private $envios;

    public function __construct()
    {
        $this->recibos_caja = new ArrayCollection();
        $this->envios = new ArrayCollection();
    }

    public function getId(): ?string
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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDepartamento(): ?Departamento
    {
        return $this->departamento;
    }

    public function setDepartamento(?Departamento $departamento): self
    {
        $this->departamento = $departamento;

        return $this;
    }

    /**
     * @return Collection<int, ReciboCaja>
     */
    public function getRecibosCaja(): Collection
    {
        return $this->recibos_caja;
    }

    public function addCreadaPor(ReciboCaja $reciboCaja): self
    {
        if (!$this->recibos_caja->contains($reciboCaja)) {
            $this->recibos_caja[] = $reciboCaja;
            $reciboCaja->setMunicipio($this);
        }

        return $this;
    }

    public function removeReciboCaja(ReciboCaja $reciboCaja): self
    {
        if ($this->recibos_caja->removeElement($reciboCaja)) {
            // set the owning side to null (unless already changed)
            if ($reciboCaja->getMunicipio() === $this) {
                $reciboCaja->setMunicipio(null);
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
            $envio->setMunicipio($this);
        }

        return $this;
    }

    public function removeEnvio(Envio $envio): self
    {
        if ($this->envios->removeElement($envio)) {
            // set the owning side to null (unless already changed)
            if ($envio->getMunicipio() === $this) {
                $envio->setMunicipio(null);
            }
        }

        return $this;
    }


}
