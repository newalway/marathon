<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sku
 *
 * @ORM\Table(name="sku", indexes={@ORM\Index(name="search_idx", columns={"sku"})})
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\SkuRepository")
 */
class Sku
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
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="skus")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $product;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $sku;

	/**
	 * @ORM\Column(type="decimal", scale=2, nullable = true)
	 */
	private $price;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true, "default":1})
     */
    private $status = 1;

    /**
	 * @ORM\Column(name="compare_at_price", type="decimal", scale=2, nullable = true)
	 */
	private $compareAtPrice;

    /**
     * @ORM\Column(name="inventory_policy_status", type="smallint", options={"unsigned":true, "default":0})
     */
    private $inventoryPolicyStatus = 0;

    /**
     * @ORM\Column(name="inventory_quantity", type="integer", nullable = true)
     */
    private $inventoryQuantity;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable = true)
     */
    private $grams;

	/**
     * @ORM\Column(type="decimal", scale=2, nullable = true)
     */
    private $weight;

	/**
     * @ORM\Column(name="weight_unit", type="string", length=45, nullable = true)
     */
    private $weightUnit;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $image;

    /**
     * @ORM\Column(name="default_option", type="boolean", options={"default":0})
     */
    private $defaultOption = false;

    /**
     * @ORM\OneToMany(targetEntity="SkuValue", mappedBy="sku")
     */
    private $skuValues;

    /**
     * @ORM\Column(name="image_small", type="string", length=255, nullable = true)
     */
    private $imageSmall;

	/**
     * @ORM\Column(name="image_medium", type="string", length=255, nullable = true)
     */
    private $imageMedium;

	/**
     * @ORM\Column(name="image_large", type="string", length=255, nullable = true)
     */
    private $imageLarge;

    /**
     * @ORM\OneToMany(targetEntity="CustomerOrderItem", mappedBy="sku")
     */
    private $customerOrderItems;

    public function __construct()
    {
      $this->skuValues = new ArrayCollection();
      $this->customerOrderItems = new ArrayCollection();
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
     * Get the value of Sku Values
     *
     * @return mixed
     */
    public function getSkuValues()
    {
        return $this->skuValues;
    }

    /**
     * Set the value of Sku Values
     *
     * @param mixed skuValues
     *
     * @return self
     */
    public function setSkuValues($skuValues)
    {
        $this->skuValues = $skuValues;

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
     * Get the value of Inventory Quantity
     *
     * @return mixed
     */
    public function getInventoryQuantity()
    {
        return $this->inventoryQuantity;
    }

    /**
     * Set the value of Inventory Quantity
     *
     * @param mixed inventoryQuantity
     *
     * @return self
     */
    public function setInventoryQuantity($inventoryQuantity)
    {
        $this->inventoryQuantity = $inventoryQuantity;

        return $this;
    }

    /**
     * Get the value of Grams
     *
     * @return mixed
     */
    public function getGrams()
    {
        return $this->grams;
    }

    /**
     * Set the value of Grams
     *
     * @param mixed grams
     *
     * @return self
     */
    public function setGrams($grams)
    {
        $this->grams = $grams;

        return $this;
    }

    /**
     * Get the value of Weight
     *
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set the value of Weight
     *
     * @param mixed weight
     *
     * @return self
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get the value of Weight Unit
     *
     * @return mixed
     */
    public function getWeightUnit()
    {
        return $this->weightUnit;
    }

    /**
     * Set the value of Weight Unit
     *
     * @param mixed weightUnit
     *
     * @return self
     */
    public function setWeightUnit($weightUnit)
    {
        $this->weightUnit = $weightUnit;

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

    public function removeImage()
	{
		$this->setImage(null);
        $this->setImageSmall(null);
		$this->setImageMedium(null);
		$this->setImageLarge(null);
	}

    /**
     * Get the value of Default Option
     *
     * @return mixed
     */
    public function getDefaultOption()
    {
        return $this->defaultOption;
    }

    /**
     * Set the value of Default Option
     *
     * @param mixed defaultOption
     *
     * @return self
     */
    public function setDefaultOption($defaultOption)
    {
        $this->defaultOption = $defaultOption;

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
     * Get the value of Image Small
     *
     * @return mixed
     */
    public function getImageSmall()
    {
        return $this->imageSmall;
    }

    /**
     * Set the value of Image Small
     *
     * @param mixed imageSmall
     *
     * @return self
     */
    public function setImageSmall($imageSmall)
    {
        $this->imageSmall = $imageSmall;

        return $this;
    }

    /**
     * Get the value of Image Medium
     *
     * @return mixed
     */
    public function getImageMedium()
    {
        return $this->imageMedium;
    }

    /**
     * Set the value of Image Medium
     *
     * @param mixed imageMedium
     *
     * @return self
     */
    public function setImageMedium($imageMedium)
    {
        $this->imageMedium = $imageMedium;

        return $this;
    }

    /**
     * Get the value of Image Large
     *
     * @return mixed
     */
    public function getImageLarge()
    {
        return $this->imageLarge;
    }

    /**
     * Set the value of Image Large
     *
     * @param mixed imageLarge
     *
     * @return self
     */
    public function setImageLarge($imageLarge)
    {
        $this->imageLarge = $imageLarge;

        return $this;
    }


    /**
     * Get the value of Customer Order Items
     *
     * @return mixed
     */
    public function getCustomerOrderItems()
    {
        return $this->customerOrderItems;
    }

    /**
     * Set the value of Customer Order Items
     *
     * @param mixed customerOrderItems
     *
     * @return self
     */
    public function setCustomerOrderItems($customerOrderItems)
    {
        $this->customerOrderItems = $customerOrderItems;

        return $this;
    }

}
