<?php

namespace App\Entity;

use App\Repository\TextRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TextRepository::class)]
class Text
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $id = null;

    #[ORM\Column(type: 'text', columnDefinition: 'longtext')]
    private ?string $text = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }
}
