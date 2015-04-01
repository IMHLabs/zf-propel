<?php
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/Propel/Adapter
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */

/**
 * InMotion Hosting Source Code
 * @package ImhPropel/Propel/Adapter
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
namespace ImhPropel\Propel;

namespace ImhPropel\Propel\Generator\Manager;

use Propel\Generator\Manager\MigrationManager as baseMigrationManager;
use Composer\Autoload\ClassLoader;

/**
 * Service class for preparing and executing migrations
 *
 * @author François Zaninotto
 */
class MigrationManager extends baseMigrationManager
{
    public function getMigrationClassBody($migrationsUp, $migrationsDown, $timestamp, $comment = "", $currentNamespace)
    {
        $timeInWords = date('Y-m-d H:i:s', $timestamp);
        $migrationAuthor = ($author = $this->getUser()) ? 'by ' . $author : '';
        $migrationClassName = $this->getMigrationClassName($timestamp);
        $migrationUpString = var_export($migrationsUp, true);
        $migrationDownString = var_export($migrationsDown, true);
        $commentString = var_export($comment, true);
        $migrationClassBody = <<<EOP
<?php
use \ImhPropel\Propel\Generator\MigrationBootstrap;

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version $timestamp.
 * Generated on $timeInWords $migrationAuthor
 */
class $migrationClassName
{
    public \$comment = $commentString;

    public function preUp(\$manager)
    {
        \ImhPropel\Propel\Generator\MigrationBootstrap::init(\$manager, "$currentNamespace");
        // add the pre-migration code here
    }

    public function postUp(\$manager)
    {
        \ImhPropel\Propel\Generator\MigrationBootstrap::init(\$manager, "$currentNamespace");
        // add the post-migration code here
    }

    public function preDown(\$manager)
    {
        \ImhPropel\Propel\Generator\MigrationBootstrap::init(\$manager, "$currentNamespace");
        // add the pre-migration code here
    }

    public function postDown(\$manager)
    {
        \ImhPropel\Propel\Generator\MigrationBootstrap::init(\$manager, "$currentNamespace");
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return $migrationUpString;
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return $migrationDownString;
    }

}
EOP;
        return $migrationClassBody;
    }
}
