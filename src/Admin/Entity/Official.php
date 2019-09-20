<?php

namespace App\Admin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Official
 *
 * @ORM\Table(name="official")
 * @ORM\Entity(repositoryClass="App\Admin\Repository\OfficialRepository")
 */
class Official
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

    /**
     * @var \NominationList
     *
     * @ORM\OneToMany(targetEntity="NominationList", mappedBy="official", cascade={"persist"})
     * @ORM\OrderBy({"season" = "ASC", "partOfSeason" = "DESC"})
     * @Assert\Valid
     */
    private $nominationLists;


    public function __construct()
    {
        $this->nominationLists = new ArrayCollection();
    }

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

    /**
     * @return Collection|NominationList[]
     */
    public function getNominationLists(): Collection
    {
        return $this->nominationLists;
    }

    public function addNominationList(NominationList $nominationList): self
    {
        if (!$this->nominationLists->contains($nominationList)) {
            $this->nominationLists[] = $nominationList;
            $nominationList->setOfficial($this);
        }

        return $this;
    }

    public function removeNominationList(NominationList $nominationList): self
    {
        if ($this->nominationLists->contains($nominationList)) {
            $this->nominationLists->removeElement($nominationList);
            // set the owning side to null (unless already changed)
            if ($nominationList->getOfficial() === $this) {
                $nominationList->setOfficial(null);
            }
        }

        return $this;
    }

}
