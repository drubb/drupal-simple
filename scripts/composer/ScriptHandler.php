<?php

/**
 * @file
 * Contains \DrupalProject\composer\ScriptHandler.
 */

namespace DrupalProject\composer;

use Symfony\Component\Filesystem\Filesystem;

class ScriptHandler {

  /**
   * Determine the path to the web root, below the project root
   *
   * @param $project_root
   * @return string
   */
  protected static function getDrupalRoot($project_root) {
    return $project_root . '/web';
  }

  /**
   * Creates directories, links and files during project creation
   * or updates
   */
  public static function createRequiredFiles() {

    $fs = new Filesystem();
    $project_root = getcwd();
    $web_root = static::getDrupalRoot($project_root);

    // List directories to be created during install
    $dirs = [
      "$web_root/libraries/contrib",
      "$web_root/libraries/custom",
      "$web_root/modules/contrib",
      "$web_root/modules/custom",
      "$web_root/profiles/contrib",
      "$web_root/profiles/custom",
      "$web_root/sites/default",
      "$web_root/themes/contrib",
      "$web_root/themes/custom",
      "$project_root/drush/contrib",
      "$project_root/drush/custom",
      "$project_root/files/private",
      "$project_root/files/public",
      "$project_root/config/sync",
    ];

    // Create the listed directories
    foreach ($dirs as $dir) {
      if (!$fs->exists($dir)) {
        $fs->mkdir($dir, 0755);
        $fs->touch($dir . '/.gitkeep');
      }
    }
    $fs->chmod("$project_root/files/private", 0777);
    $fs->chmod("$project_root/files/public", 0777);
    $fs->chmod("$project_root/config/sync", 0777);

    // Create a symbolic link to the public files directory
    if (!$fs->exists($web_root . '/sites/default/files')) {
      symlink($project_root . '/files/public', $web_root . '/sites/default/files');
    }

    // Prepare the settings file for installation
    if (!$fs->exists($web_root . '/sites/default/settings.php') and $fs->exists($web_root . '/sites/default/default.settings.php')) {
      $fs->copy($web_root . '/sites/default/default.settings.php', $web_root . '/sites/default/settings.php');
      $fs->chmod($web_root . '/sites/default/settings.php', 0777);
    }

    // Prepare the services file for installation
    if (!$fs->exists($web_root . '/sites/default/services.yml') and $fs->exists($web_root . '/sites/default/default.services.yml')) {
      $fs->copy($web_root . '/sites/default/default.services.yml', $web_root . '/sites/default/services.yml');
      $fs->chmod($web_root . '/sites/default/services.yml', 0777);
    }

    // Add a local settings file and include it
    if (!$fs->exists($web_root . '/sites/default/settings.local.php')) {
      $settings = '<?php' . PHP_EOL;
      $settings .= '$settings["file_public_path"] = "sites/default/files";' . PHP_EOL;
      $settings .= '$settings["file_private_path"] = "' . getcwd() . '/files/private";' . PHP_EOL;
      $settings .= '$settings["trusted_host_patterns"] = [ ".*" ];' . PHP_EOL;
      $settings .= '$config_directories[CONFIG_SYNC_DIRECTORY] = "' . getcwd() . '/config/sync";' . PHP_EOL;
      $fs->dumpFile($web_root . '/sites/default/settings.local.php', $settings);
      $fs->chmod($web_root . '/sites/default/settings.local.php', 0777);
      $line = "file_exists(__DIR__ . '/settings.local.php') and include __DIR__ . '/settings.local.php';" . PHP_EOL;
      file_put_contents($web_root . '/sites/default/settings.php', $line, FILE_APPEND);
    }

  }

  /**
   * Remove some leftover project files after project creation
   */
  public static function cleanupComposer() {
    $fs = new Filesystem();
    $fs->remove([
      'LICENSE',
      'README.md',
      '.git'
    ]);

  }
}