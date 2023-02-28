<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaResolucion
 *
 * @ORM\Table(name="factura_resolucion")
 * @ORM\Entity(repositoryClass="App\Repository\FacturaResolucionRepository")
 */
class FacturaResolucion
{
    public function __toString()
    {
        return $this->numeroResolucion.'-'.$this->prefijo;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="numero_resolucion", type="bigint", nullable=false)
     */
    private $numeroResolucion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_habilitacion", type="date", nullable=false)
     */
    private $fechaHabilitacion;

    /**
     * @var int
     *
     * @ORM\Column(name="inicio_consecutivo", type="bigint", nullable=false)
     */
    private $inicioConsecutivo;

    /**
     * @var int
     *
     * @ORM\Column(name="fin_consecutivo", type="bigint", nullable=false)
     */
    private $finConsecutivo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prefijo", type="string", length=125, nullable=true)
     */
    private $prefijo;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false)
     */
    private $activo;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_vencimiento", type="date", nullable=true)
     */
    private $fechaVencimiento;

    /**
     * @ORM\ManyToOne(targetEntity=empresa::class, inversedBy="facturaResolucions")
     */
    private $empresa;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroResolucion(): ?string
    {
        return $this->numeroResolucion;
    }

    public function setNumeroResolucion(string $numeroResolucion): self
    {
        $this->numeroResolucion = $numeroResolucion;

        return $this;
    }

    public function getFechaHabilitacion(): ?\DateTimeInterface
    {
        return $this->fechaHabilitacion;
    }

    public function setFechaHabilitacion(\DateTimeInterface $fechaHabilitacion): self
    {
        $this->fechaHabilitacion = $fechaHabilitacion;

        return $this;
    }

    public function getInicioConsecutivo(): ?string
    {
        return $this->inicioConsecutivo;
    }

    public function setInicioConsecutivo(string $inicioConsecutivo): self
    {
        $this->inicioConsecutivo = $inicioConsecutivo;

        return $this;
    }

    public function getFinConsecutivo(): ?string
    {
        return $this->finConsecutivo;
    }

    public function setFinConsecutivo(string $finConsecutivo): self
    {
        $this->finConsecutivo = $finConsecutivo;

        return $this;
    }

    public function getPrefijo(): ?string
    {
        return $this->prefijo;
    }

    public function setPrefijo(?string $prefijo): self
    {
        $this->prefijo = $prefijo;

        return $this;
    }

    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): self
    {
        $this->activo = $activo;

        return $this;
    }

    public function getFechaVencimiento(): ?\DateTimeInterface
    {
        return $this->fechaVencimiento;
    }

    public function setFechaVencimiento(?\DateTimeInterface $fechaVencimiento): self
    {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    public function getEmpresa(): ?empresa
    {
        return $this->empresa;
    }

    public function setEmpresa(?empresa $empresa): self
    {
        $this->empresa = $empresa;

        return $this;
    }
}
