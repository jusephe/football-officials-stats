<?php

namespace App\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Assessor
 *
 * @ORM\Table(name="assessor")
 * @ORM\Entity(repositoryClass="App\Admin\Repository\AssessorRepository")
 */
class Assessor
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=8, nullable=false)
     * @ORM\Id
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 7,
     *      max = 8
     * )
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 4,
     *      max = 80
     * )
     */
    private $name;


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getNameWithId(): ?string
    {
        return $this->name . ' (' . $this->id . ')';
    }

}
