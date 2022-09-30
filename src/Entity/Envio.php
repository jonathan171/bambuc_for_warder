<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Envio
 *
 * @ORM\Table(name="envio", indexes={@ORM\Index(name="pais_destino", columns={"pais_destino"}), @ORM\Index(name="factura_items_id", columns={"factura_items_id"}), @ORM\Index(name="pais_origen", columns={"pais_origen"})})
 * @ORM\Entity(repositoryClass="App\Repository\EnvioRepository")
 */
class Envio
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
     * @ORM\Column(name="codigo", type="string", length=255, nullable=false)
     */
    private $codigo;

    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="integer", nullable=false)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_envio", type="string", length=255, nullable=false)
     */
    private $numeroEnvio;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", length=0, nullable=false)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="peso_estimado", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $pesoEstimado;

    /**
     * @var string
     *
     * @ORM\Column(name="peso_real", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $pesoReal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="total_peso_cobrar", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $totalPesoCobrar;

    /**
     * @var string
     *
     * @ORM\Column(name="total_a_cobrar", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $totalACobrar = '0.00';

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_envio", type="date", nullable=false)
     */
    private $fechaEnvio;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_estimada_entrega", type="date", nullable=false)
     */
    private $fechaEstimadaEntrega;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa", type="string", length=255, nullable=false)
     */
    private $empresa;

    /**
     * @var string
     *
     * @ORM\Column(name="quien_envia", type="string", length=255, nullable=false)
     */
    private $quienEnvia;

    /**
     * @var string
     *
     * @ORM\Column(name="quien_recibe", type="string", length=255, nullable=false)
     */
    private $quienRecibe;

    /**
     * @var string|null
     *
     * @ORM\Column(name="json_recibido", type="text", length=0, nullable=true)
     */
    private $jsonRecibido;

    /**
     * @var bool
     *
     * @ORM\Column(name="facturado", type="boolean", nullable=false)
     */
    private $facturado;

    /**
     * @var \Pais
     *
     * @ORM\ManyToOne(targetEntity="Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pais_destino", referencedColumnName="id")
     * })
     */
    private $paisDestino;

    /**
     * @var \FacturaItems
     *
     * @ORM\ManyToOne(targetEntity="FacturaItems")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="factura_items_id", referencedColumnName="id")
     * })
     */
    private $facturaItems;

    /**
     * @var \Pais
     *
     * @ORM\ManyToOne(targetEntity="Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pais_origen", referencedColumnName="id")
     * })
     */
    private $paisOrigen;

    /**
     * @var bool
     *
     * @ORM\Column(name="verificado", type="boolean", nullable=false)
     */
    private $verificado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="referencia", type="text", length=0, nullable=true)
     */
    private $referencia;

    

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEstado(): ?int
    {
        return $this->estado;
    }

    public function setEstado(int $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getNumeroEnvio(): ?string
    {
        return $this->numeroEnvio;
    }

    public function setNumeroEnvio(string $numeroEnvio): self
    {
        $this->numeroEnvio = $numeroEnvio;

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

    public function getPesoEstimado(): ?string
    {
        return $this->pesoEstimado;
    }

    public function setPesoEstimado(string $pesoEstimado): self
    {
        $this->pesoEstimado = $pesoEstimado;

        return $this;
    }

    public function getPesoReal(): ?string
    {
        return $this->pesoReal;
    }

    public function setPesoReal(string $pesoReal): self
    {
        $this->pesoReal = $pesoReal;

        return $this;
    }

    public function getTotalPesoCobrar(): ?string
    {
        return $this->totalPesoCobrar;
    }

    public function setTotalPesoCobrar(?string $totalPesoCobrar): self
    {
        $this->totalPesoCobrar = $totalPesoCobrar;

        return $this;
    }

    public function getTotalACobrar(): ?string
    {
        return $this->totalACobrar;
    }

    public function setTotalACobrar(string $totalACobrar): self
    {
        $this->totalACobrar = $totalACobrar;

        return $this;
    }

    public function getFechaEnvio(): ?\DateTimeInterface
    {
        return $this->fechaEnvio;
    }

    public function setFechaEnvio(\DateTimeInterface $fechaEnvio): self
    {
        $this->fechaEnvio = $fechaEnvio;

        return $this;
    }

    public function getFechaEstimadaEntrega(): ?\DateTimeInterface
    {
        return $this->fechaEstimadaEntrega;
    }

    public function setFechaEstimadaEntrega(\DateTimeInterface $fechaEstimadaEntrega): self
    {
        $this->fechaEstimadaEntrega = $fechaEstimadaEntrega;

        return $this;
    }

    public function getEmpresa(): ?string
    {
        return $this->empresa;
    }

    public function setEmpresa(string $empresa): self
    {
        $this->empresa = $empresa;

        return $this;
    }

    public function getQuienEnvia(): ?string
    {
        return $this->quienEnvia;
    }

    public function setQuienEnvia(string $quienEnvia): self
    {
        $this->quienEnvia = $quienEnvia;

        return $this;
    }

    public function getQuienRecibe(): ?string
    {
        return $this->quienRecibe;
    }

    public function setQuienRecibe(string $quienRecibe): self
    {
        $this->quienRecibe = $quienRecibe;

        return $this;
    }

    public function getJsonRecibido(): ?string
    {
        return $this->jsonRecibido;
    }

    public function setJsonRecibido(?string $jsonRecibido): self
    {
        $this->jsonRecibido = $jsonRecibido;

        return $this;
    }

    public function getFacturado(): ?bool
    {
        return $this->facturado;
    }

    public function setFacturado(bool $facturado): self
    {
        $this->facturado = $facturado;

        return $this;
    }
    public function getVerificado(): ?bool
    {
        return $this->verificado;
    }

    public function setVerificado(bool $verificado): self
    {
        $this->verificado = $verificado;

        return $this;
    }

    public function getReferencia(): ?string
    {
        return $this->referencia;
    }

    public function setReferencia(?string $referencia): self
    {
        $this->referencia = $referencia;

        return $this;
    }

    public function getPaisDestino(): ?Pais
    {
        return $this->paisDestino;
    }

    public function setPaisDestino(?Pais $paisDestino): self
    {
        $this->paisDestino = $paisDestino;

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

    public function getPaisOrigen(): ?Pais
    {
        return $this->paisOrigen;
    }

    public function setPaisOrigen(?Pais $paisOrigen): self
    {
        $this->paisOrigen = $paisOrigen;

        return $this;
    }


}
