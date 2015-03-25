<?php

namespace AI\Tester\Console;

use DI\Annotation\Inject;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Helper\Helper;

class DocumentManagerHelper extends Helper
{
    /**
     * @Inject("doctrine.documentManager")
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @return string The canonical name
     */
    public function getName()
    {
        return 'documentManager';
    }

    /**
     * @return DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->documentManager;
    }
}