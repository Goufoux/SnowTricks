<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrickRepository")
 * @UniqueEntity(
 *  fields={"name"},
 *  message="Un trick nommé {{ value }} existe déjà")
 */
class Trick
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank(message = "Cette valeur ne peut être vide")
     * @Assert\Length(
     *  min=3, 
     *  minMessage = "Cette valeur doit être supérieur ou égale à {{ limit }} caractères",
     *  max=50,
     *  maxMessage = "Cette valeur doit être inférieur ou égale  à {{ limit }} caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message = "Cette valeur ne peut être vide")
     * @Assert\Length(
     *  min=15, 
     *  minMessage = "Cette valeur doit être supérieur ou égale à {{ limit }} caractères",
     *  max=5000,
     *  maxMessage = "Cette valeur doit être inférieur ou égale  à {{ limit }} caractères")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Media", mappedBy="trick", cascade={"persist", "merge", "remove"})
     */
    private $media;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="trick", cascade={"remove"})
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrickGroup", inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trickGroup;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\VideoLink", mappedBy="trick", cascade={"persist", "merge", "remove"})
     */
    private $videoLinks;

    /**
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    public function __construct()
    {
        $this->media = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->videoLinks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media[] = $medium;
            $medium->setTrick($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->contains($medium)) {
            $this->media->removeElement($medium);
            // set the owning side to null (unless already changed)
            if ($medium->getTrick() === $this) {
                $medium->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    public function getTrickGroup(): ?TrickGroup
    {
        return $this->trickGroup;
    }

    public function setTrickGroup(?TrickGroup $trickGroup): self
    {
        $this->trickGroup = $trickGroup;

        return $this;
    }

    /**
     * @return Collection|VideoLink[]
     */
    public function getVideoLinks(): Collection
    {
        return $this->videoLinks;
    }

    public function addVideoLink(VideoLink $videoLink): self
    {
        if (!$this->videoLinks->contains($videoLink)) {
            $this->videoLinks[] = $videoLink;
            $videoLink->setTrick($this);
        }

        return $this;
    }

    public function removeVideoLink(VideoLink $videoLink): self
    {
        if ($this->videoLinks->contains($videoLink)) {
            $this->videoLinks->removeElement($videoLink);
            // set the owning side to null (unless already changed)
            if ($videoLink->getTrick() === $this) {
                $videoLink->setTrick(null);
            }
        }

        return $this;
    }
}
