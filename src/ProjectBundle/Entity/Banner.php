<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Banner
 *
 * @ORM\Table(name="banner")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\BannerRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Banner
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
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $image_mobile;

    /**
     * @ORM\Column(name="public_date", type="date", nullable=true)
    */
    private $publicDate;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status = 1;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
    private $updatedAt;

    /** @ORM\Column(name="created_at", type="datetime") */
    private $createdAt;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position = 0;

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

    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }



    /**
     * Get the value of Id
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
	}

    /**
     * Get the value of Image Mobile
     *
     * @return mixed
     */
    public function getImageMobile()
    {
        return $this->image_mobile;
    }

    /**
     * Set the value of Image Mobile
     *
     * @param mixed image_mobile
     *
     * @return self
     */
    public function setImageMobile($image_mobile)
    {
        $this->image_mobile = $image_mobile;

        return $this;
    }
    public function removeImageMobile()
	{
		$this->setImageMobile(null);
	}

    /**
     * Get the value of Public Date
     *
     * @return mixed
     */
    public function getPublicDate()
    {
        return $this->publicDate;
    }

    /**
     * Set the value of Public Date
     *
     * @param mixed publicDate
     *
     * @return self
     */
    public function setPublicDate($publicDate)
    {
        $this->publicDate = $publicDate;

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
     * Get the value of Position
     *
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of Position
     *
     * @param mixed position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }



}
