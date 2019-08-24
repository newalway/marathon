<?php

namespace ProjectBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * ProductCategory
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="product_category")
 * use repository for handy tree functions
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\ProductCategoryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ProductCategory
{
    use ORMBehaviors\Translatable\Translatable,
        ORMBehaviors\Sluggable\Sluggable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="ProductCategory")
     * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="ProductCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="ProductCategory", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
	* @ORM\Column(name="image", type="string", length=255, nullable = true)
	*/
	private $image;

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
	* @ORM\Column(name="is_highlight", type="boolean", options={"default":0})
	*/
	private $isHighlight = false;

    /**
     * Many ProductCategory have Many Products.
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="productCategories")
     */
    private $products;

    /**
	* Many ProductCategory have Many TemplateCustomerGroup.
	* @ORM\ManyToMany(targetEntity="TemplateCustomerGroup", inversedBy="productCategorys")
	* @ORM\JoinTable(name="template_customer_groups_product_categories")
	*/
	private $templateCustomerGroups;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->templateCustomerGroups = new ArrayCollection();
    }

    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    public function getSluggableFields()
    {
        return ['titleslug'];
    }

    //method for getSluggableFields
    public function getTitleslug()
    {
        // get english title for slug generation
        return $this->translate('en')->getTitle();
        // get the title translation in current locale
        // return $this->translate(null,true)->getTitle();
    }

    // don't need override this function
    // public function generateSlugValue($values)
    // {
    //     $text = str_replace(' ', '-', $values);
    //     $text = implode('-', $text);
    //     $text = strtolower($text);
    //     return $text;
    // }
    // override
    // public function generateSlug()
    // {
    //     $localTranslation = $this->getLocale();
    //     $categorySlug = $this->getTranslatable()->getCategory()->translate($localTranslation)->getSlug();
    //     $this->slug = $categorySlug.'/'.$urlized;
    // }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function setParent(ProductCategory $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }


    /**
     * Get the value of Lft
     *
     * @return mixed
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set the value of Lft
     *
     * @param mixed lft
     *
     * @return self
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get the value of Lvl
     *
     * @return mixed
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set the value of Lvl
     *
     * @param mixed lvl
     *
     * @return self
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get the value of Rgt
     *
     * @return mixed
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set the value of Rgt
     *
     * @param mixed rgt
     *
     * @return self
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Set the value of Root
     *
     * @param mixed root
     *
     * @return self
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get the value of Children
     *
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set the value of Children
     *
     * @param mixed children
     *
     * @return self
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
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

    public function removeImage()
	{
		$this->setImage(null);
		$this->setImageSmall(null);
		$this->setImageMedium(null);
		$this->setImageLarge(null);
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
     * Get the value of Is Highlight
     *
     * @return mixed
     */
    public function getIsHighlight()
    {
        return $this->isHighlight;
    }

    /**
     * Set the value of Is Highlight
     *
     * @param mixed isHighlight
     *
     * @return self
     */
    public function setIsHighlight($isHighlight)
    {
        $this->isHighlight = $isHighlight;

        return $this;
    }

    /**
     * Get the value of Many ProductCategory have Many Products.
     *
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set the value of Many ProductCategory have Many Products.
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
        $product->removeProductCategories($this);
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
        $product->addProductCategories($this);
    }

    /**
     * Get the value of Many ProductCategory have Many TemplateCustomerGroup.
     *
     * @return mixed
     */
    public function getTemplateCustomerGroups()
    {
        return $this->templateCustomerGroups;
    }

    /**
     * Set the value of Many ProductCategory have Many TemplateCustomerGroup.
     *
     * @param mixed templateCustomerGroups
     *
     * @return self
     */
    public function setTemplateCustomerGroups($templateCustomerGroups)
    {
        $this->templateCustomerGroups = $templateCustomerGroups;

        return $this;
    }

    /**
     * @param mixed templateCustomerGroups
     */
    public function removeTemplateCustomerGroups(TemplateCustomerGroup $templateCustomerGroup)
    {
        if (false === $this->templateCustomerGroups->contains($templateCustomerGroup)) {
            return;
        }
        $this->templateCustomerGroups->removeElement($templateCustomerGroup);
        $templateCustomerGroup->removeProductCategorys($this);
    }

    /**
     * @param mixed templateCustomerGroups
     */
    public function addTemplateCustomerGroups(TemplateCustomerGroup $templateCustomerGroup)
    {
        if (true === $this->templateCustomerGroups->contains($templateCustomerGroup)) {
            return;
        }
        $this->templateCustomerGroups->add($templateCustomerGroup);
        $templateCustomerGroup->addProductCategorys($this);
    }

}
