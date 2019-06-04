<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="post", indexes={@ORM\Index(name="post_admin_id_fk", columns={"admin_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", nullable=false, options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 2,
     *      max = 255
     * )
     */
    private $title;

    /**
     * @var \Admin
     *
     * @ORM\ManyToOne(targetEntity="Admin")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="admin_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $admin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime
     */
    private $published;

    /**
     * @ORM\Column(name="contents_md", type="text", nullable=true)
     */
    private $contentsMd;

    /**
     * @ORM\Column(name="contents_html", type="text", nullable=true)
     */
    private $contentsHtml;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(?\DateTimeInterface $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getContentsMd(): ?string
    {
        return $this->contentsMd;
    }

    public function setContentsMd(?string $contentsMd): self
    {
        $this->contentsMd = $contentsMd;

        return $this;
    }

    public function getContentsHtml(): ?string
    {
        return $this->contentsHtml;
    }

    public function setContentsHtml(?string $contentsHtml): self
    {
        $this->contentsHtml = $contentsHtml;

        return $this;
    }

    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(?Admin $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

}
