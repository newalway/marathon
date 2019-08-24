<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerOrderItem
 *
 * @ORM\Table(name="customer_order_item")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\CustomerOrderItemRepository")
 */
class CustomerOrderItem
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
     * @ORM\ManyToOne(targetEntity="CustomerOrder", inversedBy="customerOrderItems")
     * @ORM\JoinColumn(name="customer_order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $customerOrder;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="customerOrderItems")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="Sku", inversedBy="customerOrderItems")
     * @ORM\JoinColumn(name="sku_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $sku;

    /**
     * @ORM\Column(name="product_title", type="string", length=255)
     */
    private $productTitle;

    /**
	 * @ORM\Column(name="price", type="decimal", scale=2, nullable = true)
	 */
	private $price;

    /**
	 * @ORM\Column(name="compare_at_price", type="decimal", scale=2, nullable = true)
	 */
	private $compareAtPrice;

    /**
	 * @ORM\Column(name="discount", type="decimal", scale=2, nullable = true)
	 */
	private $discount;

    /**
	 * @ORM\Column(name="quantity", type="integer", nullable = true)
	 */
	private $quantity;

    /**
	 * @ORM\Column(name="amount", type="decimal", scale=2, nullable = true)
	 */
	private $amount;

    /**
     * @ORM\Column(name="image", type="string", length=255, nullable = true)
     */
    private $image;

    /**
     * @ORM\Column(name="inventory_policy_status", type="smallint", options={"unsigned":true, "default":0})
     */
    private $inventoryPolicyStatus = 0;

    /**
     * @ORM\Column(name="sku_title", type="array")
    */
    private $skuTitle;

    /**
     * @ORM\Column(name="sku_value", type="string", length=255, nullable = true)
     */
    private $skuValue;

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
     * Get the value of Product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set the value of Product
     *
     * @param mixed product
     *
     * @return self
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get the value of Sku
     *
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set the value of Sku
     *
     * @param mixed sku
     *
     * @return self
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

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
     * Get the value of Price
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of Price
     *
     * @param mixed price
     *
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of Compare At Price
     *
     * @return mixed
     */
    public function getCompareAtPrice()
    {
        return $this->compareAtPrice;
    }

    /**
     * Set the value of Compare At Price
     *
     * @param mixed compareAtPrice
     *
     * @return self
     */
    public function setCompareAtPrice($compareAtPrice)
    {
        $this->compareAtPrice = $compareAtPrice;

        return $this;
    }

    /**
     * Get the value of Discount
     *
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set the value of Discount
     *
     * @param mixed discount
     *
     * @return self
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get the value of Quantity
     *
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set the value of Quantity
     *
     * @param mixed quantity
     *
     * @return self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

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
     * Get the value of Image
     *
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of Image
     *
     * @param mixed image
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }


    /**
     * Get the value of Inventory Policy Status
     *
     * @return mixed
     */
    public function getInventoryPolicyStatus()
    {
        return $this->inventoryPolicyStatus;
    }

    /**
     * Set the value of Inventory Policy Status
     *
     * @param mixed inventoryPolicyStatus
     *
     * @return self
     */
    public function setInventoryPolicyStatus($inventoryPolicyStatus)
    {
        $this->inventoryPolicyStatus = $inventoryPolicyStatus;

        return $this;
    }

    /**
     * Get the value of Sku Title
     *
     * @return mixed
     */
    public function getSkuTitle()
    {
        return $this->skuTitle;
    }

    /**
     * Set the value of Sku Title
     *
     * @param mixed skuTitle
     *
     * @return self
     */
    public function setSkuTitle($skuTitle)
    {
        $this->skuTitle = $skuTitle;

        foreach ($skuTitle as $sku_title) {
            $this->addSkuTitle($sku_title);
        }

        return $this;
    }

    public function addSkuTitle($sku_title)
    {
        if (!in_array($sku_title, $this->skuTitle, true)) {
            $this->skuTitle[] = $sku_title;
        }

        return $this;
    }


    /**
     * Get the value of Sku Value
     *
     * @return mixed
     */
    public function getSkuValue()
    {
        return $this->skuValue;
    }

    /**
     * Set the value of Sku Value
     *
     * @param mixed skuValue
     *
     * @return self
     */
    public function setSkuValue($skuValue)
    {
        $this->skuValue = $skuValue;

        return $this;
    }

}
