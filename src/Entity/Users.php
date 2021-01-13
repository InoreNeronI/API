<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * 
 * @ApiResource(
 *	normalizationContext={"groups"={"users:read"}},
 *	normalizationContext={"groups"={"users:write"}},
 * )
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
		 * 
		 * @Groups({"users:read", "users:write"})
		 * 
     */
    private $email;

    /**
     * @ORM\Column(type="json")
		 * 
		 * @Groups({"user:reads", "users:write"})
		 * 
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
		 * 
		 * @Groups({"users:write"})
		 * 
     */
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }
		
		/**
		 * 
		 * @Groups("users:write")
		 */
		private $plainPassword;
		
		public function getPlainPassword() : ?string
		{
			return $this->plainPassword;
		}
		
		public function setPlainPassword(string $plainPassword) : self
		{
			$this->plainPassword = $plainPassword;
			
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
		
		public function __toString() : string
		{
			return $this->email;
		}

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
