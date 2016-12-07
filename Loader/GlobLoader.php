<?php
namespace Bricks\Migration\Loader;

use ArrayIterator;
use GlobIterator;

/**
 * Загрузчик миграций, использующий glob-шаблон.
 *
 * @author Artur Sh. Mamedbekov
 */
class GlobLoader extends ArrayIterator implements LoaderInterface{
  /**
   * @param string $path Адрес каталога, содержащего файлы классов-миграций.
   * @param string $glob [optional] Glob-шаблон для выборки файлов 
   * классов-миграций.
   */
  public function __construct($namespace, $path, $glob = '*.php'){
    $migrations = [];

    foreach(new GlobIterator($path . '/' . $glob) as $file){
      if($file->isDir()){
        continue;
      }

      require_once($file->getPathname());

      $migrationClassname = '\\' . $namespace . '\\' . $file->getBasename('.' . $file->getExtension());
      if(!class_exists($migrationClassname)){
        continue;
      }

      $migrations[] = new $migrationClassname;
    }

    parent::__construct($migrations);
  }
}
