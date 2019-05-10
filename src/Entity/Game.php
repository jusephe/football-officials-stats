<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Game
 *
 * @ORM\Table(name="game", indexes={@ORM\Index(name="game_ar2_official_id_fk", columns={"ar2_official_id"}), @ORM\Index(name="game_away_team_id_fk", columns={"away_team_id"}), @ORM\Index(name="game_league_id_fk", columns={"league_id"}), @ORM\Index(name="game_ar1_official_id_fk", columns={"ar1_official_id"}), @ORM\Index(name="game_assessor_id_fk", columns={"assessor_id"}), @ORM\Index(name="game_home_team_id_fk", columns={"home_team_id"}), @ORM\Index(name="game_referee_official_id_fk", columns={"referee_official_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
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
     * @ORM\Column(name="season", type="smallint", nullable=false, options={"unsigned"=true})
     * @Assert\Range(
     *      min = 1950,
     *      max = 2070)
     */
    private $season;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_autumn", type="boolean", nullable=false)
     */
    private $isAutumn;

    /**
     * @var int
     *
     * @ORM\Column(name="round", type="smallint", nullable=false, options={"unsigned"=true})
     * @Assert\Range(
     *      min = 1,
     *      max = 48)
     */
    private $round;

    /**
     * @var \Official
     *
     * @ORM\ManyToOne(targetEntity="Official")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ar1_official_id", referencedColumnName="id")
     * })
     */
    private $ar1Official;

    /**
     * @var \Official
     *
     * @ORM\ManyToOne(targetEntity="Official")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ar2_official_id", referencedColumnName="id")
     * })
     */
    private $ar2Official;

    /**
     * @var \Assessor
     *
     * @ORM\ManyToOne(targetEntity="Assessor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="assessor_id", referencedColumnName="id")
     * })
     */
    private $assessor;

    /**
     * @var \Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="away_team_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $awayTeam;

    /**
     * @var \Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="home_team_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $homeTeam;

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
     * @ORM\ManyToOne(targetEntity="Official")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="referee_official_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $refereeOfficial;

    /**
     * @var \RedCard
     *
     * @ORM\OneToMany(targetEntity="RedCard", mappedBy="game", cascade={"persist"})
     */
    private $redCards;

    /**
     * @var \YellowCard
     *
     * @ORM\OneToMany(targetEntity="YellowCard", mappedBy="game", cascade={"persist"})
     * @Assert\Valid
     */
    private $yellowCards;


    public function __construct()
    {
        $this->redCards = new ArrayCollection();
        $this->yellowCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIsAutumn(): ?bool
    {
        return $this->isAutumn;
    }

    public function setIsAutumn(bool $isAutumn): self
    {
        $this->isAutumn = $isAutumn;

        return $this;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getAr1Official(): ?Official
    {
        return $this->ar1Official;
    }

    public function setAr1Official(?Official $ar1Official): self
    {
        $this->ar1Official = $ar1Official;

        return $this;
    }

    public function getAr2Official(): ?Official
    {
        return $this->ar2Official;
    }

    public function setAr2Official(?Official $ar2Official): self
    {
        $this->ar2Official = $ar2Official;

        return $this;
    }

    public function getAssessor(): ?Assessor
    {
        return $this->assessor;
    }

    public function setAssessor(?Assessor $assessor): self
    {
        $this->assessor = $assessor;

        return $this;
    }

    public function getAwayTeam(): ?Team
    {
        return $this->awayTeam;
    }

    public function setAwayTeam(?Team $awayTeam): self
    {
        $this->awayTeam = $awayTeam;

        return $this;
    }

    public function getHomeTeam(): ?Team
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(?Team $homeTeam): self
    {
        $this->homeTeam = $homeTeam;

        return $this;
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

    public function getRefereeOfficial(): ?Official
    {
        return $this->refereeOfficial;
    }

    public function setRefereeOfficial(?Official $refereeOfficial): self
    {
        $this->refereeOfficial = $refereeOfficial;

        return $this;
    }

    /**
     * @return Collection|RedCard[]
     */
    public function getRedCards(): Collection
    {
        return $this->redCards;
    }

    public function addRedCard(RedCard $redCard): self
    {
        if (!$this->redCards->contains($redCard)) {
            $this->redCards[] = $redCard;
            $redCard->setGame($this);
        }

        return $this;
    }

    public function removeRedCard(RedCard $redCard): self
    {
        if ($this->redCards->contains($redCard)) {
            $this->redCards->removeElement($redCard);
            // set the owning side to null (unless already changed)
            if ($redCard->getGame() === $this) {
                $redCard->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|YellowCard[]
     */
    public function getYellowCards(): Collection
    {
        return $this->yellowCards;
    }

    public function addYellowCard(YellowCard $yellowCard): self
    {
        if (!$this->yellowCards->contains($yellowCard)) {
            $this->yellowCards[] = $yellowCard;
            $yellowCard->setGame($this);
        }

        return $this;
    }

    public function removeYellowCard(YellowCard $yellowCard): self
    {
        if ($this->yellowCards->contains($yellowCard)) {
            $this->yellowCards->removeElement($yellowCard);
            // set the owning side to null (unless already changed)
            if ($yellowCard->getGame() === $this) {
                $yellowCard->setGame(null);
            }
        }

        return $this;
    }

}
