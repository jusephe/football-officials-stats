<?php

namespace App\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Team
 *
 * @ORM\Table(name="team", uniqueConstraints={@UniqueConstraint(name="team__un", columns={"full_name"})})
 * @ORM\Entity(repositoryClass="App\Admin\Repository\TeamRepository")
 * @UniqueEntity("fullName")
 */
class Team
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="club_id", type="string", length=10, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 6,
     *      max = 9
     * )
     */
    private $clubId;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=140, nullable=false, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 1,
     *      max = 140
     * )
     */
    private $fullName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="short_name", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      min = 1,
     *      max = 50
     * )
     */
    private $shortName;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClubId(): ?string
    {
        return $this->clubId;
    }

    public function setClubId(string $clubId): self
    {
        $this->clubId = $clubId;

        return $this;
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
