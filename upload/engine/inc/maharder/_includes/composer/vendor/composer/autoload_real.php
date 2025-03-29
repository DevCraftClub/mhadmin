<?php



class ComposerAutoloaderInitComposerPhar1663164675
{
private static $loader;

public static function loadClassLoader($class)
{
if ('Composer\Autoload\ClassLoader' === $class) {
require __DIR__ . '/ClassLoader.php';
}
}




public static function getLoader()
{
if (null !== self::$loader) {
return self::$loader;
}

spl_autoload_register(array('ComposerAutoloaderInitComposerPhar1663164675', 'loadClassLoader'), true, true);
self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
spl_autoload_unregister(array('ComposerAutoloaderInitComposerPhar1663164675', 'loadClassLoader'));

require __DIR__ . '/autoload_static.php';
call_user_func(\Composer\Autoload\ComposerStaticInitComposerPhar1663164675::getInitializer($loader));

$loader->register(true);

$includeFiles = \Composer\Autoload\ComposerStaticInitComposerPhar1663164675::$files;
foreach ($includeFiles as $fileIdentifier => $file) {
composerRequireComposerPhar1663164675($fileIdentifier, $file);
}

return $loader;
}
}






function composerRequireComposerPhar1663164675($fileIdentifier, $file)
{
if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
$GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;

require $file;
}
}
