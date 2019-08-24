<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrackingNumber
 *
 * @ORM\Table(name="tracking_number")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\TrackingNumberRepository")
 * @ORM\HasLifecycleCallbacks
 */
class TrackingNumber
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
     * @ORM\Column(name="order_number", type="string", length=255)
     */
    private $orderNumber;

    /**
     * @ORM\Column(name="tracking_number", type="string", length=255)
     */
    private $trackingNumber;

    /**
     * @ORM\ManyToOne(targetEntity="CustomerOrder", inversedBy="trackingNumbers")
     * @ORM\JoinColumn(name="customer_order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $customerOrder;

    /**
     * @ORM\ManyToOne(targetEntity="ShippingCarrier", inversedBy="trackingNumbers")
     * @ORM\JoinColumn(name="shipping_carrier_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    private $shippingCarrier;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
	private $updatedAt;

	/** @ORM\Column(name="created_at", type="datetime") */
	private $createdAt;

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
     * Get the value of Order Number
     *
     * @return mixed
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set the value of Order Number
     *
     * @param mixed orderNumber
     *
     * @return self
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get the value of Tracking Number
     *
     * @return mixed
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    /**
     * Set the value of Tracking Number
     *
     * @param mixed trackingNumber
     *
     * @return self
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;

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
     * Get the value of Shipping Carrier
     *
     * @return mixed
     */
    public function getShippingCarrier()
    {
        return $this->shippingCarrier;
    }

    /**
     * Set the value of Shipping Carrier
     *
     * @param mixed shippingCarrier
     *
     * @return self
     */
    public function setShippingCarrier($shippingCarrier)
    {
        $this->shippingCarrier = $shippingCarrier;

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

}
