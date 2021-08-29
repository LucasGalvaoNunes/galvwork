<?php


namespace Lucasgnunes\Galvwork\Database;

use PDO;
use PDOException;

/**
 * Class PDOConnection
 * @package Kernel\Database
 * @author Lucas Galvão Nunes <contato@lucasgnunes.dev>
 */
class PDOConnection extends PDO
{
    /**
     * PDOConnection constructor.
     */
    public function __construct()
    {
        try {
            parent::__construct(CONFIG['DB_ENGINE'] . ":host=" . CONFIG['DB_HOST'] . ";dbname=" . CONFIG['DB_NAME'], CONFIG['DB_USER'], CONFIG['DB_PASSWORD']);
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->exec("set names utf8");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}