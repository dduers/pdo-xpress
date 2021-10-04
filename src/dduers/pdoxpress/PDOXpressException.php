<?php
declare(strict_types=1);
namespace Dduers\PDOXpress;

use Throwable;

/**
 * pdo-xpress exceptions
 */
class PDOXpressException extends \Exception
{
    public const CANNOT_UPDATE_TABLE_WITHOUT_PRIMARY_KEY = 'CANNOT_UPDATE_TABLE_WITHOUT_PRIMARY_KEY';
    public const CANNOT_DELETE_RECORD_FROM_TABLE_WITHOUT_PRIMARY_KEY = 'CANNOT_DELETE_RECORD_FROM_TABLE_WITHOUT_PRIMARY_KEY';
    public const DATABASE_DRIVER_NOT_SUPPORTED_BY_PDOXPRESS = 'DATABASE_DRIVER_NOT_SUPPORTED_BY_PDOXPRESS';

    function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        throw new parent($message, $code, $previous);
    }
}
