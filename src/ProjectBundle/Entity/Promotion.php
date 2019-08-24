<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Promotion
 *
 * @ORM\Table(name="promotion")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\PromotionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Promotion
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\Column(type="string", length=255, nullable = true)
    */
    private $filepath;

    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $filename;

    /**
    * @ORM\Column(type="decimal", scale=1, nullable=true)
    */
    private $filesize;

    /**
    * @ORM\Column(name="download_count", type="integer")
    */
    private $downloadCount = 0;

    /**
    * @ORM\Column(name="start_date", type="date", nullable=true)
    */
    private $startDate;

    /**
     * @ORM\Column(name="is_end_date", type="boolean")
     */
    private $isEndDate = false;

    /** @ORM\Column(name="end_date", type="date", nullable = true) */
    private $endDate;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status = 1;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
	private $updatedAt;

    /** @ORM\Column(name="created_at", type="datetime") */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="PromotionDownloadCounter", mappedBy="promotion")
     */
    private $promotionDownloadCounter;

    /**
     * Many Promotions have Many Products.
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="promotions")
     */
    private $products;

    public function __construct()
    {
        $this->promotionDownloadCounter = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
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
     * Get the value of Filepath
     *
     * @return mixed
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * Set the value of Filepath
     *
     * @param mixed filepath
     *
     * @return self
     */
    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;

        return $this;
    }

    /**
     * Get the value of Filename
     *
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the value of Filename
     *
     * @param mixed filename
     *
     * @return self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get the value of Filesize
     *
     * @return mixed
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * Set the value of Filesize
     *
     * @param mixed filesize
     *
     * @return self
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;

        return $this;
    }

    /**
     * Get the value of Download Count
     *
     * @return mixed
     */
    public function getDownloadCount()
    {
        return $this->downloadCount;
    }

    /**
     * Set the value of Download Count
     *
     * @param mixed downloadCount
     *
     * @return self
     */
    public function setDownloadCount($downloadCount)
    {
        $this->downloadCount = $downloadCount;

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
     * Get the value of Promotion Download Counter
     *
     * @return mixed
     */
    public function getPromotionDownloadCounter()
    {
        return $this->promotionDownloadCounter;
    }

    /**
    * Set the value of Promotion Download Counter
    *
    * @param mixed promotionDownloadCounter
    *
    * @return self
    */
    public function setPromotionDownloadCounter($promotionDownloadCounter)
    {
        $this->promotionDownloadCounter = $promotionDownloadCounter;

        return $this;
    }

    public function removeFilepath()
    {
    	$this->setFilepath(null);
        $this->setFilename(null);
        $this->setFilesize(null);
    }


    /**
     * Get the value of Many Promotions have Many Products.
     *
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set the value of Many Promotions have Many Products.
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
        $product->removePromotions($this);
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
        $product->addPromotions($this);
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

}
