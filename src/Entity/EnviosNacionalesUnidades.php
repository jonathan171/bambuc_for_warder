<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EnviosNacionalesUnidades
 *
 * @ORM\Table(name="envios_nacionales_unidades", indexes={@ORM\Index(name="envio_nacional_id", columns={"envio_nacional_id"})})
 * @ORM\Entity
 */
class EnviosNacionalesUnidades
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
     * @var string
     *
     * @ORM\Column(name="peso", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $peso = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="valor_declarado", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $valorDeclarado = '0.00';

    /**
     * @var int
     *
     * @ORM\Column(name="numero_referencia", type="integer", nullable=false)
     */
    private $numeroReferencia;

    /**
     * @var string
     *
     * @ORM\Column(name="largo", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $largo = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="alto", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $alto = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="ancho", type="decimal", precision=20, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $ancho = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="numero_guia", type="string", length=255, nullable=false)
     */
    private $numeroGuia;

    /**
     * @var \EnviosNacionales
     *
     * @ORM\ManyToOne(targetEntity="EnviosNacionales")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="envio_nacional_id", referencedColumnName="id")
     * })
     */
    private $envioNacional;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getValorDeclarado(): ?string
    {
        return $this->valorDeclarado;
    }

    public function setValorDeclarado(string $valorDeclarado): self
    {
        $this->valorDeclarado = $valorDeclarado;

        return $this;
    }

    public function getNumeroReferencia(): ?int
    {
        return $this->numeroReferencia;
    }

    public function setNumeroReferencia(int $numeroReferencia): self
    {
        $this->numeroReferencia = $numeroReferencia;

        return $this;
    }

    public function getLargo(): ?string
    {
        return $this->largo;
    }

    public function setLargo(string $largo): self
    {
        $this->largo = $largo;

        return $this;
    }

    public function getAlto(): ?string
    {
        return $this->alto;
    }

    public function setAlto(string $alto): self
    {
        $this->alto = $alto;

        return $this;
    }

    public function getAncho(): ?string
    {
        return $this->ancho;
    }

    public function setAncho(string $ancho): self
    {
        $this->ancho = $ancho;

        return $this;
    }

    public function getNumeroGuia(): ?string
    {
        return $this->numeroGuia;
    }

    public function setNumeroGuia(string $numeroGuia): self
    {
        $this->numeroGuia = $numeroGuia;

        return $this;
    }

    public function getEnvioNacional(): ?EnviosNacionales
    {
        return $this->envioNacional;
    }

    public function setEnvioNacional(?EnviosNacionales $envioNacional): self
    {
        $this->envioNacional = $envioNacional;

        return $this;
    }


}
