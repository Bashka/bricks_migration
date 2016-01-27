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
   * Проверяет, возможен ли upgrade с помощью данной миграции.
   *
   * @param Migration $migration Целевая миграция для upgrade.
   *
   * @return bool true - если данную миграцию можно использовать для выполнения 
   * upgrade.
   */
  public function isUp(Migration $migration){
    if(in_array(get_class($migration), $this->completed)){
      return false;
    }

    return true;
  }

  /**
   * Проверяет, возможен ли downgrade с помощью данной миграции.
   *
   * @param Migration $migration Целевая миграция для downgrade.
   *
   * @return bool true - если данную миграцию можно использовать для выполнения 
   * downgrade.
   */
  public function isDown(Migration $migration){
    if(!in_array(get_class($migration), $this->completed)){
      return false;
    }

    return true;
  }

  /**
   * Выполняет миграцию.
   *
   * @param Migration $migration Выполняемый экземпляр миграции.
   */
  public function up(Migration $migration){
    if(!$this->isUp($migration)){
      return;
    }

    $migration->up();
    $this->completed[] = get_class($migration);
    file_put_contents($this->journalPath, serialize($this->completed));
  }

  /**
   * Откатывает миграцию.
   *
   * @param Migration $migration Откатываемый экземпляр миграции.
   *
   * @return bool true - если downgrade выполнен, иначе - false.
   */
  public function down(Migration $migration){
    if(!$this->isDown($migration)){
      return;
    }

    $migration->down();
    $p = array_search(get_class($migration), $this->completed);
    array_splice($this->completed, $p, 1);
    file_put_contents($this->journalPath, serialize($this->completed));
  }
}
