<?php

namespace App\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Offence
 *
 * @ORM\Table(name="offence")
 * @ORM\Entity(repositoryClass="App\Admin\Repository\OffenceRepository")
 */
class Offence
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
     * @ORM\Column(name="short_name", type="string", length=35, nullable=false)
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=80, nullable=false)
     */
    private $fullName;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): self
    {
        $this->shortName = $shortName;

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

}
