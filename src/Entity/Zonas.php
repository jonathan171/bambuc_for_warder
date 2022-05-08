<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Zonas
 *
 * @ORM\Table(name="zonas")
 * @ORM\Entity
 */
class Zonas
{   
    public function __toString() {
        return $this->nombre;
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
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

     /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=100, nullable=false)
     */
    private $tipo;

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
