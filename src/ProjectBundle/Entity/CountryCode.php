<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CountryCode
 *
 * @ORM\Table(name="country_code")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\CountryCodeRepository")
 */
class CountryCode
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
     * @ORM\OneToMany(targetEntity="DeliveryAddress", mappedBy="countryCode")
     */
    private $deliveryAddress;

    /**
     * @ORM\Column(name="country", type="string", length=255)
    */
    private $country;

    /**
     * @ORM\Column(name="iso_code", type="string", length=5)
    */
    private $isoCode;

    /**
     * @ORM\Column(name="dial_code", type="string", length=45, nullable=true)
    */
    private $dialCode;

    /**
     * @ORM\Column(name="nationality", type="string", length=255, nullable=true)
    */
    private $nationality;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position = 0;

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
     * Get the value of Delivery Address
     *
     * @return mixed
     */
    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }

    /**
     * Set the value of Delivery Address
     *
     * @param mixed deliveryAddress
     *
     * @return self
     */
    public function setDeliveryAddress($deliveryAddress)
    {
        $this->deliveryAddress = $deliveryAddress;

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
     * Get the value of Iso Code
     *
     * @return mixed
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Set the value of Iso Code
     *
     * @param mixed isoCode
     *
     * @return self
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * Get the value of Dial Code
     *
     * @return mixed
     */
    public function getDialCode()
    {
        return $this->dialCode;
    }

    /**
     * Set the value of Dial Code
     *
     * @param mixed dialCode
     *
     * @return self
     */
    public function setDialCode($dialCode)
    {
        $this->dialCode = $dialCode;

        return $this;
    }

    /**
     * Get the value of Nationality
     *
     * @return mixed
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set the value of Nationality
     *
     * @param mixed nationality
     *
     * @return self
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

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

}
