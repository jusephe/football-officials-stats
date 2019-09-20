<?php

namespace App\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * NominationList
 *
 * @ORM\Table(name="nomination_list", indexes={@ORM\Index(name="nomination_list_official_id_fk", columns={"official_id"})})
 * @ORM\Entity(repositoryClass="App\Admin\Repository\NominationListRepository")
 */
class NominationList
{
    /**
     * @var int
     *
     * @ORM\Column(name="season", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @Assert\NotBlank
     * @Assert\Range(
     *      min = 1950,
     *      max = 2070)
     */
    private $season;

    /**
     * @var string
     *
     * @ORM\Column(name="part_of_season", type="string", columnDefinition="ENUM('Jaro', 'Podzim')", nullable=false)
     * @ORM\Id
     * @Assert\NotBlank
     */
    private $partOfSeason;

    /**
     * @var \Official
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Official", inversedBy="nominationLists")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="official_id", referencedColumnName="id", nullable=false)
     * })
     * @Assert\NotBlank
     */
    private $official;

    /**
     * @var string
     *
     * @ORM\Column(name="league_level_name", type="string", length=20, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 2,
     *      max = 20
     * )
     */
    private $leagueLevelName;


    // check for valid enum value
    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        $parts = ['Jaro', 'Podzim'];

        if (!in_array($this->getPartOfSeason(), $parts)) {
            $context->buildViolation('Nepovolená hodnota! Možné hodnoty jsou "Jaro" a "Podzim".')
                ->atPath('partOfSeason')
                ->addViolation();
        }
    }


    public function getSeason(): ?int
    {
        return $this->season;
    }

    public function setSeason(int $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getPartOfSeason(): ?string
    {
        return $this->partOfSeason;
    }

    public function setPartOfSeason(string $partOfSeason): self
    {
        $this->partOfSeason = $partOfSeason;

        return $this;
    }

    public function getLeagueLevelName(): ?string
    {
        return $this->leagueLevelName;
    }

    public function setLeagueLevelName(string $leagueLevelName): self
    {
        $this->leagueLevelName = $leagueLevelName;

        return $this;
    }

    public function getOfficial(): ?Official
    {
        return $this->official;
    }

    public function setOfficial(?Official $official): self
    {
        $this->official = $official;

        return $this;
    }

}
