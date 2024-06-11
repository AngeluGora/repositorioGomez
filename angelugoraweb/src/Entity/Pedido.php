<?php

namespace App\Entity;

use App\Repository\PedidoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PedidoRepository::class)]
class Pedido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 255)]
    private ?string $estado = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalPrecio = null;

    /**
     * @var Collection<int, LineaPedido>
     */
    #[ORM\OneToMany(targetEntity: LineaPedido::class, mappedBy: 'pedido')]
    private Collection $lineaPedidos;

    public function __construct()
    {
        $this->lineaPedidos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getTotalPrecio(): ?string
    {
        return $this->totalPrecio;
    }

    public function setTotalPrecio(string $totalPrecio): static
    {
        $this->totalPrecio = $totalPrecio;

        return $this;
    }

    /**
     * @return Collection<int, LineaPedido>
     */
    public function getLineaPedidos(): Collection
    {
        return $this->lineaPedidos;
    }

    public function addLineaPedido(LineaPedido $lineaPedido): static
    {
        if (!$this->lineaPedidos->contains($lineaPedido)) {
            $this->lineaPedidos->add($lineaPedido);
            $lineaPedido->setPedido($this);
        }

        return $this;
    }

    public function removeLineaPedido(LineaPedido $lineaPedido): static
    {
        if ($this->lineaPedidos->removeElement($lineaPedido)) {
            // set the owning side to null (unless already changed)
            if ($lineaPedido->getPedido() === $this) {
                $lineaPedido->setPedido(null);
            }
        }

        return $this;
    }
}
