<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotaCredito
 *
 * @ORM\Table(name="nota_credito", indexes={@ORM\Index(name="factura_cliente_id", columns={"factura_cliente_id"})})
 * @ORM\Entity
 */
class NotaCredito
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hora", type="time", nullable=false)
     */
    private $hora;

    /**
     * @var int
     *
     * @ORM\Column(name="numero_nota", type="bigint", nullable=false)
     */
    private $numeroNota;

    /**
     * @var float
     *
     * @ORM\Column(name="subtotal", type="float", precision=10, scale=0, nullable=false)
     */
    private $subtotal = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", precision=10, scale=0, nullable=false)
     */
    private $total = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="total_iva", type="float", precision=10, scale=0, nullable=false)
     */
    private $totalIva = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="rete_fuente", type="float", precision=10, scale=0, nullable=false)
     */
    private $reteFuente = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="total_rete_fuente_g", type="float", precision=10, scale=0, nullable=false)
     */
    private $totalReteFuenteG = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="observaciones", type="text", length=65535, nullable=true)
     */
    private $observaciones;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=255, nullable=false, options={"default"="credito"})
     */
    private $tipo = 'credito';

    /**
     * @var int|null
     *
     * @ORM\Column(name="concepto_debito", type="integer", nullable=true)
     */
    private $conceptoDebito;

    /**
     * @var int
     *
     * @ORM\Column(name="concepto_credito", type="integer", nullable=false)
     */
    private $conceptoCredito;

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
     * @var \Factura
     *
     * @ORM\ManyToOne(targetEntity="Factura")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="factura_cliente_id", referencedColumnName="id")
     * })
     */
    private $facturaCliente;

    public function getId(): ?int
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

    public function getHora(): ?\DateTimeInterface
    {
        return $this->hora;
    }

    public function setHora(\DateTimeInterface $hora): self
    {
        $this->hora = $hora;

        return $this;
    }

    public function getNumeroNota(): ?string
    {
        return $this->numeroNota;
    }

    public function setNumeroNota(string $numeroNota): self
    {
        $this->numeroNota = $numeroNota;

        return $this;
    }

    public function getSubtotal(): ?float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getTotalIva(): ?float
    {
        return $this->totalIva;
    }

    public function setTotalIva(float $totalIva): self
    {
        $this->totalIva = $totalIva;

        return $this;
    }

    public function getReteFuente(): ?float
    {
        return $this->reteFuente;
    }

    public function setReteFuente(float $reteFuente): self
    {
        $this->reteFuente = $reteFuente;

        return $this;
    }

    public function getTotalReteFuenteG(): ?float
    {
        return $this->totalReteFuenteG;
    }

    public function setTotalReteFuenteG(float $totalReteFuenteG): self
    {
        $this->totalReteFuenteG = $totalReteFuenteG;

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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getConceptoDebito(): ?int
    {
        return $this->conceptoDebito;
    }

    public function setConceptoDebito(?int $conceptoDebito): self
    {
        $this->conceptoDebito = $conceptoDebito;

        return $this;
    }

    public function getConceptoCredito(): ?int
    {
        return $this->conceptoCredito;
    }

    public function setConceptoCredito(int $conceptoCredito): self
    {
        $this->conceptoCredito = $conceptoCredito;

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

    public function getFacturaCliente(): ?Factura
    {
        return $this->facturaCliente;
    }

    public function setFacturaCliente(?Factura $facturaCliente): self
    {
        $this->facturaCliente = $facturaCliente;

        return $this;
    }


}
