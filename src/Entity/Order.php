<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\{ApiResource, ApiSubresource};
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={"get", "post"},
 *     itemOperations={"get", "put", "delete"}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="`order`")
 * @ORM\HasLifecycleCallbacks
 */
class Order
{
    public const STATE_NEW = 'new';
    public const STATE_CONFIRMED = 'confirmed';
    public const STATE_PICKED_UP = 'picked-up';
    public const STATE_DELIVERED = 'delivered';
    public const STATE_CANCELLED = 'cancelled';
    public const STATE_CANCELLED_BY_SYSTEM = 'cancelled-by-system';
    public const STATE_ABORTED = 'aborted';

    public const STATES = [
        self::STATE_NEW,
        self::STATE_CONFIRMED,
        self::STATE_PICKED_UP,
        self::STATE_DELIVERED,
        self::STATE_CANCELLED,
        self::STATE_CANCELLED_BY_SYSTEM,
        self::STATE_ABORTED,
    ];

    public const TRANSITION_CHECKOUT = 'checkout';
    public const TRANSITION_BACK_TO_NEW = 'backToNew';
    public const TRANSITION_PICK_UP = 'pickUp';
    public const TRANSITION_DELIVER = 'deliver';
    public const TRANSITION_CANCEL = 'cancel';
    public const TRANSITION_ABORT = 'abort';

    public static function getTransitions(): array
    {
        return [
            self::TRANSITION_CHECKOUT => 'Checkout',
            self::TRANSITION_BACK_TO_NEW => 'To New',
            self::TRANSITION_PICK_UP => 'Pick Up',
            self::TRANSITION_DELIVER => 'To Delivered',
            self::TRANSITION_CANCEL => 'Cancel',
            self::TRANSITION_ABORT => 'Abort',
        ];
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Choice(choices=Order::STATES, message="Choose a valid state")
     */
    private $state = self::STATE_NEW;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     * @ApiSubresource
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", inversedBy="orders")
     * @ApiSubresource
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderProduct", mappedBy="order")
     * @ApiSubresource
     */
    private $orderProducts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->orderProducts = new ArrayCollection();

        $this->registry = $registry;
    }

    public function __toString(): string
    {
        return '#' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        if (in_array($state, self::STATES)) {
            $this->state = $state;
        } else {
            throw new ValidatorException('Unknown state: "' . $state . '""');
        }

        $this->state = $state;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|OrderProduct[]
     */
    public function getOrderProducts(): ?Collection
    {
        return $this->orderProducts;
    }

    public function addOrderProduct(OrderProduct $orderProduct): self
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts[] = $orderProduct;
            $orderProduct->setOrder($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): self
    {
        if ($this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->removeElement($orderProduct);
            // set the owning side to null (unless already changed)
            if ($orderProduct->getOrder() === $this) {
                $orderProduct->setOrder(null);
            }
        }

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}
