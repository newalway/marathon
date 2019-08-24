<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
    */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
    */
    private $lastName;

    /**
     * @ORM\Column(name="gender", type="string", length=255, nullable=true)
    */
    private $gender;

    /**
     * @ORM\Column(name="phone_number", type="string", length=45, nullable=true)
    */
    private $phoneNumber;

    /**
     * @ORM\Column(name="birth_date", type="date", nullable=true)
    */
    private $birthDate;

    /**
     * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
    */
    private $companyName;

    /** @ORM\Column(name="oauth", type="smallint") */
    private $oauth = 0;
    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;
    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;
    /** @ORM\Column(name="google_id", type="string", length=255, nullable=true) */
    protected $google_id;
    /** @ORM\Column(name="google_access_token", type="string", length=255, nullable=true) */
    protected $google_access_token;
    /** @ORM\Column(name="service_name", type="string", length=255, nullable=true) */
    protected $service_name;
    /** @ORM\Column(name="service_email", type="string", length=255, nullable=true) */
    protected $service_email;

    /** @ORM\Column(name="is_set_password", type="smallint") */
    private $isSetPassword = 0;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
    private $updatedAt;

  	/** @ORM\Column(name="created_at", type="datetime") */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="DeliveryAddress", mappedBy="user")
     */
    private $deliveryAddress;

    /**
     * @ORM\OneToMany(targetEntity="CustomerOrder", mappedBy="user")
     */
    private $customerOrders;

    public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->deliveryAddress = new ArrayCollection();
        $this->customerOrders = new ArrayCollection();
    }

    public function setEmail($email)
    {
        // we use email as the username
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
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
     * Get the value of Gender
     *
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set the value of Gender
     *
     * @param mixed gender
     *
     * @return self
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get the value of Phone Number
     *
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set the value of Phone Number
     *
     * @param mixed phoneNumber
     *
     * @return self
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get the value of Birth Date
     *
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set the value of Birth Date
     *
     * @param mixed birthDate
     *
     * @return self
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

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
     * Get the value of Id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of Id
     *
     * @param mixed id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of Oauth
     *
     * @return mixed
     */
    public function getOauth()
    {
        return $this->oauth;
    }

    /**
     * Set the value of Oauth
     *
     * @param mixed oauth
     *
     * @return self
     */
    public function setOauth($oauth)
    {
        $this->oauth = $oauth;

        return $this;
    }

    /**
     * Get the value of Facebook Id
     *
     * @return mixed
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set the value of Facebook Id
     *
     * @param mixed facebook_id
     *
     * @return self
     */
    public function setFacebookId($facebook_id)
    {
        $this->facebook_id = $facebook_id;

        return $this;
    }

    /**
     * Get the value of Facebook Access Token
     *
     * @return mixed
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set the value of Facebook Access Token
     *
     * @param mixed facebook_access_token
     *
     * @return self
     */
    public function setFacebookAccessToken($facebook_access_token)
    {
        $this->facebook_access_token = $facebook_access_token;

        return $this;
    }

    /**
     * Get the value of Google Id
     *
     * @return mixed
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Set the value of Google Id
     *
     * @param mixed google_id
     *
     * @return self
     */
    public function setGoogleId($google_id)
    {
        $this->google_id = $google_id;

        return $this;
    }

    /**
     * Get the value of Google Access Token
     *
     * @return mixed
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }

    /**
     * Set the value of Google Access Token
     *
     * @param mixed google_access_token
     *
     * @return self
     */
    public function setGoogleAccessToken($google_access_token)
    {
        $this->google_access_token = $google_access_token;

        return $this;
    }


    /**
     * Get the value of Service Name
     *
     * @return mixed
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * Set the value of Service Name
     *
     * @param mixed service_name
     *
     * @return self
     */
    public function setServiceName($service_name)
    {
        $this->service_name = $service_name;

        return $this;
    }


    /**
     * Get the value of Service Email
     *
     * @return mixed
     */
    public function getServiceEmail()
    {
        return $this->service_email;
    }

    /**
     * Set the value of Service Email
     *
     * @param mixed service_email
     *
     * @return self
     */
    public function setServiceEmail($service_email)
    {
        $this->service_email = $service_email;

        return $this;
    }


    /**
     * Get the value of Is Set Password
     *
     * @return mixed
     */
    public function getIsSetPassword()
    {
        return $this->isSetPassword;
    }

    /**
     * Set the value of Is Set Password
     *
     * @param mixed isSetPassword
     *
     * @return self
     */
    public function setIsSetPassword($isSetPassword)
    {
        $this->isSetPassword = $isSetPassword;

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
     * Get the value of Customer Orders
     * 
     * @return mixed
     */
    public function getCustomerOrders()
    {
        return $this->customerOrders;
    }

    /**
     * Set the value of Customer Orders
     *
     * @param mixed customerOrders
     *
     * @return self
     */
    public function setCustomerOrders($customerOrders)
    {
        $this->customerOrders = $customerOrders;

        return $this;
    }

}
