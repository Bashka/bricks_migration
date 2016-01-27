<?php
namespace Bricks\Migration;

/**
 * Менеджер миграций.
 *
 * @author Artur Sh. Mamedbekov
 */
class Manager{
  /**
   * @var string Адрес файла журнала, используемый для хранения информации о 
   * выполненных миграциях.
   */
  private $journalPath;

  /**
   * @var array Имена классов выполненных миграций.
   */
  private $completed;

  /**
   * @param string $journalPath Адрес файла журнала, используемый для хранения 
   * информации о выполненных миграциях.
   */
  public function __construct($journalPath){
    $this->journalPath = $journalPath;
    if(file_exists($this->journalPath)){
      $this->completed = unserialize(file_get_contents($this->journalPath));
    }

    if(!is_array($this->completed)){
      $this->completed = [];
    }
  }

  /**
   * @return array Журнал исполненных миграций.
   */
  public function journal(){
    return $this->completed;
  }

  /**
   * Выполняет миграцию.
   *
   * @param Migration $migration Выполняемый экземпляр миграции.
   */
  public function up(Migration $migration){
    $migrationClass = get_class($migration);
    if(in_array($migrationClass, $this->completed)){
      return;
    }

    $migration->up();
    $this->completed[] = $migrationClass;
    file_put_contents($this->journalPath, serialize($this->completed));
  }

  /**
   * Откатывает миграцию.
   *
   * @param Migration $migration Откатываемый экземпляр миграции.
   */
  public function down(Migration $migration){
    if(($p = array_search(get_class($migration), $this->completed)) === false){
      return false;
    }

    $migration->down();
    array_splice($this->completed, $p, 1);
    file_put_contents($this->journalPath, serialize($this->completed));
  }
}
