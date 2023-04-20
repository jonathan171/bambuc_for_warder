<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EnviosNacionales
 *
 * @ORM\Table(name="envios_nacionales", indexes={@ORM\Index(name="municipio_origen", columns={"municipio_origen"}), @ORM\Index(name="municipio_destino", columns={"municipio_destino"}), @ORM\Index(name="cliente_id", columns={"cliente_id"})})
 * @ORM\Entity
 */
class EnviosNacionales
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="bigint", nullable=false)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion_origen", type="string", length=255, nullable=false)
     */
    private $direccionOrigen;

    /**
     * @var int
     *
     * @ORM\Column(name="unidades", type="integer", nullable=false)
     */
    private $unidades;

    /**
     * @var string
     *
     * @ORM\Column(name="destinatario", type="text", length=65535, nullable=false)
     */
    private $destinatario;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion_destino", type="string", length=255, nullable=false)
     */
    private $direccionDestino;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono_destinatario", type="string", length=255, nullable=false)
     */
    private $telefonoDestinatario;

    /**
     * @var string
     *
     * @ORM\Column(name="peso", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $peso;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcion", type="text", length=65535, nullable=true)
     */
    private $descripcion;
    /**
     * @var string|null
     *
     * @ORM\Column(name="observacion", type="text", length=65535, nullable=true)
     */
    private $observacion;

    /**
     * @var string
     *
     * @ORM\Column(name="seguro", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $seguro;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_total", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $valorTotal ;

    /**
     * @var string
     *
     * @ORM\Column(name="forma_pago", type="string", length=255, nullable=false)
     */
    private $formaPago;

    /**
     * @var bool
     *
     * @ORM\Column(name="contra_entrega", type="boolean", nullable=false)
     */
    private $contraEntrega = '0';

    /**
     * @var \Clientes
     *
     * @ORM\ManyToOne(targetEntity="Clientes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * })
     */
    private $cliente;

    /**
     * @var \Municipio
     *
     * @ORM\ManyToOne(targetEntity="Municipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="municipio_origen", referencedColumnName="id")
     * })
     */
    private $municipioOrigen;

    /**
     * @var \Municipio
     *
     * @ORM\ManyToOne(targetEntity="Municipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="municipio_destino", referencedColumnName="id")
     * })
     */
    private $municipioDestino;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $estado;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $facturado;

    /**
     * @ORM\ManyToOne(targetEntity=FacturaItems::class, inversedBy="enviosNacionales")
     */
    private $facturaItems;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDireccionOrigen(): ?string
    {
        return $this->direccionOrigen;
    }

    public function setDireccionOrigen(string $direccionOrigen): self
    {
        $this->direccionOrigen = $direccionOrigen;

        return $this;
    }

    public function getUnidades(): ?int
    {
        return $this->unidades;
    }

    public function setUnidades(int $unidades): self
    {
        $this->unidades = $unidades;

        return $this;
    }

    public function getDestinatario(): ?string
    {
        return $this->destinatario;
    }

    public function setDestinatario(string $destinatario): self
    {
        $this->destinatario = $destinatario;

        return $this;
    }

    public function getDireccionDestino(): ?string
    {
        return $this->direccionDestino;
    }

    public function setDireccionDestino(string $direccionDestino): self
    {
        $this->direccionDestino = $direccionDestino;

        return $this;
    }

    public function getTelefonoDestinatario(): ?string
    {
        return $this->telefonoDestinatario;
    }

    public function setTelefonoDestinatario(string $telefonoDestinatario): self
    {
        $this->telefonoDestinatario = $telefonoDestinatario;

        return $this;
    }

    public function getPeso(): ?string
    {
        return $this->peso;
    }

    public function setPeso(string $peso): self
    {
        $this->peso = $peso;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getObservacion(): ?string
    {
        return $this->observacion;
    }

    public function setObservacion(?string $observacion): self
    {
        $this->observacion = $observacion;

        return $this;
    }

    public function getSeguro(): ?string
    {
        return $this->seguro;
    }

    public function setSeguro(string $seguro): self
    {
        $this->seguro = $seguro;

        return $this;
    }

    public function getValorTotal(): ?string
    {
        return $this->valorTotal;
    }

    public function setValorTotal(string $valorTotal): self
    {
        $this->valorTotal = $valorTotal;

        return $this;
    }

    public function getFormaPago(): ?string
    {
        return $this->formaPago;
    }

    public function setFormaPago(string $formaPago): self
    {
        $this->formaPago = $formaPago;

        return $this;
    }

    public function isContraEntrega(): ?bool
    {
        return $this->contraEntrega;
    }

    public function setContraEntrega(bool $contraEntrega): self
    {
        $this->contraEntrega = $contraEntrega;

        return $this;
    }

    public function getCliente(): ?Clientes
    {
        return $this->cliente;
    }

    public function setCliente(?Clientes $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }

    public function getMunicipioOrigen(): ?Municipio
    {
        return $this->municipioOrigen;
    }

    public function setMunicipioOrigen(?Municipio $municipioOrigen): self
    {
        $this->municipioOrigen = $municipioOrigen;

        return $this;
    }

    public function getMunicipioDestino(): ?Municipio
    {
        return $this->municipioDestino;
    }

    public function setMunicipioDestino(?Municipio $municipioDestino): self
    {
        $this->municipioDestino = $municipioDestino;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function isFacturado(): ?bool
    {
        return $this->facturado;
    }

    public function setFacturado(?bool $facturado): self
    {
        $this->facturado = $facturado;

        return $this;
    }

    public function getFacturaItems(): ?FacturaItems
    {
        return $this->facturaItems;
    }

    public function setFacturaItems(?FacturaItems $facturaItems): self
    {
        $this->facturaItems = $facturaItems;

        return $this;
    }


}
