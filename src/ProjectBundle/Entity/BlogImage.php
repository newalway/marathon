<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlogImage
 *
 * @ORM\Table(name="blog_image")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\BlogImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class BlogImage
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
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $image;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
	  private $updatedAt;

		/** @ORM\Column(name="created_at", type="datetime") */
	  private $createdAt;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position = 0;

    /**
	   * @ORM\ManyToOne(targetEntity="Blog", inversedBy="blogImages")
	   * @ORM\JoinColumn(name="blog_id", referencedColumnName="id", onDelete="CASCADE")
	   */
	  private $blog;

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

    /**
     * Get the value of Blog
     *
     * @return mixed
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * Set the value of Blog
     *
     * @param mixed blog
     *
     * @return self
     */
    public function setBlog($blog)
    {
        $this->blog = $blog;

        return $this;
    }

}
