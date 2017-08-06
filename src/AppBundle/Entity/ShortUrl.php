<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShortUrl
 *
 * @ORM\Table(name="short_urls")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShortUrlRepository")
 */
class ShortUrl
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
     * @var string
     *
     * @ORM\Column(name="short_url", type="string", length=6, unique=true)
     */
    private $shortUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="destination_url", type="text")
     */
    private $destinationUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;


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
     * Set shortUrl
     *
     * @param string $shortUrl
     *
     * @return ShortUrl
     */
    public function setShortUrl($shortUrl)
    {
        $this->shortUrl = $shortUrl;

        return $this;
    }

    /**
     * Get shortUrl
     *
     * @return string
     */
    public function getShortUrl()
    {
        return $this->shortUrl;
    }

    /**
     * Set destinationUrl
     *
     * @param string $destinationUrl
     *
     * @return ShortUrl
     */
    public function setDestinationUrl($destinationUrl)
    {
        $this->destinationUrl = $destinationUrl;

        return $this;
    }

    /**
     * Get destinationUrl
     *
     * @return string
     */
    public function getDestinationUrl()
    {
        return $this->destinationUrl;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ShortUrl
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}

