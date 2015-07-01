<?php

namespace Symbio\OrangeGate\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table()
 */
class PostHasMedia
{

    /**
     * @var integer $id
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer $position
     * @ORM\Column(type="integer")
     */
    protected $position;

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="postHasMedias")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", nullable=true)
     */
    protected $post;

    /**
     * @ORM\ManyToOne(targetEntity="Symbio\OrangeGate\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=true)
     */
    protected $media;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return PropertyHasMedia
     */
    public function setPosition($position = null)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set post
     *
     * @param Post $post
     * @return PostHasMedia
     */
    public function setPost(Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get property
     *
     * @return \Symbio\OrangeGate\MediaBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set media
     *
     * @param \Symbio\OrangeGate\MediaBundle\Entity\Media $media
     * @return PropertyHasMedia
     */
    public function setMedia(\Symbio\OrangeGate\MediaBundle\Entity\Media $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \Symbio\OrangeGate\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }
}
