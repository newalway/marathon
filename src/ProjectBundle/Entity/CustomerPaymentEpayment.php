<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerPaymentEpayment
 *
 * @ORM\Table(name="customer_payment_epayment")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\CustomerPaymentEpaymentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CustomerPaymentEpayment
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
     * @ORM\OneToOne(targetEntity="CustomerOrder", inversedBy="customerPaymentEpayment")
     * @ORM\JoinColumn(name="customer_order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $customerOrder;

    /**
	 * @ORM\Column(name="amount", type="decimal", scale=2)
	 */
	private $amount;

    /**
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
    private $updatedAt;

    /** @ORM\Column(name="created_at", type="datetime") */
    private $createdAt;

    /**
     * @ORM\Column(name="transaction_id", type="string", length=255, nullable = true)
     */
    private $transactionId;

    /**
     * @ORM\Column(name="decision", type="string", length=255, nullable = true)
     */
    private $decision;

    /**
     * @ORM\Column(name="message", type="string", length=255, nullable = true)
     */
    private $message;

    /**
     * @ORM\Column(name="reason_code", type="string", length=255, nullable = true)
     */
    private $reasonCode;

    /**
     * @ORM\Column(name="reference_number", type="string", length=255, nullable = true)
     */
    private $referenceNumber;

    /**
     * @ORM\Column(name="card_number", type="string", length=255, nullable = true)
     */
    private $cardNumber;

    /**
     * @ORM\Column(name="card_expiry_date", type="string", length=255, nullable = true)
     */
    private $cardExpiryDate;

    /**
     * @ORM\Column(name="card_issuer", type="string", length=255, nullable = true)
     */
    private $cardIssuer;

    /**
     * @ORM\Column(name="card_scheme", type="string", length=255, nullable = true)
     */
    private $cardScheme;

    /**
     * @ORM\Column(name="card_country", type="string", length=45, nullable = true)
     */
    private $cardCountry;

    /**
	 * @ORM\Column(name="auth_amount", type="decimal", scale=2, nullable = true)
	 */
	private $authAmount;

    /**
     * @ORM\Column(name="currency", type="string", length=45, nullable = true)
     */
    private $currency;

    /**
     * @ORM\Column(name="auth_time", type="datetime", nullable = true)
     */
    private $authTime;

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
     * Get the value of Amount
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of Amount
     *
     * @param mixed amount
     *
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * Get the value of Transaction Id
     *
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set the value of Transaction Id
     *
     * @param mixed transactionId
     *
     * @return self
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get the value of Decision
     *
     * @return mixed
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * Set the value of Decision
     *
     * @param mixed decision
     *
     * @return self
     */
    public function setDecision($decision)
    {
        $this->decision = $decision;

        return $this;
    }

    /**
     * Get the value of Message
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of Message
     *
     * @param mixed message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of Reason Code
     *
     * @return mixed
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * Set the value of Reason Code
     *
     * @param mixed reasonCode
     *
     * @return self
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * Get the value of Reference Number
     *
     * @return mixed
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * Set the value of Reference Number
     *
     * @param mixed referenceNumber
     *
     * @return self
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    /**
     * Get the value of Card Number
     *
     * @return mixed
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * Set the value of Card Number
     *
     * @param mixed cardNumber
     *
     * @return self
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    /**
     * Get the value of Card Expiry Date
     *
     * @return mixed
     */
    public function getCardExpiryDate()
    {
        return $this->cardExpiryDate;
    }

    /**
     * Set the value of Card Expiry Date
     *
     * @param mixed cardExpiryDate
     *
     * @return self
     */
    public function setCardExpiryDate($cardExpiryDate)
    {
        $this->cardExpiryDate = $cardExpiryDate;

        return $this;
    }

    /**
     * Get the value of Card Issuer
     *
     * @return mixed
     */
    public function getCardIssuer()
    {
        return $this->cardIssuer;
    }

    /**
     * Set the value of Card Issuer
     *
     * @param mixed cardIssuer
     *
     * @return self
     */
    public function setCardIssuer($cardIssuer)
    {
        $this->cardIssuer = $cardIssuer;

        return $this;
    }

    /**
     * Get the value of Card Scheme
     *
     * @return mixed
     */
    public function getCardScheme()
    {
        return $this->cardScheme;
    }

    /**
     * Set the value of Card Scheme
     *
     * @param mixed cardScheme
     *
     * @return self
     */
    public function setCardScheme($cardScheme)
    {
        $this->cardScheme = $cardScheme;

        return $this;
    }

    /**
     * Get the value of Card Country
     *
     * @return mixed
     */
    public function getCardCountry()
    {
        return $this->cardCountry;
    }

    /**
     * Set the value of Card Country
     *
     * @param mixed cardCountry
     *
     * @return self
     */
    public function setCardCountry($cardCountry)
    {
        $this->cardCountry = $cardCountry;

        return $this;
    }

    /**
     * Get the value of Auth Amount
     *
     * @return mixed
     */
    public function getAuthAmount()
    {
        return $this->authAmount;
    }

    /**
     * Set the value of Auth Amount
     *
     * @param mixed authAmount
     *
     * @return self
     */
    public function setAuthAmount($authAmount)
    {
        $this->authAmount = $authAmount;

        return $this;
    }

    /**
     * Get the value of Currency
     *
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set the value of Currency
     *
     * @param mixed currency
     *
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get the value of Auth Time
     *
     * @return mixed
     */
    public function getAuthTime()
    {
        return $this->authTime;
    }

    /**
     * Set the value of Auth Time
     *
     * @param mixed authTime
     *
     * @return self
     */
    public function setAuthTime($authTime)
    {
        $this->authTime = $authTime;

        return $this;
    }

}
