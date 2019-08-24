<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * LayoutShop
 *
 * @ORM\Table(name="layout_shop")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\LayoutShopRepository")
 * @ORM\HasLifecycleCallbacks
 */
class LayoutShop
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
     * @ORM\Column(type="smallint")
     */
    private $status = 1;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position = 0;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
    private $updatedAt;

    /** @ORM\Column(name="created_at", type="datetime") */
    private $createdAt;

    /**
     * @ORM\Column(name="publish_date", type="date", nullable=true)
    */
    private $publishDate;

    /**
     * @ORM\Column(name="content_position", type="string", length=1, nullable=true)
     */
    private $contentPosition = 'R';

    /**
     * @ORM\Column(name="button_position", type="string", length=1, nullable=true)
     */
    private $buttonPosition = 'L';

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
     * Get the value of Publish Date
     *
     * @return mixed
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    /**
     * Set the value of Publish Date
     *
     * @param mixed publishDate
     *
     * @return self
     */
    public function setPublishDate($publishDate)
    {
        $this->publishDate = $publishDate;

        return $this;
    }


    /**
     * Get the value of Content Position
     *
     * @return mixed
     */
    public function getContentPosition()
    {
        return $this->contentPosition;
    }

    /**
     * Set the value of Content Position
     *
     * @param mixed contentPosition
     *
     * @return self
     */
    public function setContentPosition($contentPosition)
    {
        $this->contentPosition = $contentPosition;

        return $this;
    }

    /**
     * Get the value of Button Position
     *
     * @return mixed
     */
    public function getButtonPosition()
    {
        return $this->buttonPosition;
    }

    /**
     * Set the value of Button Position
     *
     * @param mixed buttonPosition
     *
     * @return self
     */
    public function setButtonPosition($buttonPosition)
    {
        $this->buttonPosition = $buttonPosition;

        return $this;
    }

}
