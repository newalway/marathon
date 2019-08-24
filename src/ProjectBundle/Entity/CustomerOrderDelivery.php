<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerOrderDelivery
 *
 * @ORM\Table(name="customer_order_delivery")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\CustomerOrderDeliveryRepository")
 */
class CustomerOrderDelivery
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(name="address_type", type="smallint", options={"unsigned":true, "default":1}) */
    private $addressType = 1;

    /**
     * @ORM\Column(name="first_name", type="string", length=255)
    */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
    */
    private $lastName;

    /**
    * @ORM\Column(name="address", type="string", length=255, nullable=true)
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
    * @ORM\Column(name="province", type="string", length=255, nullable=true)
    */
    private $province;

    /**
    * @ORM\Column(name="country", type="string", length=255, nullable=true)
    */
    private $country;

    /**
    * @ORM\Column(name="postcode", type="string", length=45, nullable=true)
    */
    private $postCode;

    /**
    * @ORM\Column(name="tax_payer_id", type="string", length=255, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="CustomerOrder", inversedBy="customerOrderDeliverys")
     * @ORM\JoinColumn(name="customer_order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $customerOrder;

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
     * Get id
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
     * Get the value of Address Type
     *
     * @return mixed
     */
    public function getAddressType()
    {
        return $this->addressType;
    }

    /**
     * Set the value of Address Type
     *
     * @param mixed addressType
     *
     * @return self
     */
    public function setAddressType($addressType)
    {
        $this->addressType = $addressType;

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
     * Get the value of Country
     *
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the value of Country
     *
     * @param mixed country
     *
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;

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
     * Get the value of Customer Order
     *
     * @return mixed
     */
    public function getCustomerOrder()
    {
        return $this->customerOrder;
    }

    /**
     * Set the value of Customer Order
     *
     * @param mixed customerOrder
     *
     * @return self
     */
    public function setCustomerOrder($customerOrder)
    {
        $this->customerOrder = $customerOrder;

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
