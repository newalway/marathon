<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeliveryMethod
 *
 * @ORM\Table(name="delivery_method")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\DeliveryMethodRepository")
 */
class DeliveryMethod
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(name="place_order_time", type="time", nullable=true) */
    private $placeOrderTime;

    /** @ORM\Column(name="before_set_date", type="string", length=45, nullable=true) */
    private $beforeSetDate;

    /** @ORM\Column(name="after_set_date", type="string", length=45, nullable=true) */
    private $afterSetDate;

    /**
     * @ORM\Column(name="non_delivery_date", type="array", nullable=true)
    */
    private $nonDeliveryDate;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status = 1;

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
     * Get the value of Place Order Time
     *
     * @return mixed
     */
    public function getPlaceOrderTime()
    {
        return $this->placeOrderTime;
    }

    /**
     * Set the value of Place Order Time
     *
     * @param mixed placeOrderTime
     *
     * @return self
     */
    public function setPlaceOrderTime($placeOrderTime)
    {
        $this->placeOrderTime = $placeOrderTime;

        return $this;
    }

    /**
     * Get the value of Before Set Date
     *
     * @return mixed
     */
    public function getBeforeSetDate()
    {
        return $this->beforeSetDate;
    }

    /**
     * Set the value of Before Set Date
     *
     * @param mixed beforeSetDate
     *
     * @return self
     */
    public function setBeforeSetDate($beforeSetDate)
    {
        $this->beforeSetDate = $beforeSetDate;

        return $this;
    }

    /**
     * Get the value of After Set Date
     *
     * @return mixed
     */
    public function getAfterSetDate()
    {
        return $this->afterSetDate;
    }

    /**
     * Set the value of After Set Date
     *
     * @param mixed afterSetDate
     *
     * @return self
     */
    public function setAfterSetDate($afterSetDate)
    {
        $this->afterSetDate = $afterSetDate;

        return $this;
    }

    /**
     * Get the value of Non Delivery Date
     *
     * @return mixed
     */
    public function getNonDeliveryDate()
    {
        return $this->nonDeliveryDate;
    }

    /**
     * Set the value of Non Delivery Date
     *
     * @param mixed nonDeliveryDate
     *
     * @return self
     */
    public function setNonDeliveryDate($nonDeliveryDate)
    {
        $this->nonDeliveryDate = $nonDeliveryDate;

        return $this;
    }

    /**
     * Get the value of Status
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of Status
     *
     * @param mixed status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

}
