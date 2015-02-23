# ZF2 Propel configuration for Modules 

This module is designed to ease usage of Propel 2.x within ZF2 Modules
It provides multiple console commands to perform migration and updates on Modules using propel
as well as setups connection information for Modules using Propel via config files

## Requirements

 * [Zend Framework 2](https://github.com/zendframework/zf2)
 * [Propel](https://github.com/propelorm/Propel2) 
 
## Installation

### Composer

The suggested installation method is via [composer](http://getcomposer.org/):

Composer can be set to automatically apply changes on install/update by including the following
script commands in the composer.json file.

"scripts": {
    "post-install-cmd": [
        "ImhPropel\\Composer\\ScriptHandler::updatePropel"
    ],
    "post-update-cmd": [
        "ImhPropel\\Composer\\ScriptHandler::updatePropel"
    ]
}

## Application Configuration

Here is an sample local configuration file

<?php
return array(
    'propel' => array( 
        'database' => array( 
            'connections' => array( 
                'default' => array( 
                    'host'       => 'localhost',
                    'user'       => 'my_db_user',
                    'password'   => 's3cr3t',
                ),
            )
        ),
    )
);

Here is an sample global configuration file.

<?php
return array(
    'propel' => array( 
        'data_directory'    => '../../data/propel'
        'database' => array( 
            'connections' => array( 
                'default' => array( 
                    'adapter'    => 'mysql',
                    'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
                    "settings" => array(
                        "charset" => "utf8",
                        "queries" => array(
                            "utf8" => "SET NAMES utf8 COLLATE utf8_unicode_ci, COLLATION_CONNECTION = utf8_unicode_ci, COLLATION_DATABASE = utf8_unicode_ci, COLLATION_SERVER = utf8_unicode_ci"
                        )
                    )
                ),
            )
        ),
    )
);


## Module Configuration

Here is an annotated sample module configuration file 

<?php
return array(
    "propel" => array(
        /** Path settings are required to identify locations of the Module's schema and migration files
         *  The class setting determines where Propel generated classes will be stored within the Module
         *  These directories must exist in the file system
         */
        "paths"    => array(
            "schema"     => __DIR__,
            "migrations" => __DIR__ . '/../data/migrations',
            "class"      => __DIR__ . '/../src',
        ),
        "database" => array(
            "connections" => array(
                /** Any settings not specified here will be inherited from the default connection
                 *  See Propel for documentation on these settings
                 */
                "my_db_name" => array(
                    'adapter'    => 'mysql',
                    'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
                    "settings" => array(
                        "charset" => "utf8",
                        "queries" => array(
                            "utf8" => "SET NAMES utf8 COLLATE utf8_unicode_ci, COLLATION_CONNECTION = utf8_unicode_ci, COLLATION_DATABASE = utf8_unicode_ci, COLLATION_SERVER = utf8_unicode_ci"
                        )
                    ),
                    "dbname" => "my_db_name",
                    'attributes' => []
                )
            )
        ),
        "runtime" => array(
            "defaultConnection" => "my_db_name",
            "connections" => array("my_db_name")
        ),
        "generator" => array(
            "defaultConnection" => "my_db_name",
            "connections" => array("my_db_name")
        )
    ),
);

## Console Commands

php index.php propel build [all|NAMESPACE|[comma delimited NAMESPACES]                
 - Build the model classes based on Propel XML schemas
 
php index.php propel build-sql [all|NAMESPACE|[comma delimited NAMESPACES]            
 - Build SQL files

php index.php propel diff [all|NAMESPACE|[comma delimited NAMESPACES]                 
 - Generate diff classes

php index.php propel migration-data [all|NAMESPACE|[comma delimited NAMESPACES]       
 - Generate data migration file

php index.php propel migrate-data [all|NAMESPACE|[comma delimited NAMESPACES]         
 - Generate data migration file

php index.php propel down [all|NAMESPACE|[comma delimited NAMESPACES]                 
 - Execute migrations down

php index.php propel migrate [all|NAMESPACE|[comma delimited NAMESPACES]              
 - Execute all pending migrations

php index.php propel status [all|NAMESPACE|[comma delimited NAMESPACES]               
 - Get migration status

php index.php propel up [all|NAMESPACE|[comma delimited NAMESPACES]                   
 - Execute migrations up

php index.php propel migration-diff [all|NAMESPACE|[comma delimited NAMESPACES]       
 - Generate diff classes

php index.php propel migration-down [all|NAMESPACE|[comma delimited NAMESPACES]       
 - Execute migrations down

php index.php propel migration-migrate [all|NAMESPACE|[comma delimited NAMESPACES]    
 - Execute all pending migrations

php index.php propel migration-status [all|NAMESPACE|[comma delimited NAMESPACES]     
 - Get migration status

php index.php propel migration-up [all|NAMESPACE|[comma delimited NAMESPACES]         
 - Execute migrations up

php index.php propel model-build [all|NAMESPACE|[comma delimited NAMESPACES]          
 - Build the model classes based on Propel XML schemas

php index.php propel sql-build [all|NAMESPACE|[comma delimited NAMESPACES]            
 - Build SQL files

php index.php propel update [all|NAMESPACE|[comma delimited NAMESPACES]               
 - Execute pending migrations, build SQL files, build Propel classes