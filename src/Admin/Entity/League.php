<?php

namespace App\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * League
 *
 * @ORM\Table(name="league", uniqueConstraints={@UniqueConstraint(name="league__un", columns={"full_name"})})
 * @ORM\Entity(repositoryClass="App\Admin\Repository\LeagueRepository")
 * @UniqueEntity("fullName")
 */
class League
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=80, nullable=false, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 2,
     *      max = 80
     * )
     */
    private $fullName;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=20, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 2,
     *      max = 20
     * )
     */
    private $shortName;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(?string $shortName): self
    {
        $this->shortName = $shortName;

        return $this;
    }

}
