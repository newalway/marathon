<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShippingRate
 *
 * @ORM\Table(name="shipping_rate")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\ShippingRateRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ShippingRate
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
     * @var string
     *
     * @ORM\Column(name="rate_type", type="string", length=255)
     */
    private $rateType;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="minimum_range", type="integer")
     */
    private $minimumRange;

    /**
     * @ORM\Column(name="maximum_range", type="integer")
     */
    private $maximumRange;

    /**
     * @ORM\Column(name="rate_amount", type="integer")
     */
    private $rateAmount;

    /**
     * @ORM\Column(name="variable_cost", type="integer")
     */
    private $variableCost;

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
     * Get the value of Rate Type
     *
     * @return string
     */
    public function getRateType()
    {
        return $this->rateType;
    }

    /**
     * Set the value of Rate Type
     *
     * @param string rateType
     *
     * @return self
     */
    public function setRateType($rateType)
    {
        $this->rateType = $rateType;

        return $this;
    }

    /**
     * Get the value of Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of Title
     *
     * @param string title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of Minimum Range
     *
     * @return mixed
     */
    public function getMinimumRange()
    {
        return $this->minimumRange;
    }

    /**
     * Set the value of Minimum Range
     *
     * @param mixed minimumRange
     *
     * @return self
     */
    public function setMinimumRange($minimumRange)
    {
        $this->minimumRange = $minimumRange;

        return $this;
    }

    /**
     * Get the value of Maximum Range
     *
     * @return mixed
     */
    public function getMaximumRange()
    {
        return $this->maximumRange;
    }

    /**
     * Set the value of Maximum Range
     *
     * @param mixed maximumRange
     *
     * @return self
     */
    public function setMaximumRange($maximumRange)
    {
        $this->maximumRange = $maximumRange;

        return $this;
    }

    /**
     * Get the value of Rate Amount
     *
     * @return mixed
     */
    public function getRateAmount()
    {
        return $this->rateAmount;
    }

    /**
     * Set the value of Rate Amount
     *
     * @param mixed rateAmount
     *
     * @return self
     */
    public function setRateAmount($rateAmount)
    {
        $this->rateAmount = $rateAmount;

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
     * Get the value of Variable Cost
     *
     * @return mixed
     */
    public function getVariableCost()
    {
        return $this->variableCost;
    }

    /**
     * Set the value of Variable Cost
     *
     * @param mixed variableCost
     *
     * @return self
     */
    public function setVariableCost($variableCost)
    {
        $this->variableCost = $variableCost;

        return $this;
    }

}
