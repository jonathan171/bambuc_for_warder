<?php

namespace App\Entity;

use App\Repository\EmpresaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmpresaRepository::class)
 */
class Empresa
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
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo_doc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $documento;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $usuario;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clave;

    /**
     * @ORM\OneToMany(targetEntity=FacturaResolucion::class, mappedBy="empresa")
     */
    private $facturaResolucions;

    public function __construct()
    {
        $this->facturaResolucions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTipoDoc(): ?string
    {
        return $this->tipo_doc;
    }

    public function setTipoDoc(string $tipo_doc): self
    {
        $this->tipo_doc = $tipo_doc;

        return $this;
    }

    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    public function setDocumento(?string $documento): self
    {
        $this->documento = $documento;

        return $this;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getClave(): ?string
    {
        return $this->clave;
    }

    public function setClave(string $clave): self
    {
        $this->clave = $clave;

        return $this;
    }

    /**
     * @return Collection<int, FacturaResolucion>
     */
    public function getFacturaResolucions(): Collection
    {
        return $this->facturaResolucions;
    }

    public function addFacturaResolucion(FacturaResolucion $facturaResolucion): self
    {
        if (!$this->facturaResolucions->contains($facturaResolucion)) {
            $this->facturaResolucions[] = $facturaResolucion;
            $facturaResolucion->setEmpresa($this);
        }

        return $this;
    }

    public function removeFacturaResolucion(FacturaResolucion $facturaResolucion): self
    {
        if ($this->facturaResolucions->removeElement($facturaResolucion)) {
            // set the owning side to null (unless already changed)
            if ($facturaResolucion->getEmpresa() === $this) {
                $facturaResolucion->setEmpresa(null);
            }
        }

        return $this;
    }
}
