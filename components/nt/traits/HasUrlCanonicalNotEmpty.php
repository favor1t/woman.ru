<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "что-то с непустым канониклом"
 * Class HasUrlCanonicalNotEmpty
 */
trait HasUrlCanonicalNotEmpty
{

    /** @var string | null $urlCanonical */
    private $urlCanonical = null;


    /**
     * @param string $urlCanonical
     * @return $this
     * @throws \Exception
     */
    public function setUrlCanonical(string $urlCanonical) : self
    {
        if($urlCanonical == '') throw new \Exception('UrlCanonical is empty');

        $this->urlCanonical = $urlCanonical;
        return $this;
    }
    /**
     * @return string
     */
    public function getUrlCanonical() : string
    {
        // грустно, но это наиболее частая реализация
        return $this->urlCanonical ?? \UrlHelper::getUrlCanonical();
    }


};

