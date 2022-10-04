<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Clientes
 *
 * @ORM\Table(name="clientes", indexes={@ORM\Index(name="nit", columns={"nit"}), @ORM\Index(name="municipio_id", columns={"municipio_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ClientesRepository")
 * @UniqueEntity(
 *     fields={"nit"},
 *     message="Ya existe un cliente con este numero de documento."
 * )
 */
class Clientes
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
     * @ORM\Column(name="razon_social", type="string", length=255, nullable=false)
     */
    private $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="nit", type="string", length=255, nullable=false,  unique=true)
     */
    private $nit;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=false)
     */
    private $direccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="correo", type="string", length=255, nullable=false)
     */
    private $correo;

    /**
     * @var bool
     *
     * @ORM\Column(name="tipo_receptor", type="string", length=255, nullable=false)
     */
    private $tipoReceptor;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_documento", type="string", length=20, nullable=false)
     */
    private $tipoDocumento;

    /**
     * @var string
     *
     * @ORM\Column(name="regimen", type="string", length=255, nullable=false, options={"default"="SIMPLE"})
     */
    private $regimen = 'SIMPLE';

    /**
     * @var string
     *
     * @ORM\Column(name="tax_level_code", type="string", length=255, nullable=false, options={"default"="RESPONSABLE_DE_IVA"})
     */
    private $taxLevelCode = 'RESPONSABLE_DE_IVA';

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombres", type="string", length=255, nullable=true)
     */
    private $nombres;

    /**
     * @var string|null
     *
     * @ORM\Column(name="apellidos", type="string", length=255, nullable=true)
     */
    private $apellidos;

    /**
     * @var \Municipio
     *
     * @ORM\ManyToOne(targetEntity="Municipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="municipio_id", referencedColumnName="id")
     * })
     */
    private $municipio;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRazonSocial(): ?string
    {
        return $this->razonSocial;
    }

    public function setRazonSocial(string $razonSocial): self
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    public function getNit(): ?string
    {
        return $this->nit;
    }

    public function setNit(string $nit): self
    {
        $this->nit = $nit;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): self
    {
        $this->correo = $correo;

        return $this;
    }

    public function getTipoReceptor(): ?string
    {
        return $this->tipoReceptor;
    }

    public function setTipoReceptor(string $tipoReceptor): self
    {
        $this->tipoReceptor = $tipoReceptor;

        return $this;
    }

    public function getTipoDocumento(): ?string
    {
        return $this->tipoDocumento;
    }

    public function setTipoDocumento(string $tipoDocumento): self
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    public function getRegimen(): ?string
    {
        return $this->regimen;
    }

    public function setRegimen(string $regimen): self
    {
        $this->regimen = $regimen;

        return $this;
    }

    public function getTaxLevelCode(): ?string
    {
        return $this->taxLevelCode;
    }

    public function setTaxLevelCode(string $taxLevelCode): self
    {
        $this->taxLevelCode = $taxLevelCode;

        return $this;
    }

    public function getNombres(): ?string
    {
        return $this->nombres;
    }

    public function setNombres(?string $nombres): self
    {
        $this->nombres = $nombres;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getMunicipio(): ?Municipio
    {
        return $this->municipio;
    }

    public function setMunicipio(?Municipio $municipio): self
    {
        $this->municipio = $municipio;

        return $this;
    }


}
