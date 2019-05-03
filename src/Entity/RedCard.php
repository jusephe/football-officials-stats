<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RedCard
 *
 * @ORM\Table(name="red_card", indexes={@ORM\Index(name="red_card_offence_id_fk", columns={"offence_id"}), @ORM\Index(name="red_card_game_id_fk", columns={"game_id"}), @ORM\Index(name="red_card_team_id_fk", columns={"team_id"})})
 * @ORM\Entity
 */
class RedCard
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
     * @ORM\Column(name="person", type="string", length=80, nullable=false)
     */
    private $person;

    /**
     * @var int|null
     *
     * @ORM\Column(name="minute", type="smallint", nullable=true, options={"unsigned"=true})
     */
    private $minute;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @var int|null
     *
     * @ORM\Column(name="weeks", type="smallint", nullable=true, options={"unsigned"=true})
     */
    private $weeks;

    /**
     * @var int|null
     *
     * @ORM\Column(name="games", type="smallint", nullable=true, options={"unsigned"=true})
     */
    private $games;

    /**
     * @var int|null
     *
     * @ORM\Column(name="fine", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $fine;

    /**
     * @var int|null
     *
     * @ORM\Column(name="fee", type="smallint", nullable=true, options={"unsigned"=true})
     */
    private $fee;

    /**
     * @var \Game
     *
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="redCards")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="game_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $game;

    /**
     * @var \Offence
     *
     * @ORM\ManyToOne(targetEntity="Offence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="offence_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $offence;

    /**
     * @var \Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $team;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?string
    {
        return $this->person;
    }

    public function setPerson(string $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getMinute(): ?int
    {
        return $this->minute;
    }

    public function setMinute(?int $minute): self
    {
        $this->minute = $minute;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getWeeks(): ?int
    {
        return $this->weeks;
    }

    public function setWeeks(?int $weeks): self
    {
        $this->weeks = $weeks;

        return $this;
    }

    public function getGames(): ?int
    {
        return $this->games;
    }

    public function setGames(?int $games): self
    {
        $this->games = $games;

        return $this;
    }

    public function getFine(): ?int
    {
        return $this->fine;
    }

    public function setFine(?int $fine): self
    {
        $this->fine = $fine;

        return $this;
    }

    public function getFee(): ?int
    {
        return $this->fee;
    }

    public function setFee(?int $fee): self
    {
        $this->fee = $fee;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getOffence(): ?Offence
    {
        return $this->offence;
    }

    public function setOffence(?Offence $offence): self
    {
        $this->offence = $offence;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }


}
