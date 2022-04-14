<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Envio
 *
 * @ORM\Table(name="envio")
 * @ORM\Entity
 */
class Envio
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255, nullable=false)
     */
    private $codigo;

    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="integer", nullable=false)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_orden", type="string", length=255, nullable=false)
     */
    private $numeroOrden;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", length=0, nullable=false)
     */
    private $descripcion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="total_peso_estimado", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $totalPesoEstimado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="total_peso_real", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $totalPesoReal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="total_dimencional", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $totalDimencional;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_estimada_entrega", type="date", nullable=false)
     */
    private $fechaEstimadaEntrega;

    /**
     * @var string
     *
     * @ORM\Column(name="piezas", type="text", length=0, nullable=false)
     */
    private $piezas;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cantidad_piezas", type="integer", nullable=true)
     */
    private $cantidadPiezas;

    /**
     * @var string|null
     *
     * @ORM\Column(name="json_recibido", type="text", length=0, nullable=true)
     */
    private $jsonRecibido;

    /**
     * @var bool
     *
     * @ORM\Column(name="facturado", type="boolean", nullable=false)
     */
    private $facturado;

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

    public function getEstado(): ?int
    {
        return $this->estado;
    }

    public function setEstado(int $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getNumeroOrden(): ?string
    {
        return $this->numeroOrden;
    }

    public function setNumeroOrden(string $numeroOrden): self
    {
        $this->numeroOrden = $numeroOrden;

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

    public function getTotalPesoEstimado(): ?string
    {
        return $this->totalPesoEstimado;
    }

    public function setTotalPesoEstimado(?string $totalPesoEstimado): self
    {
        $this->totalPesoEstimado = $totalPesoEstimado;

        return $this;
    }

    public function getTotalPesoReal(): ?string
    {
        return $this->totalPesoReal;
    }

    public function setTotalPesoReal(?string $totalPesoReal): self
    {
        $this->totalPesoReal = $totalPesoReal;

        return $this;
    }

    public function getTotalDimencional(): ?string
    {
        return $this->totalDimencional;
    }

    public function setTotalDimencional(?string $totalDimencional): self
    {
        $this->totalDimencional = $totalDimencional;

        return $this;
    }

    public function getFechaEstimadaEntrega(): ?\DateTimeInterface
    {
        return $this->fechaEstimadaEntrega;
    }

    public function setFechaEstimadaEntrega(\DateTimeInterface $fechaEstimadaEntrega): self
    {
        $this->fechaEstimadaEntrega = $fechaEstimadaEntrega;

        return $this;
    }

    public function getPiezas(): ?string
    {
        return $this->piezas;
    }

    public function setPiezas(string $piezas): self
    {
        $this->piezas = $piezas;

        return $this;
    }

    public function getCantidadPiezas(): ?int
    {
        return $this->cantidadPiezas;
    }

    public function setCantidadPiezas(?int $cantidadPiezas): self
    {
        $this->cantidadPiezas = $cantidadPiezas;

        return $this;
    }

    public function getJsonRecibido(): ?string
    {
        return $this->jsonRecibido;
    }

    public function setJsonRecibido(?string $jsonRecibido): self
    {
        $this->jsonRecibido = $jsonRecibido;

        return $this;
    }

    public function getFacturado(): ?bool
    {
        return $this->facturado;
    }

    public function setFacturado(bool $facturado): self
    {
        $this->facturado = $facturado;

        return $this;
    }


}
