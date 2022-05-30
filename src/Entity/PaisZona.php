<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaisZona
 *
 * @ORM\Table(name="pais_zona", indexes={@ORM\Index(name="pais_id", columns={"pais_id"}), @ORM\Index(name="zona_id", columns={"zona_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\PaisZonaRepository")
 */
class PaisZona
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Pais
     *
     * @ORM\ManyToOne(targetEntity="Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pais_id", referencedColumnName="id")
     * })
     */
    private $pais;

    /**
     * @var \Zonas
     *
     * @ORM\ManyToOne(targetEntity="Zonas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="zona_id", referencedColumnName="id")
     * })
     */
    private $zona;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPais(): ?Pais
    {
        return $this->pais;
    }

    public function setPais(?Pais $pais): self
    {
        $this->pais = $pais;

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
