<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=35)
     * @Assert\NotBlank(message = "Cette valeur ne peut être vide")
     * @Assert\Length(
     *  min=3, 
     *  minMessage = "Cette valeur doit être supérieur ou égale à {{ limit }} caractères",
     *  max=35,
     *  maxMessage = "Cette valeur doit être inférieur ou égale  à {{ limit }} caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=35)
     * @Assert\NotBlank(message = "Cette valeur ne peut être vide")
     * @Assert\Length(
     *  min=3, 
     *  minMessage = "Cette valeur doit être supérieur ou égale à {{ limit }} caractères",
     *  max=35,
     *  maxMessage = "Cette valeur doit être inférieur ou égale  à {{ limit }} caractères")
     */
    private $first_name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $password_renewal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password_token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo_src;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Trick", mappedBy="author")
     */
    private $tricks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author")
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $register_token;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function __construct()
    {
        $this->tricks = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /** userInterface methods */

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->getName(). ' ' .$this->getFirstName();
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordRenewal(): ?\DateTimeInterface
    {
        return $this->password_renewal;
    }

    public function setPasswordRenewal(?\DateTimeInterface $password_renewal): self
    {
        $this->password_renewal = $password_renewal;

        return $this;
    }

    public function getPasswordToken(): ?string
    {
        return $this->password_token;
    }

    public function setPasswordToken(?string $password_token): self
    {
        $this->password_token = $password_token;

        return $this;
    }

    public function getPhotoSrc(): ?string
    {
        return $this->photo_src;
    }

    public function setPhotoSrc(?string $photo_src): self
    {
        $this->photo_src = $photo_src;

        return $this;
    }

    public function hasRoleAdmin()
    {
        return in_array('ROLE_ADMIN', $this->roles);
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks[] = $trick;
            $trick->setAuthor($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->contains($trick)) {
            $this->tricks->removeElement($trick);
            // set the owning side to null (unless already changed)
            if ($trick->getAuthor() === $this) {
                $trick->setAuthor(null);
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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    public function getRegisterToken(): ?string
    {
        return $this->register_token;
    }

    public function setRegisterToken(?string $register_token): self
    {
        $this->register_token = $register_token;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
