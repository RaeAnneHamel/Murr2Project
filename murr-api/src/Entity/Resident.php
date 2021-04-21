<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ResidentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AcmeAssert;

// Strongly considered getting rid of the get for itemOperations
/**
 * @ApiResource(
 *     collectionOperations={
 *     "post",
 *     "get"={"security"="is_granted('ROLE_USER')"}
 *    },
 *     itemOperations={
 *     "get"={"security"="is_granted('ROLE_USER')"}
 *    },
 *     normalizationContext={"groups"={"resident:read"}},
 *     denormalizationContext={"groups"={"resident:write"}}
 * )
 * @AcmeAssert\PhoneAndEmailBothLeftBlank
 * @ORM\Entity(repositoryClass=ResidentRepository::class)
 */
class Resident implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(message = "The ID has to be zero or a positive number")
     * @Groups("resident:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\Email(message = "The email is not a valid email.")
     * @Assert\Length(allowEmptyString="true", max = 150, maxMessage = "Email has more than {{ limit }} characters.")
     * @Groups("resident:read", "resident:write")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Length(allowEmptyString="true", min=10, max = 10, exactMessage = "Phone needs to be {{ limit }} digits.")
     * @Assert\Regex(pattern="/^[0-9]/", message="Phone number must only contain numbers.")
     * @Groups("resident:read", "resident:write")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity=Point::class, mappedBy="resident")
     */
    private $points;

    /**
     * @ORM\OneToOne(targetEntity=Profile::class, mappedBy="resident", cascade={"persist", "remove"})
     * @Groups("resident:write", "resident:read")
    */
    private $profile;

    /**
     * @return Profile
     */
    public function getProfile(): Profile
    {
        if(is_null($this->profile))
        {
            $this->profile = new Profile();
            $this->profile->setResident($this);
        }
        return $this->profile;
    }

    /**
     * @param ?Profile $profile
     */
    public function setProfile(?Profile $profile): void
    {
        $this->profile = is_null($profile) ? new Profile() : $profile;
        $this->profile->setResident($this);

    }

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @Assert\NotBlank(message = "Password should not be left blank.")
     * @Assert\Length(min=7, max = 30, exactMessage = "Password needs to be {{ limit }} digits.")
     * @Groups("resident:write")
     */
    private $plainPassword;

    public function __construct()
    {
        $this->points = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection|Point[]
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addPoint(Point $point): self
    {
        if (!$this->points->contains($point)) {
            $this->points[] = $point;
            $point->addResident($this);
        }
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

    public function removePoint(Point $point): self
    {
        if ($this->points->removeElement($point)) {
            $point->removeResident($this);
        }
        return $this;
    }

    /*
     * Needed For interface
     * */

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

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }


    public function getUsername()
    {
        //not needed, method in resident repo being used instead
    }
}
