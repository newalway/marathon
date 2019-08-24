<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RequestService
 *
 * @ORM\Table(name="request_service")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\RequestServiceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class RequestService
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
     * @ORM\Column(name="request_title", type="string", length=255)
     */
    private $requestTitle;

    /**
     * @ORM\Column(name="product_title", type="string", length=255)
     */
    private $productTitle;

    /**
     * @ORM\Column(name="product_model", type="string", length=255, nullable = true)
     */
    private $productModel;

    /**
     * @ORM\Column(name="product_serial_number", type="string", length=255, nullable = true)
     */
    private $productSerialNumber;

    /**
     * @ORM\Column(name="product_warranty_number", type="string", length=255, nullable = true)
     */
    private $productWarrantyNumber;

    /**
     * @ORM\Column(name="product_text_type", type="string", length=255, nullable = true)
     */
    private $productTextType;



    /**
     * @ORM\Column(name="request_detail", type="string", length=255)
     */
    private $requestDetail;

    /**
     * @ORM\Column(name="first_name", type="string", length=45, nullable = true)
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=45, nullable = true)
     */
    private $lastName;

    /**
     * @ORM\Column(name="phone", type="string", length=45, nullable = true)
     */
    private $phone;

    /**
     * @ORM\Column(name="email", type="string", length=45, nullable = true)
     */
    private $email;

    /**
     * @ORM\Column(name="address", type="string", length=45, nullable = true)
     */
    private $address;

    /**
     * @ORM\Column(name="district", type="string", length=255, nullable = true)
     */
    private $district;

    /**
     * @ORM\Column(name="sub_district", type="string", length=255, nullable=true)
    */
    private $subDistrict;

    /**
     * @ORM\Column(name="province", type="string", length=45, nullable = true)
     */
    private $province;

    /**
    * @ORM\Column(name="postcode", type="string", length=45, nullable = true)
    */
    private $postCode;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status = 4;

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
     * Get the value of Request Title
     *
     * @return mixed
     */
    public function getRequestTitle()
    {
        return $this->requestTitle;
    }

    /**
     * Set the value of Request Title
     *
     * @param mixed requestTitle
     *
     * @return self
     */
    public function setRequestTitle($requestTitle)
    {
        $this->requestTitle = $requestTitle;

        return $this;
    }

    /**
     * Get the value of Product Title
     *
     * @return mixed
     */
    public function getProductTitle()
    {
        return $this->productTitle;
    }

    /**
     * Set the value of Product Title
     *
     * @param mixed productTitle
     *
     * @return self
     */
    public function setProductTitle($productTitle)
    {
        $this->productTitle = $productTitle;

        return $this;
    }

    /**
     * Get the value of Product Model
     *
     * @return mixed
     */
    public function getProductModel()
    {
        return $this->productModel;
    }

    /**
     * Set the value of Product Model
     *
     * @param mixed productModel
     *
     * @return self
     */
    public function setProductModel($productModel)
    {
        $this->productModel = $productModel;

        return $this;
    }

    /**
     * Get the value of Product Serial Number
     *
     * @return mixed
     */
    public function getProductSerialNumber()
    {
        return $this->productSerialNumber;
    }

    /**
     * Set the value of Product Serial Number
     *
     * @param mixed productSerialNumber
     *
     * @return self
     */
    public function setProductSerialNumber($productSerialNumber)
    {
        $this->productSerialNumber = $productSerialNumber;

        return $this;
    }

    /**
     * Get the value of Request Detail
     *
     * @return mixed
     */
    public function getRequestDetail()
    {
        return $this->requestDetail;
    }

    /**
     * Set the value of Request Detail
     *
     * @param mixed requestDetail
     *
     * @return self
     */
    public function setRequestDetail($requestDetail)
    {
        $this->requestDetail = $requestDetail;

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
     * Get the value of Email
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of Email
     *
     * @param mixed email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

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
     * Get the value of Product Warranty Number
     *
     * @return mixed
     */
    public function getProductWarrantyNumber()
    {
        return $this->productWarrantyNumber;
    }

    /**
     * Set the value of Product Warranty Number
     *
     * @param mixed productWarrantyNumber
     *
     * @return self
     */
    public function setProductWarrantyNumber($productWarrantyNumber)
    {
        $this->productWarrantyNumber = $productWarrantyNumber;

        return $this;
    }

    /**
     * Get the value of Product Text Type
     *
     * @return mixed
     */
    public function getProductTextType()
    {
        return $this->productTextType;
    }

    /**
     * Set the value of Product Text Type
     *
     * @param mixed productTextType
     *
     * @return self
     */
    public function setProductTextType($productTextType)
    {
        $this->productTextType = $productTextType;

        return $this;
    }


    /**
     * Get the value of Sub District
     *
     * @return mixed
     */
    public function getSubDistrict()
    {
        return $this->subDistrict;
    }

    /**
     * Set the value of Sub District
     *
     * @param mixed subDistrict
     *
     * @return self
     */
    public function setSubDistrict($subDistrict)
    {
        $this->subDistrict = $subDistrict;

        return $this;
    }

}
