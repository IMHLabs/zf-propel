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

php index.php propel install all
 - Creates connection information for all Modules

php index.php propel install [ModuleNamespace]
 - Creates connection information for one Module

php index.php propel migration all
 - Runs Propel migration:diff on all Modules and generate migrations as appropriate

php index.php propel migration [ModuleNamespace]
 - Runs Propel migration:diff on one Module and generate migrations as appropriate

php index.php propel update all
 - Updates application with most recent changes, applies Migrations, generates new SQL files and 
   generates Propel classes for all Modules

php index.php propel update [ModuleNamespace]
 - Updates application with most recent changes, applies Migrations, generates new SQL files and 
   generates Propel classes for one Module
