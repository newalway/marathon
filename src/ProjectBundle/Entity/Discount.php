<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Discount
 *
 * @ORM\Table(name="discount")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\DiscountRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Discount
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
     * @ORM\Column(name="discount_code", type="string", length=255)
     */
    private $discountCode;

    /**
     * @ORM\Column(name="discount_type", type="smallint")
     */
    private $discountType = 1;

    /**
     * @ORM\Column(name="discount_value", type="integer")
     */
    private $discountValue;

    /**
     * @ORM\Column(name="applies_to", type="smallint")
     */
    private $appliesTo = 1;

    /**
     * @ORM\Column(name="only_applies_once_item_per_product", type="boolean")
     */
    private $onlyAppliesOnceItemPerProduct = false;

    /**
     * @ORM\Column(name="minimum_requirement", type="smallint")
     */
    private $minimumRequirement = 1;

    /**
     * @ORM\Column(name="minimum_requirement_amount_value", type="integer", nullable = true)
     */
    private $minimumRequirementAmountValue;

    /**
     * @ORM\Column(name="minimum_requirement_quantity_value", type="integer", nullable = true)
     */
    private $minimumRequirementQuantityValue;

    /**
     * @ORM\Column(name="usage_limit_discount_total", type="boolean")
     */
    private $usageLimitDiscountTotal = false;

    /**
     * @ORM\Column(name="usage_limit_discount_total_value", type="integer", nullable = true)
     */
    private $usageLimitDiscountTotalValue;

    /**
     * @ORM\Column(name="usage_limit_discount_one_per_customer", type="boolean")
     */
    private $usageLimitDiscountOnePerCustomer = false;

    /** @ORM\Column(name="start_date", type="datetime") */
	private $startDate;

    /**
     * @ORM\Column(name="is_end_date", type="boolean")
     */
    private $isEndDate = false;

    /** @ORM\Column(name="end_date", type="datetime", nullable = true) */
	private $endDate;

    /**
     * Many Discount have Many Product.
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="discounts")
     */
    private $products;

    /**
     * Many Discount have Many CustomerOrder.
     * @ORM\ManyToMany(targetEntity="CustomerOrder", mappedBy="discounts")
     */
    private $customerOrders;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
	private $updatedAt;

	/** @ORM\Column(name="created_at", type="datetime") */
	private $createdAt;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->customerOrders = new ArrayCollection();
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
     * Get the value of Discount Code
     *
     * @return mixed
     */
    public function getDiscountCode()
    {
        return $this->discountCode;
    }

    /**
     * Set the value of Discount Code
     *
     * @param mixed discountCode
     *
     * @return self
     */
    public function setDiscountCode($discountCode)
    {
        $this->discountCode = $discountCode;

        return $this;
    }

    /**
     * Get the value of Discount Type
     *
     * @return mixed
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }

    /**
     * Set the value of Discount Type
     *
     * @param mixed discountType
     *
     * @return self
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;

        return $this;
    }

    /**
     * Get the value of Discount Value
     *
     * @return mixed
     */
    public function getDiscountValue()
    {
        return $this->discountValue;
    }

    /**
     * Set the value of Discount Value
     *
     * @param mixed discountValue
     *
     * @return self
     */
    public function setDiscountValue($discountValue)
    {
        $this->discountValue = $discountValue;

        return $this;
    }

    /**
     * Get the value of Applies To
     *
     * @return mixed
     */
    public function getAppliesTo()
    {
        return $this->appliesTo;
    }

    /**
     * Set the value of Applies To
     *
     * @param mixed appliesTo
     *
     * @return self
     */
    public function setAppliesTo($appliesTo)
    {
        $this->appliesTo = $appliesTo;

        return $this;
    }

    /**
     * Get the value of Minimum Requirement
     *
     * @return mixed
     */
    public function getMinimumRequirement()
    {
        return $this->minimumRequirement;
    }

    /**
     * Set the value of Minimum Requirement
     *
     * @param mixed minimumRequirement
     *
     * @return self
     */
    public function setMinimumRequirement($minimumRequirement)
    {
        $this->minimumRequirement = $minimumRequirement;

        return $this;
    }

    /**
     * Get the value of Usage Limit Discount Total
     *
     * @return mixed
     */
    public function getUsageLimitDiscountTotal()
    {
        return $this->usageLimitDiscountTotal;
    }

    /**
     * Set the value of Usage Limit Discount Total
     *
     * @param mixed usageLimitDiscountTotal
     *
     * @return self
     */
    public function setUsageLimitDiscountTotal($usageLimitDiscountTotal)
    {
        $this->usageLimitDiscountTotal = $usageLimitDiscountTotal;

        return $this;
    }

    /**
     * Get the value of Usage Limit Discount Total Value
     *
     * @return mixed
     */
    public function getUsageLimitDiscountTotalValue()
    {
        return $this->usageLimitDiscountTotalValue;
    }

    /**
     * Set the value of Usage Limit Discount Total Value
     *
     * @param mixed usageLimitDiscountTotalValue
     *
     * @return self
     */
    public function setUsageLimitDiscountTotalValue($usageLimitDiscountTotalValue)
    {
        $this->usageLimitDiscountTotalValue = $usageLimitDiscountTotalValue;

        return $this;
    }

    /**
     * Get the value of Usage Limit Discount One Per Customer
     *
     * @return mixed
     */
    public function getUsageLimitDiscountOnePerCustomer()
    {
        return $this->usageLimitDiscountOnePerCustomer;
    }

    /**
     * Set the value of Usage Limit Discount One Per Customer
     *
     * @param mixed usageLimitDiscountOnePerCustomer
     *
     * @return self
     */
    public function setUsageLimitDiscountOnePerCustomer($usageLimitDiscountOnePerCustomer)
    {
        $this->usageLimitDiscountOnePerCustomer = $usageLimitDiscountOnePerCustomer;

        return $this;
    }

    /**
     * Get the value of Start Date
     *
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set the value of Start Date
     *
     * @param mixed startDate
     *
     * @return self
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the value of Is End Date
     *
     * @return mixed
     */
    public function getIsEndDate()
    {
        return $this->isEndDate;
    }

    /**
     * Set the value of Is End Date
     *
     * @param mixed isEndDate
     *
     * @return self
     */
    public function setIsEndDate($isEndDate)
    {
        $this->isEndDate = $isEndDate;

        return $this;
    }

    /**
     * Get the value of End Date
     *
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set the value of End Date
     *
     * @param mixed endDate
     *
     * @return self
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

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
     * Get the value of Minimum Requirement Amount Value
     *
     * @return mixed
     */
    public function getMinimumRequirementAmountValue()
    {
        return $this->minimumRequirementAmountValue;
    }

    /**
     * Set the value of Minimum Requirement Amount Value
     *
     * @param mixed minimumRequirementAmountValue
     *
     * @return self
     */
    public function setMinimumRequirementAmountValue($minimumRequirementAmountValue)
    {
        $this->minimumRequirementAmountValue = $minimumRequirementAmountValue;

        return $this;
    }

    /**
     * Get the value of Minimum Requirement Quantity Value
     *
     * @return mixed
     */
    public function getMinimumRequirementQuantityValue()
    {
        return $this->minimumRequirementQuantityValue;
    }

    /**
     * Set the value of Minimum Requirement Quantity Value
     *
     * @param mixed minimumRequirementQuantityValue
     *
     * @return self
     */
    public function setMinimumRequirementQuantityValue($minimumRequirementQuantityValue)
    {
        $this->minimumRequirementQuantityValue = $minimumRequirementQuantityValue;

        return $this;
    }


    /**
     * Get the value of Many Discount have Many Products.
     *
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set the value of Many Discount have Many Products.
     *
     * @param mixed products
     *
     * @return self
     */
    public function setProducts($products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @param mixed products
     */
    public function removeProducts(Product $product)
    {
        if (false === $this->products->contains($product)) {
            return;
        }
        $this->products->removeElement($product);
        $product->removeDiscounts($this);
    }

    /**
     * @param mixed products
     */
    public function addProducts(Product $product)
    {
        if (true === $this->products->contains($product)) {
            return;
        }
        $this->products->add($product);
        $product->addDiscounts($this);
    }


    /**
     * Get the value of Many Discount have Many CustomerOrder.
     *
     * @return mixed
     */
    public function getCustomerOrders()
    {
        return $this->customerOrders;
    }

    /**
     * Set the value of Many Discount have Many CustomerOrder.
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

    /**
     * @param mixed customerOrders
     */
    public function removeCustomerOrders(CustomerOrder $customerOrder)
    {
        if (false === $this->customerOrders->contains($customerOrder)) {
            return;
        }
        $this->customerOrders->removeElement($customerOrder);
        $customerOrder->removeDiscounts($this);
    }

    /**
     * @param mixed customerOrders
     */
    public function addCustomerOrders(CustomerOrder $customerOrder)
    {
        if (true === $this->customerOrders->contains($customerOrder)) {
            return;
        }
        $this->customerOrders->add($customerOrder);
        $customerOrder->addDiscounts($this);
    }


    /**
     * Get the value of Only Applies Once Item Per Product
     *
     * @return mixed
     */
    public function getOnlyAppliesOnceItemPerProduct()
    {
        return $this->onlyAppliesOnceItemPerProduct;
    }

    /**
     * Set the value of Only Applies Once Item Per Product
     *
     * @param mixed onlyAppliesOnceItemPerProduct
     *
     * @return self
     */
    public function setOnlyAppliesOnceItemPerProduct($onlyAppliesOnceItemPerProduct)
    {
        $this->onlyAppliesOnceItemPerProduct = $onlyAppliesOnceItemPerProduct;

        return $this;
    }

}
