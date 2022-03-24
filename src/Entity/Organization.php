<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
class Organization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Unique]
    private $name;

    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: CodeRepo::class, orphanRemoval: true)]
    private $codeRepos;

    public function __construct()
    {
        $this->codeRepos = new ArrayCollection();
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

    /**
     * @return Collection<int, CodeRepo>
     */
    public function getCodeRepos(): Collection
    {
        return $this->codeRepos;
    }

    public function addCodeRepo(CodeRepo $codeRepo): self
    {
        if (!$this->codeRepos->contains($codeRepo)) {
            $this->codeRepos[] = $codeRepo;
            $codeRepo->setOrganization($this);
        }

        return $this;
    }

    public function removeCodeRepo(CodeRepo $codeRepo): self
    {
        if ($this->codeRepos->removeElement($codeRepo)) {
            // set the owning side to null (unless already changed)
            if ($codeRepo->getOrganization() === $this) {
                $codeRepo->setOrganization(null);
            }
        }

        return $this;
    }
}
