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
        $tipo_configuracion = array(
            'exportacion' => 'Exportación',
            'importacion'  => 'Importación', 
            'especial_importacion' => 'Especial Importación',
            'especial_exportacion' => 'Especial Exportacion',
        );
        return $this->empresa.'('.$tipo_configuracion[$this->tipo].')';
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

    /**
     * @var string
     *
     * @ORM\Column(name="empresa", type="string", length=255, nullable=false)
     */
    private $empresa;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=100, nullable=false, options={"default"="exportacion"})
     */
    private $tipo = 'exportacion';

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

    public function getEmpresa(): ?string
    {
        return $this->empresa;
    }

    public function setEmpresa(string $empresa): self
    {
        $this->empresa = $empresa;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }


}
