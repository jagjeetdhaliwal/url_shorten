<?php

// src/AppBundle/Entity/Url.php
namespace AppBundle\Entity;

class Url
{
    protected $url;

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }
}
