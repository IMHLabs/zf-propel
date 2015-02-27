<?php
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/PhpUnit/PropelTest
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
    
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/PhpUnit/PropelTest
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
namespace ImhPropel\PhpUnit\Sqlite;

use PHPUnit_Framework_TestCase;

class PropelTest extends \PHPUnit_Framework_TestCase
{
    protected $connection;
    
    protected $tables;
    
    public function loadFixtureData($file)
    {
        $fixturePath = realpath($file);
        $this->resetDatabase();
        if ($fixturePath) {
            $xml = simplexml_load_file($fixturePath);
            $tables = (array) $xml;
            foreach ($tables as $table => $records) {
                $records = (array) $records;
                foreach ($records as $record) {
                    $record = (array) $record;
                    $record = $record['@attributes'];
                    $keys = array_map(
                        function($value) { return '`' . $value . '`'; },
                        array_keys($record)
                    );
                    $values = array_map(
                        function($value) { return "'" . $value . "'"; },
                        array_values($record)
                    );
                    $sql = sprintf(
                        "INSERT INTO %s (%s) VALUES (%s)",
                        $table,
                        implode(',',$keys),
                        implode(',',$values)
                    );
                    $stmt = $this->getConnection()->prepare($sql);
                    $stmt->execute();        
                }
            }
        }
        return $this;
    }
    
    public function resetDatabase()
    {
        foreach ($this->tables as $table) {
            $sql = "DELETE FROM " . $table;
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute();        
            $sql = "VACUUM";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute();        
        }
        return $this;
    }
    
    public function setConnection($connection)
    {
        $this->connection = $connection;
        return $this;
    }
    
    public function getConnection()
    {
        return $this->connection;
    }

    public function setTables($tables = array())
    {
        $this->tables = $tables;
        return $this;
    }
}

