<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TarifasConfiguracion
 *
 * @ORM\Table(name="tarifas_configuracion")
 * @ORM\Entity
 */
class TarifasConfiguracion
{   
    // para póder usar en las relaciones
    public function __toString() {
        return $this->id;
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
     * @var string
     *
     * @ORM\Column(name="valor_dolar", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $valorDolar;

    /**
     * @var string
     *
     * @ORM\Column(name="tasa_conbustible", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $tasaConbustible;

    /**
     * @var string
     *
     * @ORM\Column(name="porcentaje_ganacia", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $porcentajeGanacia;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValorDolar(): ?string
    {
        return $this->valorDolar;
    }

    public function setValorDolar(string $valorDolar): self
    {
        $this->valorDolar = $valorDolar;

        return $this;
    }

    public function getTasaConbustible(): ?string
    {
        return $this->tasaConbustible;
    }

    public function setTasaConbustible(string $tasaConbustible): self
    {
        $this->tasaConbustible = $tasaConbustible;

        return $this;
    }

    public function getPorcentajeGanacia(): ?string
    {
        return $this->porcentajeGanacia;
    }

    public function setPorcentajeGanacia(string $porcentajeGanacia): self
    {
        $this->porcentajeGanacia = $porcentajeGanacia;

        return $this;
    }


}
