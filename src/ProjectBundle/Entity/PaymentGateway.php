<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentGateway
 *
 * @ORM\Table(name="payment_gateway")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\PaymentGatewayRepository")
 */
class PaymentGateway
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
     * @ORM\Column(name="gateway", type="array")
    */
    private $gateway;

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
     * Get the value of Gateway
     *
     * @return mixed
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * Set the value of Gateway
     *
     * @param mixed gateway
     *
     * @return self
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;

        foreach ($gateway as $gw) {
            $this->addGateway($gw);
        }

        return $this;
    }

    public function addGateway($gw)
    {
        $gw = strtoupper($gw);

        if (!in_array($gw, $this->gateway, true)) {
            $this->gateway[] = $gw;
        }

        return $this;
    }

}
