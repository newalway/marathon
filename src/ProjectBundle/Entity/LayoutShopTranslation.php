<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Table(name="layout_shop_translation")
 * @ORM\Entity
 */
class LayoutShopTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @ORM\Column(name="content", type="text", length=65535, nullable = true)
     */
    private $content;

    /**
     * @ORM\Column(name="link_title", type="string", length=255, nullable = true)
     */
    private $linkTitle;

    /**
     * @ORM\Column(name="link_url", type="string", length=255, nullable = true)
     */
    private $linkUrl;

    /**
     * Get the value of Content
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of Content
     *
     * @param mixed Content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of Link Title
     *
     * @return mixed
     */
    public function getLinkTitle()
    {
        return $this->linkTitle;
    }

    /**
     * Set the value of Link Title
     *
     * @param mixed linkTitle
     *
     * @return self
     */
    public function setLinkTitle($linkTitle)
    {
        $this->linkTitle = $linkTitle;

        return $this;
    }


    /**
     * Get the value of Link Url
     *
     * @return mixed
     */
    public function getLinkUrl()
    {
        return $this->linkUrl;
    }

    /**
     * Set the value of Link Url
     *
     * @param mixed linkUrl
     *
     * @return self
     */
    public function setLinkUrl($linkUrl)
    {
        $this->linkUrl = $linkUrl;

        return $this;
    }

}
