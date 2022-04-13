<?php

namespace App\Entity;

use App\Repository\EnvioRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EnvioRepository::class)
 */
class Envio
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
     * @ORM\Column(type="integer")
     */
    private $estado;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero_orden;

    /**
     * @ORM\Column(type="text")
     */
    private $descripcion;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $total_peso_estimado;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $total_peso_real;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $total_dimencional;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha_estimada_entrega;

    /**
     * @ORM\Column(type="text")
     */
    private $piezas;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cantidad_piezas;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $json_recibido;

    /**
     * @ORM\Column(type="boolean")
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
        return $this->numero_orden;
    }

    public function setNumeroOrden(string $numero_orden): self
    {
        $this->numero_orden = $numero_orden;

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
        return $this->total_peso_estimado;
    }

    public function setTotalPesoEstimado(?string $total_peso_estimado): self
    {
        $this->total_peso_estimado = $total_peso_estimado;

        return $this;
    }

    public function getTotalPesoReal(): ?string
    {
        return $this->total_peso_real;
    }

    public function setTotalPesoReal(?string $total_peso_real): self
    {
        $this->total_peso_real = $total_peso_real;

        return $this;
    }

    public function getTotalDimencional(): ?string
    {
        return $this->total_dimencional;
    }

    public function setTotalDimencional(?string $total_dimencional): self
    {
        $this->total_dimencional = $total_dimencional;

        return $this;
    }

    public function getFechaEstimadaEntrega(): ?\DateTimeInterface
    {
        return $this->fecha_estimada_entrega;
    }

    public function setFechaEstimadaEntrega(\DateTimeInterface $fecha_estimada_entrega): self
    {
        $this->fecha_estimada_entrega = $fecha_estimada_entrega;

        return $this;
    }

    public function getPiezas(): ?string
    {
        return $this->piezas;
    }

    public function setPiesas(string $piezas): self
    {
        $this->piezas = $piezas;

        return $this;
    }

    public function getCantidadPiezas(): ?int
    {
        return $this->cantidad_piezas;
    }

    public function setCantidadPiezas(?int $cantidad_piezas): self
    {
        $this->cantidad_piezas = $cantidad_piezas;

        return $this;
    }

    public function getJsonRecibido(): ?string
    {
        return $this->json_recibido;
    }

    public function setJsonRecibido(?string $json_recibido): self
    {
        $this->json_recibido = $json_recibido;

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
