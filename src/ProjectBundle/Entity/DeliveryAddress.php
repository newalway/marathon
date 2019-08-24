<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeliveryAddress
 *
 * @ORM\Table(name="delivery_address")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\DeliveryAddressRepository")
 * @ORM\HasLifecycleCallbacks
 */
class DeliveryAddress
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
    */
    private $title;

    /**
     * @ORM\Column(name="first_name", type="string", length=255, nullable=false)
    */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
    */
    private $lastName;

    /**
    * @ORM\Column(name="address", type="string", length=255, nullable = true)
    */
    private $address;

    /**
     * @ORM\Column(name="phone", type="string", length=45, nullable=true)
    */
    private $phone;

    /**
     * @ORM\Column(name="district", type="string", length=255, nullable=true)
    */
    private $district;

    /**
     * @ORM\Column(name="amphure", type="string", length=255, nullable=true)
    */
    private $amphure;

    /**
    * @ORM\Column(name="province", type="string", length=255, nullable = true)
    */
    private $province;

    /**
    * @ORM\Column(name="postcode", type="string", length=45, nullable = true)
    */
    private $postCode;

    /**
    * @ORM\Column(name="tax_payer_id", type="string", length=255, nullable = true)
    */
    private $taxPayerId;

    /**
    * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
    */
    private $companyName;

    /**
    * @ORM\Column(name="head_office", type="string", length=255, nullable=true)
    */
    private $headOffice;

    /**
    * @ORM\Column(name="default_shipping", type="boolean", options={"default":0})
    */
    private $defaultShipping = false;

    /**
    * @ORM\Column(name="default_tax", type="boolean", options={"default":0})
    */
    private $defaultTax = false;

    /**
    * @ORM\Column(name="position", type="integer", nullable = false, options={"default":0})
    */
    private $position = 0;

    /**
    * @ORM\Column(name="deleted", type="integer", nullable = false, options={"default":0})
    */
    private $deleted = 0;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
	private $updatedAt;

	/** @ORM\Column(name="created_at", type="datetime") */
	private $createdAt;

    /**
    * @ORM\ManyToOne(targetEntity="User", inversedBy="deliveryAddress")
    * @ORM\JoinColumn(name="fos_user_id", referencedColumnName="id", onDelete="CASCADE")
    */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="CountryCode", inversedBy="deliveryAddress")
     * @ORM\JoinColumn(name="country_code_id", referencedColumnName="id")
     */
    private $countryCode;

    /**
    * @ORM\Column(name="latitude", type="string", length=255, nullable = true)
    */
    private $latitude;

    /**
    * @ORM\Column(name="longitude", type="string", length=255, nullable = true)
    */
    private $longitude;

    /**
    * @ORM\Column(name="place_id", type="string", length=255, nullable = true)
    */
    private $placeId;

    /**
	 *
	 * @ORM\PrePersist
	 * @ORM\PreUpdate
	 */
	public function updatedTimestamps()
	{
	    $this->setUpdatedAt(new \DateTime('now'));
	    if ($this->getCreatedAt() == null) {
	    	$this->setCreatedAt(new \DateTime('now'));
	    }
	}

    /**
     * Get the value of Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of Id
     *
     * @param int id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of Title
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of Title
     *
     * @param mixed title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of First Name
     *
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of First Name
     *
     * @param mixed firstName
     *
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of Last Name
     *
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the value of Last Name
     *
     * @param mixed lastName
     *
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of Address
     *
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of Address
     *
     * @param mixed address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of Phone
     *
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of Phone
     *
     * @param mixed phone
     *
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of District
     *
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Set the value of District
     *
     * @param mixed district
     *
     * @return self
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get the value of Province
     *
     * @return mixed
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set the value of Province
     *
     * @param mixed province
     *
     * @return self
     */
    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get the value of Post Code
     *
     * @return mixed
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * Set the value of Post Code
     *
     * @param mixed postCode
     *
     * @return self
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * Get the value of Tax Payer Id
     *
     * @return mixed
     */
    public function getTaxPayerId()
    {
        return $this->taxPayerId;
    }

    /**
     * Set the value of Tax Payer Id
     *
     * @param mixed taxPayerId
     *
     * @return self
     */
    public function setTaxPayerId($taxPayerId)
    {
        $this->taxPayerId = $taxPayerId;

        return $this;
    }

    /**
     * Get the value of Default Shipping
     *
     * @return mixed
     */
    public function getDefaultShipping()
    {
        return $this->defaultShipping;
    }

    /**
     * Set the value of Default Shipping
     *
     * @param mixed defaultShipping
     *
     * @return self
     */
    public function setDefaultShipping($defaultShipping)
    {
        $this->defaultShipping = $defaultShipping;

        return $this;
    }

    /**
     * Get the value of Default Tax
     *
     * @return mixed
     */
    public function getDefaultTax()
    {
        return $this->defaultTax;
    }

    /**
     * Set the value of Default Tax
     *
     * @param mixed defaultTax
     *
     * @return self
     */
    public function setDefaultTax($defaultTax)
    {
        $this->defaultTax = $defaultTax;

        return $this;
    }

    /**
     * Get the value of Position
     *
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of Position
     *
     * @param mixed position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the value of Deleted
     *
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set the value of Deleted
     *
     * @param mixed deleted
     *
     * @return self
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get the value of Updated At
     *
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of Updated At
     *
     * @param mixed updatedAt
     *
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of Created At
     *
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of Created At
     *
     * @param mixed createdAt
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of User
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of User
     *
     * @param mixed user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }


    /**
     * Get the value of Company Name
     *
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set the value of Company Name
     *
     * @param mixed companyName
     *
     * @return self
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get the value of Head Office
     *
     * @return mixed
     */
    public function getHeadOffice()
    {
        return $this->headOffice;
    }

    /**
     * Set the value of Head Office
     *
     * @param mixed headOffice
     *
     * @return self
     */
    public function setHeadOffice($headOffice)
    {
        $this->headOffice = $headOffice;

        return $this;
    }


    /**
     * Get the value of Country Code
     *
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set the value of Country Code
     *
     * @param mixed countryCode
     *
     * @return self
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }


    /**
     * Get the value of Latitude
     *
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set the value of Latitude
     *
     * @param mixed latitude
     *
     * @return self
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get the value of Longitude
     *
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set the value of Longitude
     *
     * @param mixed longitude
     *
     * @return self
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get the value of Place Id
     *
     * @return mixed
     */
    public function getPlaceId()
    {
        return $this->placeId;
    }

    /**
     * Set the value of Place Id
     *
     * @param mixed placeId
     *
     * @return self
     */
    public function setPlaceId($placeId)
    {
        $this->placeId = $placeId;

        return $this;
    }

    /**
     * Get the value of Amphure
     *
     * @return mixed
     */
    public function getAmphure()
    {
        return $this->amphure;
    }

    /**
     * Set the value of Amphure
     *
     * @param mixed amphure
     *
     * @return self
     */
    public function setAmphure($amphure)
    {
        $this->amphure = $amphure;

        return $this;
    }

}
