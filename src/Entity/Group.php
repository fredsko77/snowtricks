<?php

namespace App\Entity;

use App\Helpers\Helpers;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`group`")
 */
class Group
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Trick::class, mappedBy="group")
     */
    private $tricks;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * _hydrate
     *
     * @param  mixed $data
     *
     * @return void
     */
    public function _hydrate(array $data): self
    {
        foreach ($data as $k => $d) {
            $method = 'set' . (new Helpers)->getMethod($k);
            method_exists($this, $method) ? $this->$method($d) : null;
        }

        return $this;
    }

    public function __construct()
    {
        $this->tricks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks[] = $trick;
            $trick->setGroup($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->removeElement($trick)) {
            // set the owning side to null (unless already changed)
            if ($trick->getGroup() === $this) {
                $trick->setGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function serialize(): array
    {

        $helpers = new Helpers;

        $properties = $helpers->getProperties(self::class);

        $json_encode = [];

        foreach ($properties as $property) {
            $method = 'get' . $helpers->getMethod($property);
            if (method_exists($this, $method)) {
                $json_encode[$property] = $this->$method();
            }
        }

        return $json_encode;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
