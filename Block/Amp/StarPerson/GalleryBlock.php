<?php

declare(strict_types = 1);

namespace Block\Amp\StarPerson;


/**
 * AMP блок, отображающий информацию галерею
 */
class GalleryBlock extends \BlockBase
{
    use
        \nt\traits\HasStarPerson;

    /** array */
    private $starPersonGallery = [];

    /**
     * @param array
     * @return $this
     */
    private function initGallery() : self
    {
        if(!$this->starPerson) return $this;

        $tag = new \Tag();
        $tag->id = $this->getStarPerson()->getTagId();
        $this->starPersonGallery = $this->getStarPerson()::getRecommendationByTag($tag);

        return $this;
    }
    /**
     * @return array
     */
    public function getStarPersonGallery() : array
    {
        return $this->starPersonGallery;
    }

    public function init()
    {
        $this->initGallery();
        return parent::init();
    }
}