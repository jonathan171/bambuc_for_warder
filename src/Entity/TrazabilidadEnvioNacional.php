<?php

namespace App\Entity;

use App\Repository\TrazabilidadEnvioNacionalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrazabilidadEnvioNacionalRepository::class)
 */
class TrazabilidadEnvioNacional
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
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity=EnviosNacionales::class)
     */
    private $envio_nacional;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $quien_recibe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getEnvioNacional(): ?EnviosNacionales
    {
        return $this->envio_nacional;
    }

    public function setEnvioNacional(?EnviosNacionales $envio_nacional): self
    {
        $this->envio_nacional = $envio_nacional;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getQuienRecibe(): ?string
    {
        return $this->quien_recibe;
    }

    public function setQuienRecibe(?string $quien_recibe): self
    {
        $this->quien_recibe = $quien_recibe;

        return $this;
    }
}
