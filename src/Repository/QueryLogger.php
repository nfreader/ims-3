<?php

namespace App\Repository;

use App\Factory\LoggerFactory;
use Firehed\DbalLogger\QueryLogger as DbalLoggerQueryLogger;
use Monolog\Logger;

class QueryLogger implements DbalLoggerQueryLogger
{
    private Logger $logger;

    public function __construct(
        private LoggerFactory $loggerFactory,
        private string $requestId,
    ) {
        $logger = $this->loggerFactory
            ->addFileHandler('doctrine_db.log')
            ->createLogger('doctrine_db_log');
        $this->logger = $logger;
    }

    public function startQuery($sql, ?array $params = null, ?array $types = null)
    {
        $this->logger->info($sql, [
            'params' => $params,
            'types' => $types,
            'request' => $this->requestId,
            'trace' => array_slice(debug_backtrace(3, 7), 4)
        ]);
    }

    public function stopQuery()
    {

    }

}
