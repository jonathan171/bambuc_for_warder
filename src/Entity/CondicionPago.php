<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CondicionPago
 *
 * @ORM\Table(name="condicion_pago")
 * @ORM\Entity
 */
class CondicionPago
{
    public function __toString() {
        return $this->descripcion;
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
     * @ORM\Column(name="codigo", type="string", length=45, nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcio_dataico", type="string", length=255, nullable=true)
     */
    private $descripcioDataico;

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

    public function getDescripcioDataico(): ?string
    {
        return $this->descripcioDataico;
    }

    public function setDescripcioDataico(?string $descripcioDataico): self
    {
        $this->descripcioDataico = $descripcioDataico;

        return $this;
    }


}
