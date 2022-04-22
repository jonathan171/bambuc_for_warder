<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clientes
 *
 * @ORM\Table(name="clientes", indexes={@ORM\Index(name="id_tributo", columns={"id_tributo"}), @ORM\Index(name="id_obligacion", columns={"id_obligacion"}), @ORM\Index(name="nit", columns={"nit"}), @ORM\Index(name="municipio_id", columns={"municipio_id"})})
 * @ORM\Entity
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
     * @ORM\Column(name="nit", type="string", length=255, nullable=false)
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
     * @ORM\Column(name="tipo_receptor", type="boolean", nullable=false)
     */
    private $tipoReceptor;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_documento", type="string", length=20, nullable=false)
     */
    private $tipoDocumento;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo_regimen", type="integer", nullable=false, options={"default"="49"})
     */
    private $tipoRegimen = 49;

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

    /**
     * @var \Tributos
     *
     * @ORM\ManyToOne(targetEntity="Tributos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tributo", referencedColumnName="id")
     * })
     */
    private $idTributo;

    /**
     * @var \ObligacionesFiscales
     *
     * @ORM\ManyToOne(targetEntity="ObligacionesFiscales")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_obligacion", referencedColumnName="id")
     * })
     */
    private $idObligacion;

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

    public function getTipoReceptor(): ?bool
    {
        return $this->tipoReceptor;
    }

    public function setTipoReceptor(bool $tipoReceptor): self
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

    public function getTipoRegimen(): ?int
    {
        return $this->tipoRegimen;
    }

    public function setTipoRegimen(int $tipoRegimen): self
    {
        $this->tipoRegimen = $tipoRegimen;

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

    public function getIdTributo(): ?Tributos
    {
        return $this->idTributo;
    }

    public function setIdTributo(?Tributos $idTributo): self
    {
        $this->idTributo = $idTributo;

        return $this;
    }

    public function getIdObligacion(): ?ObligacionesFiscales
    {
        return $this->idObligacion;
    }

    public function setIdObligacion(?ObligacionesFiscales $idObligacion): self
    {
        $this->idObligacion = $idObligacion;

        return $this;
    }


}
