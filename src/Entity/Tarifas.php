<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tarifas
 *
 * @ORM\Table(name="tarifas", indexes={@ORM\Index(name="zona_id", columns={"zona_id"}), @ORM\Index(name="tarifas_configuracion_id", columns={"tarifas_configuracion_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\TarifasRepository")
 */
class Tarifas
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
     * @ORM\Column(name="peso_minimo", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $pesoMinimo;

    /**
     * @var string
     *
     * @ORM\Column(name="peso_maximo", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $pesoMaximo;

    /**
     * @var string
     *
     * @ORM\Column(name="costo_flete", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $costoFlete;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="porcentaje", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $porcentaje = '0.00';

    /**
     * @var \TarifasConfiguracion
     *
     * @ORM\ManyToOne(targetEntity="TarifasConfiguracion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tarifas_configuracion_id", referencedColumnName="id")
     * })
     */
    private $tarifasConfiguracion;

    /**
     * @var \Zonas
     *
     * @ORM\ManyToOne(targetEntity="Zonas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="zona_id", referencedColumnName="id")
     * })
     */
    private $zona;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPesoMinimo(): ?string
    {
        return $this->pesoMinimo;
    }

    public function setPesoMinimo(string $pesoMinimo): self
    {
        $this->pesoMinimo = $pesoMinimo;

        return $this;
    }

    public function getPesoMaximo(): ?string
    {
        return $this->pesoMaximo;
    }

    public function setPesoMaximo(string $pesoMaximo): self
    {
        $this->pesoMaximo = $pesoMaximo;

        return $this;
    }

    public function getCostoFlete(): ?string
    {
        return $this->costoFlete;
    }

    public function setCostoFlete(string $costoFlete): self
    {
        $this->costoFlete = $costoFlete;

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

    public function getPorcentaje(): ?string
    {
        return $this->porcentaje;
    }

    public function setPorcentaje(string $porcentaje): self
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    public function getTarifasConfiguracion(): ?TarifasConfiguracion
    {
        return $this->tarifasConfiguracion;
    }

    public function setTarifasConfiguracion(?TarifasConfiguracion $tarifasConfiguracion): self
    {
        $this->tarifasConfiguracion = $tarifasConfiguracion;

        return $this;
    }

    public function getZona(): ?Zonas
    {
        return $this->zona;
    }

    public function setZona(?Zonas $zona): self
    {
        $this->zona = $zona;

        return $this;
    }


}
