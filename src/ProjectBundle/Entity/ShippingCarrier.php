<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShippingCarrier
 *
 * @ORM\Table(name="shipping_carrier")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\ShippingCarrierRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ShippingCarrier
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
    * @ORM\Column(name="tracking_url", type="text", length=65535)
    */
    private $trackingUrl;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
	private $updatedAt;

	/** @ORM\Column(name="created_at", type="datetime") */
	private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="TrackingNumber", mappedBy="shippingCarrier")
     */
    private $trackingNumbers;

    public function __construct()
    {
        $this->trackingNumbers = new ArrayCollection();
    }

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
     * Get the value of Name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of Name
     *
     * @param mixed name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of Tracking Url
     *
     * @return mixed
     */
    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }

    /**
     * Set the value of Tracking Url
     *
     * @param mixed trackingUrl
     *
     * @return self
     */
    public function setTrackingUrl($trackingUrl)
    {
        $this->trackingUrl = $trackingUrl;

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
     * Get the value of Tracking Numbers
     *
     * @return mixed
     */
    public function getTrackingNumbers()
    {
        return $this->trackingNumbers;
    }

    /**
     * Set the value of Tracking Numbers
     *
     * @param mixed trackingNumbers
     *
     * @return self
     */
    public function setTrackingNumbers($trackingNumbers)
    {
        $this->trackingNumbers = $trackingNumbers;

        return $this;
    }

}
