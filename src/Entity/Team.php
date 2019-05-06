<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
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
     * @var int
     *
     * @ORM\Column(name="club_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $clubId;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=140, nullable=false)
     */
    private $fullName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="short_name", type="string", length=50, nullable=true)
     */
    private $shortName;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClubId(): ?int
    {
        return $this->clubId;
    }

    public function setClubId(int $clubId): self
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
