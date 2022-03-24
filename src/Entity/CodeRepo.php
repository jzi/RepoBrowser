<?php

namespace App\Entity;

use App\Repository\CodeRepoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CodeRepoRepository::class)]
class CodeRepo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'codeRepos', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private $organization;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $creation_date;

    #[ORM\Column(type: 'float')]
    private $trust_score;

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

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(?\DateTimeInterface $creation_date): self
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getTrustScore(): ?float
    {
        return $this->trust_score;
    }

    public function setTrustScore(float $trust_score): self
    {
        $this->trust_score = $trust_score;

        return $this;
    }
}
