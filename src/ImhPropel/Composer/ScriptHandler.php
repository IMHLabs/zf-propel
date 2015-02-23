<?php
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/Composer/ScriptHandler
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
	
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/Composer/ScriptHandler
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
namespace ImhPropel\Composer;

use Composer\Script\Event;

class ScriptHandler
{
    const ZEND_INDEX_PATH = 'zend-index-path';

    /**
     * Update Propel modules
     *
     * @param Event $event
     */
    public static function updatePropel(Event $event)
    {
        $defaultOptions   = array(
            self::ZEND_INDEX_PATH => 'public/index.php',
        );
        $newOptions       = $event->getComposer()->getPackage()->getExtra();
        $options          = array_merge($defaultOptions, $newOptions);
        $zendIndexPath    = $options[self::ZEND_INDEX_PATH];
        $updateCmd = 'propel update all';
        $cmd = sprintf('php %s %s', $zendIndexPath, $updateCmd);
        system($cmd);
    }
}
