<?php

declare(strict_types = 1);

namespace Block\Amp\StarPerson;

/**
 * AMP блок, отображающий информацию о персоне
 */
class PublicationListBlock extends \BlockBase
{

  use
    \nt\traits\HasStarPerson;
    

    /** @var array */
    protected $publicationList = [];


    public function init()
    {
        $publicationModel = \Publication::model()
            ->forTag((int) $this->getStarPerson()->getTagId())
            ->recentPublished()
            ->active()
            ->published()
            ->forPublicationsPage();

        $dataProvider = new \ActiveDataProvider($publicationModel);
        $dataProvider->setPagination([
            'currentPage' => $_GET['page'] ?? 0,
            'pageSize'    => 5,
        ]);

        $this->publicationList = $dataProvider->getData();

        return parent::init();
    }


};
