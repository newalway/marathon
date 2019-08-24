<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PromotionDownloadCounter
 *
 * @ORM\Table(name="promotion_download_counter")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\PromotionDownloadCounterRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PromotionDownloadCounter
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
	   * @ORM\Column(name="ip_address", type="string", length=255, nullable = true)
	   */
		private $ipAddress;

    /**
	   * @ORM\Column(name="browser_name", type="string", length=255, nullable = true)
	   */
		private $browserName;

    /**
	   * @ORM\Column(type="string", length=255, nullable = true)
	   */
		private $platform;

    /**
	   * @ORM\Column(name="platform_version", type="string", length=255, nullable = true)
	   */
		private $platformVersion;

    /**
	   * @ORM\Column(type="string", length=255, nullable = true)
	   */
		private $browser;

    /**
	   * @ORM\Column(type="string", length=255, nullable = true)
	   */
		private $version;

    /**
	   * @ORM\Column(name="country_code", type="string", length=255, nullable = true)
	   */
		private $countryCode;

    /**
	   * @ORM\Column(name="country_name", type="string", length=255, nullable = true)
	   */
		private $countryName;

    /**
	   * @ORM\Column(name="city_name", type="string", length=255, nullable = true)
	   */
		private $cityName;

    /**
	   * @ORM\Column(name="postal_code", type="string", length=255, nullable = true)
	   */
		private $postalCode;

    /**
	   * @ORM\Column(name="location_latitude", type="string", length=255, nullable = true)
	   */
		private $locationLatitude;

    /**
	   * @ORM\Column(name="location_longitude", type="string", length=255, nullable = true)
	   */
		private $locationLongitude;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
	  private $updatedAt;

		/** @ORM\Column(name="created_at", type="datetime") */
	  private $createdAt;

    /**
	   * @ORM\ManyToOne(targetEntity="Promotion", inversedBy="promotionDownloadCounter")
	   * @ORM\JoinColumn(name="promotion_id", referencedColumnName="id", onDelete="CASCADE")
	   */
	  private $promotion;

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
     * Get the value of Ip Address
     *
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set the value of Ip Address
     *
     * @param mixed ipAddress
     *
     * @return self
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get the value of Browser Name
     *
     * @return mixed
     */
    public function getBrowserName()
    {
        return $this->browserName;
    }

    /**
     * Set the value of Browser Name
     *
     * @param mixed browserName
     *
     * @return self
     */
    public function setBrowserName($browserName)
    {
        $this->browserName = $browserName;

        return $this;
    }

    /**
     * Get the value of Platform
     *
     * @return mixed
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set the value of Platform
     *
     * @param mixed platform
     *
     * @return self
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get the value of Browser
     *
     * @return mixed
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * Set the value of Browser
     *
     * @param mixed browser
     *
     * @return self
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;

        return $this;
    }

    /**
     * Get the value of Version
     *
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the value of Version
     *
     * @param mixed version
     *
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = $version;

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
     * Get the value of Country Name
     *
     * @return mixed
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * Set the value of Country Name
     *
     * @param mixed countryName
     *
     * @return self
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;

        return $this;
    }

    /**
     * Get the value of City Name
     *
     * @return mixed
     */
    public function getCityName()
    {
        return $this->cityName;
    }

    /**
     * Set the value of City Name
     *
     * @param mixed cityName
     *
     * @return self
     */
    public function setCityName($cityName)
    {
        $this->cityName = $cityName;

        return $this;
    }

    /**
     * Get the value of Postal Code
     *
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set the value of Postal Code
     *
     * @param mixed postalCode
     *
     * @return self
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get the value of Location Latitude
     *
     * @return mixed
     */
    public function getLocationLatitude()
    {
        return $this->locationLatitude;
    }

    /**
     * Set the value of Location Latitude
     *
     * @param mixed locationLatitude
     *
     * @return self
     */
    public function setLocationLatitude($locationLatitude)
    {
        $this->locationLatitude = $locationLatitude;

        return $this;
    }

    /**
     * Get the value of Location Longitude
     *
     * @return mixed
     */
    public function getLocationLongitude()
    {
        return $this->locationLongitude;
    }

    /**
     * Set the value of Location Longitude
     *
     * @param mixed locationLongitude
     *
     * @return self
     */
    public function setLocationLongitude($locationLongitude)
    {
        $this->locationLongitude = $locationLongitude;

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
     * Get the value of Promotion
     *
     * @return mixed
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * Set the value of Promotion
     *
     * @param mixed promotion
     *
     * @return self
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get the value of Platform Version
     *
     * @return mixed
     */
    public function getPlatformVersion()
    {
        return $this->platformVersion;
    }

    /**
     * Set the value of Platform Version
     *
     * @param mixed platformVersion
     *
     * @return self
     */
    public function setPlatformVersion($platformVersion)
    {
        $this->platformVersion = $platformVersion;

        return $this;
    }

}
