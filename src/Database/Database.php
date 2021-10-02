<?php
/**
 * Filename: Database.php
 * User: lucas
 * Date: 27/09/2021
 * Time: 20:35
 */

namespace Lucasgnunes\Galvwork\Database;

use Closure;
use PDOException;
use Throwable;

class Database
{
    private static $instance = null;

    private $transactions = 0;

    /**
     * @var PDOConnection
     */
    private $pdo;

    private function __construct()
    {
        $this->pdo = new PDOConnection();
    }

    public static function getInstance(): Database
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function getPDO(): PDOConnection
    {
        return $this->pdo;
    }

    /**
     * @param  Closure  $callback
     * @param  int  $attempts
     * @return mixed|void
     * @throws Throwable
     */
    public function transaction(Closure $callback, int $attempts = 1)
    {
        for ($currentAttempt = 1; $currentAttempt <= $attempts; $currentAttempt++) {
            $this->beginTransaction();

            try {
                $callbackResult = $callback($this);
            }
            catch (Throwable $e) {
                $this->handleTransactionException(
                    $e, $currentAttempt, $attempts
                );

                continue;
            }

            try {
                if ($this->transactions == 1) {
                    $this->getPdo()->commit();
                }

                $this->transactions = max(0, $this->transactions - 1);
            } catch (Throwable $e) {
                $this->handleCommitTransactionException(
                    $e, $currentAttempt, $attempts
                );

                continue;
            }

            return $callbackResult;
        }
    }

    /**
     * @throws Throwable
     */
    public function beginTransaction()
    {
        $this->createTransaction();

        $this->transactions++;
    }

    public function commit()
    {
        if ($this->transactions == 1) {
            $this->getPdo()->commit();
        }

        $this->transactions = max(0, $this->transactions - 1);
    }

    /**
     * @throws Throwable
     */
    public function rollBack()
    {
        $toLevel = $this->transactions - 1;

        if ($toLevel < 0) {
            return;
        }

        try {
            $this->performRollBack($toLevel);
        } catch (Throwable $e) {
            $this->handleRollBackException($e);
        }

        $this->transactions = $toLevel;
    }

    /**
     * @throws Throwable
     */
    private function createTransaction()
    {
        if ($this->transactions == 0) {
            try {
                $this->pdo->beginTransaction();
            } catch (Throwable $e) {
                $this->handleBeginTransactionException($e);
            }
        }
    }

    /**
     * @param  int  $toLevel
     */
    private function performRollBack(int $toLevel)
    {
        if ($toLevel == 0) {
            $this->getPdo()->rollBack();
        }
    }

    /**
     * @param  Throwable  $e
     * @throws Throwable
     */
    private function handleBeginTransactionException(Throwable $e)
    {
        if ($this->causedByLostConnection($e)) {
            $this->pdo->beginTransaction();
        } else {
            throw $e;
        }
    }

    /**
     * @param  Throwable  $e
     * @param $currentAttempt
     * @param $maxAttempts
     * @throws Throwable
     */
    private function handleTransactionException(Throwable $e, $currentAttempt, $maxAttempts)
    {
        if ($this->causedByConcurrencyError($e) &&
            $this->transactions > 1) {
            $this->transactions--;

            throw $e;
        }

        $this->rollBack();

        if ($this->causedByConcurrencyError($e) &&
            $currentAttempt < $maxAttempts) {
            return;
        }

        throw $e;
    }

    /**
     * @param  Throwable  $e
     * @throws Throwable
     */
    private function handleRollBackException(Throwable $e)
    {
        if ($this->causedByLostConnection($e)) {
            $this->transactions = 0;
        }

        throw $e;
    }

    /**
     * @param  Throwable  $e
     * @return bool
     */
    private function causedByConcurrencyError(Throwable $e): bool
    {
        if ($e instanceof PDOException && $e->getCode() === '40001') {
            return true;
        }

        $message = $e->getMessage();

        return $this->strContains($message, [
            'Deadlock found when trying to get lock',
            'deadlock detected',
            'The database file is locked',
            'database is locked',
            'database table is locked',
            'A table in the database is locked',
            'has been chosen as the deadlock victim',
            'Lock wait timeout exceeded; try restarting transaction',
            'WSREP detected deadlock/conflict and aborted the transaction. Try restarting the transaction',
        ]);
    }

    /**
     * @param  Throwable  $e
     * @return bool
     */
    private function causedByLostConnection(Throwable $e): bool
    {
        $message = $e->getMessage();

        return $this->strContains($message, [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
            'Transaction() on null',
            'child connection forced to terminate due to client_idle_limit',
            'query_wait_timeout',
            'reset by peer',
            'Physical connection is not usable',
            'TCP Provider: Error code 0x68',
            'ORA-03114',
            'Packets out of order. Expected',
            'Adaptive Server connection failed',
            'Communication link failure',
            'connection is no longer usable',
            'Login timeout expired',
            'SQLSTATE[HY000] [2002] Connection refused',
            'running with the --read-only option so it cannot execute this statement',
            'The connection is broken and recovery is not possible. The connection is marked by the client driver as unrecoverable. No attempt was made to restore the connection.',
            'SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo failed: Try again',
            'SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo failed: Name or service not known',
            'SQLSTATE[HY000]: General error: 7 SSL SYSCALL error: EOF detected',
            'SQLSTATE[HY000] [2002] Connection timed out',
            'SSL: Connection timed out',
            'SQLSTATE[HY000]: General error: 1105 The last transaction was aborted due to Seamless Scaling. Please retry.',
        ]);
    }

    /**
     * @param $haystack
     * @param $needles
     * @return bool
     */
    private static function strContains($haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  Throwable  $e
     * @param $currentAttempt
     * @param $maxAttempts
     * @throws Throwable
     */
    private function handleCommitTransactionException(Throwable $e, $currentAttempt, $maxAttempts)
    {
        $this->transactions = max(0, $this->transactions - 1);

        if ($this->causedByConcurrencyError($e) &&
            $currentAttempt < $maxAttempts) {
            return;
        }

        if ($this->causedByLostConnection($e)) {
            $this->transactions = 0;
        }

        throw $e;
    }
}