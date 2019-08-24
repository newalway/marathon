<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\EventRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Event
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
    private $position = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status = 1;

    /**
     * @ORM\ManyToOne(targetEntity="EventCategory", inversedBy="events")
     * @ORM\JoinColumn(name="event_category_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    private $eventCategory;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
    private $updatedAt;

    /** @ORM\Column(name="created_at", type="datetime") */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $video;
    /**
    * @ORM\Column(name="video_id", type="string", length=255, nullable = true)
    */
    private $videoId;
    /**
     * @ORM\Column(name="video_width", type="smallint", nullable = true)
     */
    private $videoWidth;
    /**
     * @ORM\Column(name="video_height", type="smallint", nullable = true)
     */
    private $videoHeight;
    /**
     * @ORM\Column(name="video_duration", type="smallint", nullable = true)
     */
    private $videoDuration;
    /**
    * @ORM\Column(name="thumbnail_url", type="string", length=255, nullable = true)
    */
    private $thumbnailUrl;
    /**
     * @ORM\Column(name="thumbnail_width", type="smallint", nullable = true)
     */
    private $thumbnailWidth;
    /**
    * @ORM\Column(name="thumbnail_height", type="smallint", nullable = true)
    */
    private $thumbnailHeight;
    /**
    * @ORM\Column(name="thumbnail_url_play_button", type="string", length=255, nullable = true)
    */
    private $thumbnailUrlPlayButton;
    /**
    * @ORM\Column(name="video_embed", type="text", length=65535, nullable = true)
    */
    private $videoEmbed;

    /**
     * @ORM\Column(name="public_date", type="date", nullable=true)
    */
    private $publicDate;


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
     * Get the value of Event Category
     *
     * @return mixed
     */
    public function getEventCategory()
    {
        return $this->eventCategory;
    }

    /**
     * Set the value of Event Category
     *
     * @param mixed eventCategory
     *
     * @return self
     */
    public function setEventCategory($eventCategory)
    {
        $this->eventCategory = $eventCategory;

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
     * Get the value of Video
     *
     * @return mixed
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set the value of Video
     *
     * @param mixed video
     *
     * @return self
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get the value of Video Id
     *
     * @return mixed
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * Set the value of Video Id
     *
     * @param mixed videoId
     *
     * @return self
     */
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;

        return $this;
    }

    /**
     * Get the value of Video Width
     *
     * @return mixed
     */
    public function getVideoWidth()
    {
        return $this->videoWidth;
    }

    /**
     * Set the value of Video Width
     *
     * @param mixed videoWidth
     *
     * @return self
     */
    public function setVideoWidth($videoWidth)
    {
        $this->videoWidth = $videoWidth;

        return $this;
    }

    /**
     * Get the value of Video Height
     *
     * @return mixed
     */
    public function getVideoHeight()
    {
        return $this->videoHeight;
    }

    /**
     * Set the value of Video Height
     *
     * @param mixed videoHeight
     *
     * @return self
     */
    public function setVideoHeight($videoHeight)
    {
        $this->videoHeight = $videoHeight;

        return $this;
    }

    /**
     * Get the value of Video Duration
     *
     * @return mixed
     */
    public function getVideoDuration()
    {
        return $this->videoDuration;
    }

    /**
     * Set the value of Video Duration
     *
     * @param mixed videoDuration
     *
     * @return self
     */
    public function setVideoDuration($videoDuration)
    {
        $this->videoDuration = $videoDuration;

        return $this;
    }

    /**
     * Get the value of Thumbnail Url
     *
     * @return mixed
     */
    public function getThumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

    /**
     * Set the value of Thumbnail Url
     *
     * @param mixed thumbnailUrl
     *
     * @return self
     */
    public function setThumbnailUrl($thumbnailUrl)
    {
        $this->thumbnailUrl = $thumbnailUrl;

        return $this;
    }

    /**
     * Get the value of Thumbnail Width
     *
     * @return mixed
     */
    public function getThumbnailWidth()
    {
        return $this->thumbnailWidth;
    }

    /**
     * Set the value of Thumbnail Width
     *
     * @param mixed thumbnailWidth
     *
     * @return self
     */
    public function setThumbnailWidth($thumbnailWidth)
    {
        $this->thumbnailWidth = $thumbnailWidth;

        return $this;
    }

    /**
     * Get the value of Thumbnail Height
     *
     * @return mixed
     */
    public function getThumbnailHeight()
    {
        return $this->thumbnailHeight;
    }

    /**
     * Set the value of Thumbnail Height
     *
     * @param mixed thumbnailHeight
     *
     * @return self
     */
    public function setThumbnailHeight($thumbnailHeight)
    {
        $this->thumbnailHeight = $thumbnailHeight;

        return $this;
    }

    /**
     * Get the value of Thumbnail Url Play Button
     *
     * @return mixed
     */
    public function getThumbnailUrlPlayButton()
    {
        return $this->thumbnailUrlPlayButton;
    }

    /**
     * Set the value of Thumbnail Url Play Button
     *
     * @param mixed thumbnailUrlPlayButton
     *
     * @return self
     */
    public function setThumbnailUrlPlayButton($thumbnailUrlPlayButton)
    {
        $this->thumbnailUrlPlayButton = $thumbnailUrlPlayButton;

        return $this;
    }

    /**
     * Get the value of Video Embed
     *
     * @return mixed
     */
    public function getVideoEmbed()
    {
        return $this->videoEmbed;
    }

    /**
     * Set the value of Video Embed
     *
     * @param mixed videoEmbed
     *
     * @return self
     */
    public function setVideoEmbed($videoEmbed)
    {
        $this->videoEmbed = $videoEmbed;

        return $this;
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

}
