<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NominationList
 *
 * @ORM\Table(name="nomination_list", indexes={@ORM\Index(name="nomination_list_league_id_fk", columns={"league_id"}), @ORM\Index(name="nomination_list_official_id_fk", columns={"official_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\NominationListRepository")
 */
class NominationList
{
    /**
     * @var string
     *
     * @ORM\Column(name="season_with_part", type="string", length=12, nullable=false)
     * @ORM\Id
     */
    private $seasonWithPart;

    /**
     * @var \League
     *
     * @ORM\ManyToOne(targetEntity="League")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="league_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $league;

    /**
     * @var \Official
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Official", inversedBy="nominationLists")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="official_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $official;


    public function getSeasonWithPart(): ?string
    {
        return $this->seasonWithPart;
    }

    public function getLeague(): ?League
    {
        return $this->league;
    }

    public function setLeague(?League $league): self
    {
        $this->league = $league;

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
