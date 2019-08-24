<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SkuValue
 *
 * @ORM\Table(name="sku_value")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\SkuValueRepository")
 */
class SkuValue
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
     * @ORM\ManyToOne(targetEntity="Variant", inversedBy="skuValues")
     * @ORM\JoinColumn(name="variant_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $variant;

    /**
     * @ORM\ManyToOne(targetEntity="VariantOption", inversedBy="skuValues")
     * @ORM\JoinColumn(name="variant_option_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $variantOption;

    /**
     * @ORM\ManyToOne(targetEntity="Sku", inversedBy="skuValues")
     * @ORM\JoinColumn(name="sku_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $sku;

    /**
     * @ORM\Column(name="product_id", type="integer", nullable = true)
     */
	private $productId;

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
     * Get the value of Variant
     *
     * @return mixed
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * Set the value of Variant
     *
     * @param mixed variant
     *
     * @return self
     */
    public function setVariant($variant)
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Get the value of Variant Option
     *
     * @return mixed
     */
    public function getVariantOption()
    {
        return $this->variantOption;
    }

    /**
     * Set the value of Variant Option
     *
     * @param mixed variantOption
     *
     * @return self
     */
    public function setVariantOption($variantOption)
    {
        $this->variantOption = $variantOption;

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
     * Get the value of Product Id
     *
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set the value of Product Id
     *
     * @param mixed productId
     *
     * @return self
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

}
