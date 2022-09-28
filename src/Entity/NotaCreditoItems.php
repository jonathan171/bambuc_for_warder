<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotaCreditoItems
 *
 * @ORM\Table(name="nota_credito_items", indexes={@ORM\Index(name="nota_credito_id", columns={"nota_credito_id"})})
 * @ORM\Entity
 */
class NotaCreditoItems
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
     * @var int
     *
     * @ORM\Column(name="cantidad", type="integer", nullable=false)
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", length=65535, nullable=false)
     */
    private $descripcion;

    /**
     * @var float
     *
     * @ORM\Column(name="valor_unitario", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorUnitario = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="subtotal", type="float", precision=10, scale=0, nullable=false)
     */
    private $subtotal = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float", precision=10, scale=0, nullable=false)
     */
    private $iva = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="valor_iva", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorIva = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", precision=10, scale=0, nullable=false)
     */
    private $total = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255, nullable=false, options={"comment"="identificador del item"})
     */
    private $codigo;

    /**
     * @var float
     *
     * @ORM\Column(name="retencion_fuente", type="float", precision=10, scale=0, nullable=false)
     */
    private $retencionFuente = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="valor_retencion_fuente", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorRetencionFuente = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="tasa_descuento", type="float", precision=10, scale=0, nullable=false)
     */
    private $tasaDescuento = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="valor_descuento", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorDescuento = '0';

    /**
     * @var \NotaCredito
     *
     * @ORM\ManyToOne(targetEntity="NotaCredito")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nota_credito_id", referencedColumnName="id")
     * })
     */
    private $notaCredito;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

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

    public function getValorUnitario(): ?float
    {
        return $this->valorUnitario;
    }

    public function setValorUnitario(float $valorUnitario): self
    {
        $this->valorUnitario = $valorUnitario;

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

    public function getIva(): ?float
    {
        return $this->iva;
    }

    public function setIva(float $iva): self
    {
        $this->iva = $iva;

        return $this;
    }

    public function getValorIva(): ?float
    {
        return $this->valorIva;
    }

    public function setValorIva(float $valorIva): self
    {
        $this->valorIva = $valorIva;

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

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getRetencionFuente(): ?float
    {
        return $this->retencionFuente;
    }

    public function setRetencionFuente(float $retencionFuente): self
    {
        $this->retencionFuente = $retencionFuente;

        return $this;
    }

    public function getValorRetencionFuente(): ?float
    {
        return $this->valorRetencionFuente;
    }

    public function setValorRetencionFuente(float $valorRetencionFuente): self
    {
        $this->valorRetencionFuente = $valorRetencionFuente;

        return $this;
    }

    public function getTasaDescuento(): ?float
    {
        return $this->tasaDescuento;
    }

    public function setTasaDescuento(float $tasaDescuento): self
    {
        $this->tasaDescuento = $tasaDescuento;

        return $this;
    }

    public function getValorDescuento(): ?float
    {
        return $this->valorDescuento;
    }

    public function setValorDescuento(float $valorDescuento): self
    {
        $this->valorDescuento = $valorDescuento;

        return $this;
    }

    public function getNotaCredito(): ?NotaCredito
    {
        return $this->notaCredito;
    }

    public function setNotaCredito(?NotaCredito $notaCredito): self
    {
        $this->notaCredito = $notaCredito;

        return $this;
    }


}
