<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserAdditionalAttributesRepository")
 */
class UserAdditionalAttributes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json_array")
     */
    private $attributes_json;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="userAdditionalAttributes", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttributesJson()
    {
        return $this->attributes_json;
    }

    public function setAttributesJson($attributes_json): self
    {
        $this->attributes_json = $attributes_json;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
