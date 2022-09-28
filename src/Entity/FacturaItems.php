<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaItems
 *
 * @ORM\Table(name="factura_items", indexes={@ORM\Index(name="factura_clientes_id", columns={"factura_clientes_id"}), @ORM\Index(name="unidad_id", columns={"unidad_id"})})
 * @ORM\Entity
 */
class FacturaItems
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
     * @var string
     *
     * @ORM\Column(name="valor_unitario", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $valorUnitario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="subtotal", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $subtotal;

    /**
     * @var string
     *
     * @ORM\Column(name="iva", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $iva = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="valor_iva", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $valorIva = '0.00';

    /**
     * @var string|null
     *
     * @ORM\Column(name="total", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $total;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=255, nullable=true)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="retencion_fuente", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $retencionFuente = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="valor_retencion_fuente", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $valorRetencionFuente = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="tasa_descuento", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $tasaDescuento = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="valor_descuento", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $valorDescuento = '0.00';

    /**
     * @var \Factura
     *
     * @ORM\ManyToOne(targetEntity="Factura")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="factura_clientes_id", referencedColumnName="id")
     * })
     */
    private $facturaClientes;

    /**
     * @var \UnidadesMedida
     *
     * @ORM\ManyToOne(targetEntity="UnidadesMedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_id", referencedColumnName="id")
     * })
     */
    private $unidad;

    public function getId(): ?string
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

    public function getValorUnitario(): ?string
    {
        return $this->valorUnitario;
    }

    public function setValorUnitario(string $valorUnitario): self
    {
        $this->valorUnitario = $valorUnitario;

        return $this;
    }

    public function getSubtotal(): ?string
    {
        return $this->subtotal;
    }

    public function setSubtotal(?string $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getIva(): ?string
    {
        return $this->iva;
    }

    public function setIva(string $iva): self
    {
        $this->iva = $iva;

        return $this;
    }

    public function getValorIva(): ?string
    {
        return $this->valorIva;
    }

    public function setValorIva(string $valorIva): self
    {
        $this->valorIva = $valorIva;

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

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getRetencionFuente(): ?string
    {
        return $this->retencionFuente;
    }

    public function setRetencionFuente(string $retencionFuente): self
    {
        $this->retencionFuente = $retencionFuente;

        return $this;
    }

    public function getValorRetencionFuente(): ?string
    {
        return $this->valorRetencionFuente;
    }

    public function setValorRetencionFuente(string $valorRetencionFuente): self
    {
        $this->valorRetencionFuente = $valorRetencionFuente;

        return $this;
    }

    public function getTasaDescuento(): ?string
    {
        return $this->tasaDescuento;
    }

    public function setTasaDescuento(string $tasaDescuento): self
    {
        $this->tasaDescuento = $tasaDescuento;

        return $this;
    }

    public function getValorDescuento(): ?string
    {
        return $this->valorDescuento;
    }

    public function setValorDescuento(string $valorDescuento): self
    {
        $this->valorDescuento = $valorDescuento;

        return $this;
    }

    public function getFacturaClientes(): ?Factura
    {
        return $this->facturaClientes;
    }

    public function setFacturaClientes(?Factura $facturaClientes): self
    {
        $this->facturaClientes = $facturaClientes;

        return $this;
    }

    public function getUnidad(): ?UnidadesMedida
    {
        return $this->unidad;
    }

    public function setUnidad(?UnidadesMedida $unidad): self
    {
        $this->unidad = $unidad;

        return $this;
    }


}
