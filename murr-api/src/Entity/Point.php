<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={
 *     "post"={"security"="is_granted('ROLE_USER')"},
 *     "get"={"security"="is_granted('ROLE_USER')"}
 *    },
 *     itemOperations={
 *     "get"={"security"="is_granted('ROLE_USER')"}
 *    }
 * )
 * @ORM\Entity(repositoryClass=PointRepository::class)
 */
class Point
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(message = "The ID has to be zero or a positive number")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(message = "The points has to be greater than zero")
     * @Assert\NotNull(message = "Points cannot be left null"))
     */
    public $num_points;

    /**
     * @ORM\ManyToMany(targetEntity=Resident::class, inversedBy="points")
     * @Assert\Count(min = "1", minMessage = "You must add at least one Resident")
     */
    private $resident;

    public function __construct()
    {
        $this->resident = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getnum_points(): ?int
    {
        return $this->num_points;
    }

    public function setnum_points(int $num_points): self
    {
        $this->num_points = $num_points;

        return $this;
    }

    /**
     * @return Collection|Resident[]
     */
    public function getResident(): Collection
    {
        return $this->resident;
    }

    public function addResident(Resident $residentToAdd): self
    {
        if (!$this->resident->contains($residentToAdd)) {
            $this->resident[] = $residentToAdd;
        }

        return $this;
    }

    public function removeResident(Resident $resident): self
    {
        $this->resident->removeElement($resident);

        return $this;
    }
}
