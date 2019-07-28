<?php
declare(strict_types = 1);

namespace nt\traits;


trait HasExpertMessage
{
    private $expertMessage = null;

    /**
     * @return Message | null
     */
    public function getExpertMessage()
    {
        return $this->expertMessage;
    }

    /**
     * @param null $expertMessage
     */
    public function setExpertMessage(\nt\Forum\Message $expertMessage)
    {
        $this->expertMessage = $expertMessage;
    }


}