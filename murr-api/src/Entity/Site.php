<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use App\Controller\SitePointController;

// Using API platform for just 'get' operation at this time.
// Sites will be hardcoded into the database at this point in time.

// for the pagination we had set the max amount of items on the page to 10
// and ordered then alphabetical as well as filtered partial if sent something to filter.
/**
 * @ORM\Entity(repositoryClass=SiteRepository::class)
 * @ApiResource(
 *     itemOperations={
 *     "post",
 *     "get"={"security"="is_granted('ROLE_USER')"}
 *     },
 *     attributes={"pagination_items_per_page"=10, "maximum_items_per_page"=10, "pagination_partial"=false}
 * )
 * @ApiFilter(OrderFilter::class, properties={"siteName": "ASC"})
 * @ApiFilter(SearchFilter::class, properties={"siteName": "partial"})
 *
 */
class Site
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    // For the site name, we will be testing to make sure no digits are added
    // As well as, testing that the site name is between 3 and 100 characters long
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Regex(
     *     pattern="/^[A-Za-z]/",
     *     match=true,
     *     message="Site name cannot have a number"
     * )
     * @Assert\Length(
     *     min = 3,
     *     max = 100,
     *     minMessage = "The site name has to be at least {{ limit }} characters long",
     *     maxMessage = "The site name cannot be longer than {{ limit }} characters"
     * )
     */
    private $siteName;

    // Each site is required to have at least one recycling bin
    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(
     *      message="Site needs to have at least one bin"
     * )
     */
    private $numBins;

    // An array of residents that are living at the site. Can be null.
    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $residents = [];

    // A collection of pickups made at this site. Acts as the many to one relationship
    // between the site and pickups
    /**
     * @ORM\OneToMany(targetEntity=PickUp::class, mappedBy="siteObject")
     */
    private $pickupCollection;

    public function __construct()
    {
        $this->pickupCollection = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteName(): ?string
    {
        return $this->siteName;
    }

    public function setSiteName(string $siteName): self
    {
        $this->siteName = $siteName;

        return $this;
    }

    public function getNumBins(): ?int
    {
        return $this->numBins;
    }

    public function setNumBins(int $numBins): self
    {
        $this->numBins = $numBins;

        return $this;
    }

    public function getResidents(): ?array
    {
        return $this->residents;
    }

    public function setResidents(?array $residents): self
    {
        $this->residents = $residents;

        return $this;
    }

    /**
     * @return Collection|PickUp[]
     */
    public function getPickupCollection(): Collection
    {
        return $this->pickupCollection;
    }

    public function addPickupCollection(PickUp $numCollected): self
    {
        if (!$this->pickupCollection->contains($numCollected)) {
            $this->pickupCollection[] = $numCollected;
            $numCollected->setSite($this);
        }

        return $this;
    }

    public function removePickupCollection(PickUp $numCollected): self
    {
        if ($this->pickupCollection->removeElement($numCollected)) {
            // set the owning side to null (unless already changed)
            if ($numCollected->getSite() === $this) {
                $numCollected->setSite(null);
            }
        }

        return $this;
    }
}
