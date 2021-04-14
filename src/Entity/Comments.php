<?php

namespace App\Entity;

use App\Helpers\Helpers;
use App\Repository\CommentsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentsRepository::class)
 */
class Comments
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="comments")
     */
    private $trick;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

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
                if ($property === 'user') {
                    $json_encode[$property] = $this->$method()->serialize();
                } else {

                    $json_encode[$property] = $this->$method();
                }
            }
        }
        return $json_encode;
    }
}
