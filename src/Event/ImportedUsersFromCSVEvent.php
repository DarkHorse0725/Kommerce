<?php
namespace inklabs\kommerce\Event;

use inklabs\kommerce\Lib\Event\EventInterface;
use inklabs\kommerce\Service\Import\ImportResult;

class ImportedUsersFromCSVEvent implements EventInterface
{
    /** @var int */
    private $successCount;

    /** @var int */
    private $failedCount;

    public function __construct($successCount, $failedCount)
    {
        $this->successCount = $successCount;
        $this->failedCount = $failedCount;
    }

    public static function createFromImportResult(ImportResult $importResult)
    {
        return new self(
            $importResult->getSuccessCount(),
            $importResult->getFailedCount()
        );
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getFailedCount()
    {
        return $this->failedCount;
    }
}
