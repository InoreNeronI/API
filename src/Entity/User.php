<?php

namespace App\Entity;

use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *	normalizationContext={"groups"={"users:read"}},
 *	denormalizationContext={"groups"={"users:write"}},
 * )
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private readonly int $id;

    
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    #[Groups(['users:read', 'users:write'])]
    private ?string $firstName = null;

    
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    #[Groups(['users:read', 'users:write'])]
    private ?string $lastName = null;

    
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['users:read', 'users:write'])]
    private string $email;

    
    #[ORM\Column(type: 'json')]
    #[Groups(['user:reads', 'users:write'])]
    private array $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    #[Groups(['users:write'])]
    private string $password;

    #[Groups('users:write')]
    private ?string $plainPassword = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
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
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function __toString(): string
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
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): string
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return '';
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return '#'.(string)$this->getId();
    }
}
