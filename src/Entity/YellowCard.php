<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * YellowCard
 *
 * @ORM\Table(name="yellow_card", indexes={ @ORM\Index(name="yellow_card_game_id_fk", columns={"game_id"}) })
 * @ORM\Entity(repositoryClass="App\Repository\YellowCardRepository")
 */
class YellowCard
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
     * @ORM\Column(name="minute", type="string", length=3, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 1,
     *      max = 3
     * )
     */
    private $minute;

    /**
     * @var \Game
     *
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="yellowCards")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="game_id", referencedColumnName="id", nullable=false)
     * })
     * @Assert\NotBlank
     */
    private $game;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMinute(): ?string
    {
        return $this->minute;
    }

    public function setMinute(string $minute): self
    {
        $this->minute = $minute;

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

}
