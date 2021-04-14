<?php

namespace App\Entity;

use App\Helpers\Helpers;
use App\Repository\VideosRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VideosRepository::class)
 */
class Videos
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
    private $url;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="videos")
     */
    private $trick;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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
}
