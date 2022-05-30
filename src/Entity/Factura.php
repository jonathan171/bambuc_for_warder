<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Factura
 *
 * @ORM\Table(name="factura", indexes={@ORM\Index(name="cond_de_pago", columns={"cond_de_pago"}), @ORM\Index(name="cliente_id", columns={"cliente_id"}), @ORM\Index(name="factura_resolucion_id", columns={"factura_resolucion_id"})})
 * @ORM\Entity
 */
class Factura
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
     * @var int
     *
     * @ORM\Column(name="numero_factura", type="bigint", nullable=false)
     */
    private $numeroFactura;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=20, nullable=false)
     */
    private $estado = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_vencimiento", type="date", nullable=false)
     */
    private $fechaVencimiento;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_validacion", type="date", nullable=true)
     */
    private $fechaValidacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="total", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $total;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="anulado", type="boolean", nullable=true)
     */
    private $anulado;

    /**
     * @var int|null
     *
     * @ORM\Column(name="anulado_usuario", type="bigint", nullable=true)
     */
    private $anuladoUsuario;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="anulado_fecha", type="date", nullable=true)
     */
    private $anuladoFecha;

    /**
     * @var string
     *
     * @ORM\Column(name="forma_de_pago", type="string", length=255, nullable=false)
     */
    private $formaDePago;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_factura", type="string", length=255, nullable=false)
     */
    private $tipoFactura;

    /**
     * @var string|null
     *
     * @ORM\Column(name="observaciones", type="text", length=65535, nullable=true)
     */
    private $observaciones;

    /**
     * @var int
     *
     * @ORM\Column(name="factura_resolucion_id", type="bigint", nullable=false)
     */
    private $facturaResolucionId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hora", type="time", nullable=false)
     */
    private $hora;

    /**
     * @var string
     *
     * @ORM\Column(name="subtotal", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $subtotal = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="total_iva", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $totalIva = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="total_rete_iva", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $totalReteIva = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="total_rete_ica", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $totalReteIca = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="total_rete_fuente", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $totalReteFuente = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="total_rete_fuente_g", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $totalReteFuenteG = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="rete_fuente", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $reteFuente = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="rete_ica", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $reteIca = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="rete_iva", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $reteIva = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="descuento", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $descuento = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="tax_level_code", type="string", length=255, nullable=false, options={"default"="NO_RESPONSABLE_DE_IVA"})
     */
    private $taxLevelCode = 'NO_RESPONSABLE_DE_IVA';

    /**
     * @var string
     *
     * @ORM\Column(name="regimen", type="string", length=255, nullable=false, options={"default"="SIMPLE"})
     */
    private $regimen = 'SIMPLE';

    /**
     * @var string|null
     *
     * @ORM\Column(name="respuesta_dian", type="text", length=0, nullable=true)
     */
    private $respuestaDian;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cufe", type="string", length=255, nullable=true)
     */
    private $cufe;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pdf", type="text", length=0, nullable=true)
     */
    private $pdf;

    /**
     * @var string|null
     *
     * @ORM\Column(name="xml", type="text", length=0, nullable=true)
     */
    private $xml;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cuerpo_jsonf", type="text", length=0, nullable=true)
     */
    private $cuerpoJsonf;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cuerpo_jsonc", type="text", length=0, nullable=true)
     */
    private $cuerpoJsonc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="respuesta_correo", type="text", length=0, nullable=true)
     */
    private $respuestaCorreo;

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
     * @var \CondicionPago
     *
     * @ORM\ManyToOne(targetEntity="CondicionPago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cond_de_pago", referencedColumnName="id")
     * })
     */
    private $condDePago;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNumeroFactura(): ?string
    {
        return $this->numeroFactura;
    }

    public function setNumeroFactura(string $numeroFactura): self
    {
        $this->numeroFactura = $numeroFactura;

        return $this;
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

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getFechaVencimiento(): ?\DateTimeInterface
    {
        return $this->fechaVencimiento;
    }

    public function setFechaVencimiento(\DateTimeInterface $fechaVencimiento): self
    {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    public function getFechaValidacion(): ?\DateTimeInterface
    {
        return $this->fechaValidacion;
    }

    public function setFechaValidacion(?\DateTimeInterface $fechaValidacion): self
    {
        $this->fechaValidacion = $fechaValidacion;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(?string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getAnulado(): ?bool
    {
        return $this->anulado;
    }

    public function setAnulado(?bool $anulado): self
    {
        $this->anulado = $anulado;

        return $this;
    }

    public function getAnuladoUsuario(): ?string
    {
        return $this->anuladoUsuario;
    }

    public function setAnuladoUsuario(?string $anuladoUsuario): self
    {
        $this->anuladoUsuario = $anuladoUsuario;

        return $this;
    }

    public function getAnuladoFecha(): ?\DateTimeInterface
    {
        return $this->anuladoFecha;
    }

    public function setAnuladoFecha(?\DateTimeInterface $anuladoFecha): self
    {
        $this->anuladoFecha = $anuladoFecha;

        return $this;
    }

    public function getFormaDePago(): ?string
    {
        return $this->formaDePago;
    }

    public function setFormaDePago(string $formaDePago): self
    {
        $this->formaDePago = $formaDePago;

        return $this;
    }

    public function getTipoFactura(): ?string
    {
        return $this->tipoFactura;
    }

    public function setTipoFactura(string $tipoFactura): self
    {
        $this->tipoFactura = $tipoFactura;

        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getFacturaResolucionId(): ?string
    {
        return $this->facturaResolucionId;
    }

    public function setFacturaResolucionId(string $facturaResolucionId): self
    {
        $this->facturaResolucionId = $facturaResolucionId;

        return $this;
    }

    public function getHora(): ?\DateTimeInterface
    {
        return $this->hora;
    }

    public function setHora(\DateTimeInterface $hora): self
    {
        $this->hora = $hora;

        return $this;
    }

    public function getSubtotal(): ?string
    {
        return $this->subtotal;
    }

    public function setSubtotal(string $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getTotalIva(): ?string
    {
        return $this->totalIva;
    }

    public function setTotalIva(string $totalIva): self
    {
        $this->totalIva = $totalIva;

        return $this;
    }

    public function getTotalReteIva(): ?string
    {
        return $this->totalReteIva;
    }

    public function setTotalReteIva(string $totalReteIva): self
    {
        $this->totalReteIva = $totalReteIva;

        return $this;
    }

    public function getTotalReteIca(): ?string
    {
        return $this->totalReteIca;
    }

    public function setTotalReteIca(string $totalReteIca): self
    {
        $this->totalReteIca = $totalReteIca;

        return $this;
    }

    public function getTotalReteFuente(): ?string
    {
        return $this->totalReteFuente;
    }

    public function setTotalReteFuente(string $totalReteFuente): self
    {
        $this->totalReteFuente = $totalReteFuente;

        return $this;
    }

    public function getTotalReteFuenteG(): ?string
    {
        return $this->totalReteFuenteG;
    }

    public function setTotalReteFuenteG(string $totalReteFuenteG): self
    {
        $this->totalReteFuenteG = $totalReteFuenteG;

        return $this;
    }

    public function getReteFuente(): ?string
    {
        return $this->reteFuente;
    }

    public function setReteFuente(string $reteFuente): self
    {
        $this->reteFuente = $reteFuente;

        return $this;
    }

    public function getReteIca(): ?string
    {
        return $this->reteIca;
    }

    public function setReteIca(string $reteIca): self
    {
        $this->reteIca = $reteIca;

        return $this;
    }

    public function getReteIva(): ?string
    {
        return $this->reteIva;
    }

    public function setReteIva(string $reteIva): self
    {
        $this->reteIva = $reteIva;

        return $this;
    }

    public function getDescuento(): ?string
    {
        return $this->descuento;
    }

    public function setDescuento(string $descuento): self
    {
        $this->descuento = $descuento;

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

    public function getRegimen(): ?string
    {
        return $this->regimen;
    }

    public function setRegimen(string $regimen): self
    {
        $this->regimen = $regimen;

        return $this;
    }

    public function getRespuestaDian(): ?string
    {
        return $this->respuestaDian;
    }

    public function setRespuestaDian(?string $respuestaDian): self
    {
        $this->respuestaDian = $respuestaDian;

        return $this;
    }

    public function getCufe(): ?string
    {
        return $this->cufe;
    }

    public function setCufe(?string $cufe): self
    {
        $this->cufe = $cufe;

        return $this;
    }

    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    public function setPdf(?string $pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }

    public function getXml(): ?string
    {
        return $this->xml;
    }

    public function setXml(?string $xml): self
    {
        $this->xml = $xml;

        return $this;
    }

    public function getCuerpoJsonf(): ?string
    {
        return $this->cuerpoJsonf;
    }

    public function setCuerpoJsonf(?string $cuerpoJsonf): self
    {
        $this->cuerpoJsonf = $cuerpoJsonf;

        return $this;
    }

    public function getCuerpoJsonc(): ?string
    {
        return $this->cuerpoJsonc;
    }

    public function setCuerpoJsonc(?string $cuerpoJsonc): self
    {
        $this->cuerpoJsonc = $cuerpoJsonc;

        return $this;
    }

    public function getRespuestaCorreo(): ?string
    {
        return $this->respuestaCorreo;
    }

    public function setRespuestaCorreo(?string $respuestaCorreo): self
    {
        $this->respuestaCorreo = $respuestaCorreo;

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

    public function getCondDePago(): ?CondicionPago
    {
        return $this->condDePago;
    }

    public function setCondDePago(?CondicionPago $condDePago): self
    {
        $this->condDePago = $condDePago;

        return $this;
    }


}
