# Версионные миграции

Версионная миграция реализуется с помощью специального класса, наследуемого от 
_Migration_ и реализующего следующие методы:

- `up()` - метод выполняет upgrade
- `down()` - метод выполняет downgrade

Рекомендуется включать в имя класса миграции числовой код, определяющий 
порядковый номер миграции или дату создания класса, а так же название миграции, 
кратко описывающее ее суть. Числовой код позволит выполнять миграции в 
правильном порядке.

Пример класса миграции:

```php
namespace Application\Migration;
use Bricks\Migration\Migration;

class Migration1_createDatabase implements Migration{
  public function up(){
  }

  public function down(){
  }
}
```

Для выполнения и отката миграций используется класс _Manager_, принимающий в 
конструкторе адрес файла журнала, доступного для записи. Метод `journal()` может 
использоваться для получения содержимого этого журнала.

Класс _Manager_ использует методы `up(Migration)` и `down(Migration)` для 
upgrade и downgrade. Методы принимают экземпляр исполняемой миграции и применяют 
их, что позволяет подготовить объект перед использованием:

```php
use Bricks\Migration\Manager;

...
$migration = new Migration1_createDatabase($PDO);
$manager = new Manager('storage/journal.txt');
$manager->up($migration);
```

Upgrade будет выполнен только в случае отсутствия миграции в журнале. Downgrade 
выполняется при обратных условиях.

# Загрузка миграций

Интерфейс _LoaderInterface_ описывает классы, способные определять и загружать список доступных миграций приложения. Он расширяет интерфейс _Iterator_, что позволяет выполнять проход по коллекции загруженных миграций.

## GlobLoader

Загрузчик _GlobLoader_ использует glob-шаблон для поиска и загрузки миграций:

```php
use Bricks\Migration\Manager;
use Bricks\Migration\Loader\GlobLoader;

$manager = new Manager('storage/journal.txt');
$loader = new GlobLoader(__NAMESPACE__ . '\Migrations', __DIR__ . '/Migrations', '*Migration.php');
foreach($loader as $migration){
    $manager->up($migration);
}
```
