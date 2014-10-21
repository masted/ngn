#Ngn#
Ngn (ɛnʤn) использует стандартную модель MVC для реализации бизнес логики сайта. Начнём последовательный разбор архитектуры типового проекта.

[Красивая версия этой доки](http://doc.majexa.ru/)

##Базовые константы##
Базовая конфигурация проекта должна иметь 3 константы.

1. project/site/config/constants/core.php:
  - `PROJECT_KEY`<br>
    Это уникальный идентификатор проекта. Он не должен содержать ничего, кроме латинских букв в любом регистре. Позднее Вы узнаете в каких случаях он будет использоваться.
2. project/site/config/constants/more.php:
  - `SITE_DOMAIN`<br>
    Домен сайта, например `site.com`
3. project/site/config/constants/site.php:
  - `SITE_TITLE`<br>
    Название сайта

^Для включения _Режима отладки_ добавьте в файл `core.php` константу `IS_DEBUG = true`. Это позволит видеть все ошибки, а так же работать с системными командами, вроде очистки кэша, через адресную строку.

##Собственная инициализация##
Для расширения стандартной инициализации проекта, осуществляемой фреймворком, всегда можно добавить свой функционал. Просто создайте файл `project/site/init.php` и наполните его всем необходимым. 

<a name="di"></a>

^Таким функционалом может стать внедрение зависимых инъекций.<br>
Далее пример инъекций одного из проектов:

    O::replaceInjection('DefaultRouter', 'KpRouter');
    O::replaceInjection('RouterScripts', 'KpRouterScripts');
    O::registerInjection('DdFieldsFields', 'DdFieldsFieldsOrder', ['orders']);
    O::registerInjection('DdFieldsManager', 'DdFieldsManagerOrder', ['orders']);
    O::registerInjection('DdFields', 'DdFieldsSubclient', ['subclients']);
    O::registerInjection('DdXls', 'DdXlsOrders', ['orders'], false);
    O::registerInjection('OrderSend', 'KpOrderSend');
    O::registerInjection('SubclientsFinder', 'KpSubclientsFinder');


##Контроллеры##
О контроллерах знать нужно буквально следующее:

- Контроллер — это класс, наследуемый от `CtrlBase` и начинающийся с `Ctrl`
- Экшн — это ф-я контроллера начинающаяся с `action_`
- Какой экшн вызывать определяет строка URL. Она разбирается на части по символу `/` в массив
- Какой из элементов массива считать именем экшена, определает ф-я контроллера `CtrlBase::getParamActionN()`. Если возвращает `0`, значит по запросу `http://site.com/param1/param2` будет вызван `action_param1()`
- По запросу `http://site.com/` будет вызыван экшн по-умолчанию экшн `action_default()` 
- Экшн формирует массив для шаблона: `$this->d`
- Какой шаблон будет выведен в данном экшене определяет внутренняя логика контроллера

##Роутинг##

До вызова контроллера, его необходимо определить. Эта процедура называется роутинг. Роутинг осуществляется роутером. В Ngn существует несколько видов роутеров. Сейчас мы рассмотрим тот, что обрабатывает запросы к фронтенду сайта, т.е. к его публичным страницам. Класс `DefaultRouter`. Метод `DefaultRouter::_getController()` возвращает объект контроллера. Разберём по какому принципе происходит определение класса контроллера.

Допустим `PROJECT_KEY` = `three`. Далее описана последовательность, в которой происходит поиск классов контроллеров. Первый найденный и будет использоваться.

- для запроса `http://sub.site.com/`:
  - CtrlThreeSub
  - CtrlThreeDefault
  - CtrlDefault

- для запроса `http://sub.site.com/some`:
  - CtrlThreeSome
  - CtrlSome
  - CtrlThreeSub
  - CtrlThreeDefault
  - CtrlDefault

^Узнать какой контроллер используется в данный момент можно добавив в запрос GET параметр `showController=1` (при условие что _Режим отладки_ включен)

###Изменение стандартного поведения роутера###

Описанное выше поведение можно легко дополнить или полностью изменить, если оно не удовлетворяет ваши нуждам. Ngn имеет под капотом мощный инструмент для переопределения классов на основе паттерна [Dependency Injection](#di). Классы, поддерживающие переопределение можно найти в коде Ngn по ключевому слову `O::di`. Класс `DefaultRouter` из их числа. Само переопределение реализуется методом `O::replaceInjection()`. Это безусловное переопределение. Про условное будет сказано в другой главе. Рассмотрим результат на примере:

Создаём файл собственной инициализации `project/site/init.php`

    O::replaceInjection('DefaultRouter', 'MyRouter');

* Здесь и далее в примерах опускаются открывающие php-тэги `<?php`. Это сделано для удобства чтения. Учитывайте это при копировании. 

Создаём файл класса роутера `project/site/lib/MyRouter.class.php`

    class MyRouter extends DefaultRouter {
      protected function prefix() {
        return 'CtrlSpecial';
      }
    }

^ Все классы должны находится в папке `project/site/lib` (или её подпапках).

^ Учтите, список всех классов в php-автозагрузчике кэшируется. Так что после добавления нового класса, кэш нужно очистить. Из адресной строки это делается запросом с параметром `?cc=1`.


В роутере был переопределен метод префикс, который в стандартной реализации возвращал строку `Ctrl`. Для такого роутера последовательность поиска контроллеров будет следующей:

- для запроса `http://sub.site.com/some`:
  - CtrlThreeSome
  - CtrlSpecial
  - CtrlThreeSub
  - CtrlThreeDefault
  - CtrlSpecialDefault
  - CtrlDefault

Переопределяя метод `DefaultRouter::_getController()` и используя объект `Req` можно внедрять любую логику определения контроллеров в зависимости от запроса.

##Объект запроса##
<b>класс `Req`</b>

Объект запроса предоставляет разобранные данные из строки URL.
Этот объект присутствует как в роутерах, так и контроллерах (`$this->req`).
В первую очередь это уже знакомый нам массив параметров Req::$params. Параметрами запроса называются строки, разделённые слэшем.

Так же <i>Объект запроса</i> выполняет ряд простых, но полезных в работе с роутерами функций:

1. __Проверка входных параметров.__ В случае, если проверка не прошла выбрасывается исключение
<div class="api" markdown="1"><div class="help">@api</div>  - `Req::[b]param[/b]($n);`<br>Возвращает значение параметра $n и проверяет его на `empty()`
  - `Req::[b]rq[/b]($name);`<br>Возвращает значение `$_REQUEST[$name]` и проверяет его на `isset()`
  - `Req::[b]reqNotEmpty[/b]($name);`<br>Возвращает значение `$_REQUEST[$name]` и проверяет его на `empty()`
  - `Req::[b]reqAnyway[/b]($name);`<br>Возвращает значение `$_REQUEST[$name]` или пустую строку, если не прошла проверка на `empty()`
</div>
2. __Обёртка для $_GET, $_POST, $_REQUEST__<br>
  Эти массивы представленны в Объекте запроса соответствующими свойствами: Req::$g, Req::$p, Req::$r. Предпочтительно использовать именно их. Да. Ничего страшного не произойдёт, если вы напишете где-нибудь в контроллере `if (isset($_GET['email'])) ...`. Но есть пара причин, когда это сможет Вам повешать в дальшейшем:
  - Использование сложной логики в контроллерах может потребовать вызывать один из другого. В таком случае дочерний контроллер должен быть создан со своим уникальным _Объектом запроса_.
  - Тестирование вашего приложения так же может потребовать создавать контроллеры с необходимым для тестирования запросом не нарушая принципов изолированности объекта.
 
##Шаблоны##
- Шаблоны представляют собой php-файлы вида
  `<html><?= $d['somevar'] ?></html>`, где `$d` — это свойство контроллера `CtrlBase::$d`
- Данные для шаблонов создаются либо в контроллере, либо передаются родительским шаблоном.
- Что бы наглядно увидеть где на сайте используется какой шаблон, определите константу `TEMPLATE_DEBUG = true` в файле `site/config/constants/more.php`
- В HTML-коде сайта появятся подобные комментарии: `<!-- Begin Template "/home/user/ngn-env/sb/tpl/main" -->`
- В PHP-коде путь к такому шаблону будет выглядеть так: `main`.
  В результате будет производится поиск файла шаблона по следующим путям в файловой системе:
  - `/home/user/ngn-env/projects/projectName/site/tpl/main.php`
  - `/home/user/ngn-env/sb/tpl/main.php`
  - `/home/user/ngn-env/ngn/tpl/main.php`

В соответствующем порядке. Так что для переопределения шаблона `/home/user/ngn-env/sb/tpl/main` нужно создать в своём проекте соответствующий файл `/home/user/ngn-env/projects/projectName/site/tpl/main.php`

###Главный шаблон `main`###
_Главный шаблон_ — это шаблон лейаута страницы. Он может выглядеть примерно так:

    <html>
      <head><?= $d['tpl'] ?></head>
      <body><?= $this->tpl($d['tpl'], $d) ?></body>
    </html>

Имя _Главного шаблона_ находится в переменной `$d['mainTpl']` и имеет значение `main`

###Примеры использования шаблонов в связке с контроллером###

    class CtrlThreeDefault extends CtrlBase {
      function action_default() {
        $this->d['someVar'] = 123;
      }
    }

Содержимое `project/site/tpl/main.php`:

    <html>
      <body><?= $this->tpl($d['tpl'], $d) ?></body>
    </html>

Содержимое `project/site/tpl/default.php`:

    <b>someVar:</b> <?= $d['someVar'] ?>

Результат рендеринга шаблона:

    <html>
      <body><b>someVar:</b> 123</body>
    </html>

Имя _Внутреннего шаблона_ находится в переменной `$d['tpl']` и имеет значение `default`, если оно не определено в контроллере.

##Формы##
В Ngn формы выполняют следующие роли:

- генерация HTML-форм
- конвертация данных из формата HTML-формы в формат полей
- фильтрация пользовательских данных на основе полей

Базовым классом, а так же ядром функционала форм является класс `Form`.
В конструкторе он принимает массив или объект полей `Fields`.

В Ngn принято использовать разметку генерируемого формой HTML-кода как есть.
Т.к. изменение HTML-кода влияет на более высокие слои системы, такие, например, как js-валидация и отображение ошибок. Но это ни в коем случае не значит, что изменение шаблона формы не возможно.
Переопределение элементов массива `Form::$templates` позволит внести свои коррективы.

###Формы в шаблонах###

В шаблоне, как было сказано выше, используется готовый HTML сгенерированой формы.
Рендeринг _Формы_ чаще всего удобней делать в контроллере.
Там же мы будем обрабатывать данные из _Формы_.

Возьмём контроллер, рассмотренный выше, и измним его для работы с _Формой_. Шаблоны при этом остаются такими же.

    class CtrlThreeDefault extends CtrlBase {
      function action_default() {
        $form = new Form([
          [
            'title' => 'Название',
            'type' => 'text',
            'name' => 'title',
            'required' => true
          ]
        ]);
        if ($form->isSubmittedAndValid()) {
          // Произошел сабмит формы. Валидация так же прошла успешно.
          // Дописываем дамп массива в файл с идентификатором формы.
          file_put_contents(
            DATA_PATH.'/'.$form->id(),
            var_dump($form->getData(), true),
            FILE_APPEND
          );
          $this->d['someVar'] = 'Ваши данные успешно сохранены';
        } else {
          // Если не было сабмита или проблемы с валидацией, рендерим
          // форму и помещаем HTML-код в переменную, которая выводится
          // во Внутреннем шаблоне
          $this->d['someVar'] = $form->html();
        }
      }
    }

<div class="api" markdown="1"><div class="help">@api</div>- `Form::[b]isSubmitted[/b]();`<br>Определяет был ли сабмит этой формы
- `Form::[b]isSubmittedAndValid[/b]();`<br>Проверяет был ли сабмит этой формы и прошла ли валидация введёных в неё данных
- `Form::[b]html[/b]();`<br>Возвращает HTML формы
- `Form::[b]validate[/b]();`<br>Произваодит валидацию введёных в форму данных
- `Form::[b]id[/b]();`<br>Возвращает уникальный идентификатор формы
</div>

В контструкторе формы используется массив с настройками полей (а вернее одного поля).
Эти настройки используются при создании _Элементов полей_.

###Элемент поля###
__Элемент поля__ — это объект класса вида `FieldE{Type}`, наследуемый от `FieldEAbstract`, где `Type` — тип поля.
_Элементы полей_ создаются внутри _Формы_, а их опции задаются в конструкторе (тот самый массив в нашем примере).

Вот основные задачи, которые выполняет _Элемент поля_:

- вывода HTML-кода поля
- инициализации JS-кода поля;
- преобразования данных из формата источника в формат поля;
- преобразования данных из формата поля в формат источника;
- серверной валидации данных из поля.

`FieldEAbstract` — базовый класс, от которого наследуются классы всех _Элементов полей_.
<div class="api" markdown="1"><div class="help">@api</div>- `FieldEAbstract::[b]validate[/b]();`<br>Валидация. Происходит только при сабмите формы. Ищет методы текущего объекта, имеющие вид `validate{N}` и вызывает их по очереди. Если после вызова такого метода, свойство `FieldEAbstract::$error` заполнено, значит проверка прерывается, а метод `validate()` возвращает `FALSE`. `N` должно быть > `1`, т.к. уже существует один стандартный метод валидации `FieldEAbstract::validate1()`. Он проверяет пустое ли значение _Элемента поля_, если опция `required = true`.
- `FieldEAbstract::[b]html[/b]();`<br>Возвращает HTML-код _Элемента поля_
- `FieldEAbstract::[b]js[/b]();`<br>Возвращает JS-код _Элемента поля_
</div>

Пример валидации:

    class FieldEName extends FieldEText {
      protected function validate2() {
        if (!Misc::validName($this->options['value'])) {
          $this->error('Неправильный формат');
        }
      }
    }

`FieldEInput` используется для всех полей ввода пользовательских данных, таких как `input`, `select`, `textarea` и др.

Список опций, общих для любого _Элемента поля_-предка `FieldEInput`:

- `name`: имя поля; атрибут `name` HTML-тега
- `id`: атрибут `id` HTML-тега. Если не указан, генерируется из имени: `Misc::name2id($name)`
- `required`: обязательно ли для заполнения
- `title`: название поля
- `help`: описание/подсказка под полем
- `cssClass`: css-класс для HTML-тега (`input`/`select`/`textarea`)
- `value`: значение поля. Заполняется автоматически _Формой_
- `maxlength`: максимальная длина для текстовых полей
- `disabled`, `placeholder`, `autocomplete`, `multiple`: соответствуют аналогичным HTML-атрибутам
- `data`: этот массив будет трансформирован в `data-key="value"` атрибуты HTML-тега
- `jsOptions`: опции для JS-класса этого элемента

##Менеджер данных##
_Менеджер данных_ является связующим звеном между Формой и источником данных.
Он предоставляет абстрактный интерфейс для создания CRUD операций.
Так же _Менеджер данных_ является ядром для действий, специфичных для различных типов полей.
Т.е. CRUD-операции выполняются не только на утровне источника данных (например БД), но так же и на
уровне каждого поля. Например, для поля вставки файла, такими операциями будут создание и удаление файла.

В Ngn существует несколько типов _Менеджеров данных_. Все они - предки класса `DataManagerAbstract`.
Рассмотрим базовый функционал _Менеджера данных_, находящийся в этом классе:
<div class="api" markdown="1"><div class="help">@api</div>- `DataManagerAbstract::[b]getItem[/b]($id);`<br>Возвращает одну запись по ID
- `DataManagerAbstract::[b]requestCreate[/b]([$default]);`<br>Создает новую запись
- `DataManagerAbstract::[b]requestUpdate[/b]($id);`<br>- Получает значения для _Формы_ из записи: `DataManagerAbstract::getItem($id)`;<br> - Преобразует значения в формат, необходимый для _Формы_;<br> - Инициализирует _Форму_ и _Элементамы полей_ с преобразованными данными;<br> - Получает данные из _Формы_;<br> - Преобразует их в формат источника;<br> - Вызывает специфичные типам полей экшены;<br> - Добавляет системные значения;<br> - Если произошел сабмит _Формы_ - Выполняет апдейт записи;<br> - Вызывает специфичные типам полей пост-экшены.
- `DataManagerAbstract::[b]formData[/b]($id);`<br>Возвращает данные записи в формате _Формы_
- `DataManagerAbstract::[b]afterFormElementsInit[/b]();`<br>Вызывается после инициализации _Элементов полей_, но до рендеринга _Формы_. Таким образом этот метод можно использовать для переопределения опций _Элементов полей_ прямо из _Менеджера данных_.
- `DataManagerAbstract::[b]beforeUpdate[/b]();`<br>Действия перед апдейтов (используйте $this->data)
</div>

Как видно из описания метода `DataManagerAbstract::requestUpdate()` существует несколько возможностей для расширения поведения
при сохранении данных, например `DataManagerAbstract::beforeUpdate()`. Как на уровне _Менеджера данных_, так и на уровне _Экшенов полей_.

Сделаем пример реализации собственного _Менеджера данных_:

    class FileDataManager extends DataManagerAbstract {
      function getItem($id) {
        require DATA_PATH."/$id";
      }
      protected function _create() {
        $id = getLastFileIndex(DATA_PATH) + 1; // индекс новой записи
        file_put_contents(
          DATA_PATH."/$id",
          // $this->data — данные из Формы
          "<?php\n\n".var_dump($this->data, true).';'
        );
        return $id;
      }
      protected function _update() {
        file_put_contents(
          DATA_PATH."/".$this->id, // $this->id — ID редактируемой записи
          "<?php\n\n".var_dump($this->data, true).';'
        );
      }
      protected function _delete() {
        unlink(DATA_PATH."/".$this->id);
      }
    }

Теперь посмотрим как использовать его в роутере. Сделаем форму добавления новой записи:

    class CtrlThreeDefault extends CtrlBase {
      function action_default() {
        $manager = new FileDataManager;
        if ($manager->requestCreate()) {
          $this->d['someVar'] = 'Ваши данные успешно сохранены';
        } else {
          $this->d['someVar'] = $manager->form->html();
        }
      }
    }

Добавим экшн для редактирования уже существующих записей:

    function action_edit() {
      $manager = new FileDataManager;
      if ($manager->requestUpdate($this->req['id'])) {
        $this->d['someVar'] = 'Ваши данные успешно сохранены';
      } else {
        $this->d['someVar'] =
          '<h1>Редактирование записи '.$manager->defaultData['title'].'</h1>'.
            $manager->form->html();
      }
    }

В последнем листинге к HTML-коду формы, добавляется так же заголовок редактируемой записи `$manager->defaultData['title']`.
Массив данных текущей записи доступен благодаря выполнению `requestUpdate()`.

Заголовок, полученный из _Формы_, можно вывести так: `$manager->data['title']`.

^ Обращаться к параметрам запроса можно напрямую: `$this->req['id']`.
Такая запись (благодаря `ArrayAccesseble`) аналогична этой: `$this->req->r['id']`.

###Экшены полей###
_Экшены полей_ дают возможность создавать поля вместе с изолироваными обработчиками данных.
Такой подход позволяет не только использовать существующий
функционал многократно в любых _Менеджерах данных_, но и комбинировать любое количество типов полей, с готовым
проверенным поведением.

_Экшены поля_ — это методы класс вида `Dmfa{Type}`, наследуемый от `Dmfa`, где `Type` - тип поля.
Экшены бывают 2-х типой. Посмотрите на их заголовки:

    /**
     * Data Manager Field Action
     * look at DataManagerAbstract::getDmfa()
     */
    abstract class Dmfa {
     
      /**
       * @var DataManagerAbstract
       */
      protected $dm;
    
      function __construct(DataManagerAbstract $dm) {
        $this->dm = $dm;
      }
    
      // function form2sourceFormat($v) { return $v; }
      // function source2formFormat($v) { return $v; }
      // function elBeforeCreateUpdate(FieldEAbstract $el) {}
      // function elAfterCreateUpdate(FieldEAbstract $el) {}
      // function elAfterUpdate(FieldEAbstract $el) {}
      // function elBeforeDelete(FieldEAbstract $el) {}
    
    }

Первые 2 выполняются, когда _Элемент поля_ ещё не создан. В метод передаётся значение поля, полученное из _Формы_.

Другие 4 получают в качестве аргумента _Элемент поля_. Значение этого поля доступно по укороченной записи `$el['value']`.

Придумаем несколько экшнов для поля с типом `phone`:

    class DmfaPhone extends Dmfa {
      // в БД числовое поле; вырезаем плюс
      function form2sourceFormat($v) {
        return ltrim($v, '+');
      }
      // а в форме всё будет как надо с плюсом
      function source2formFormat($v) {
        return '+'.$v;
      }
      // добавляем в Менеджер данных ещё одно значение для сохранения
      function elBeforeCreateUpdate(FieldEAbstract $el) {
        $this->dm->data['secondaryPhone'] = $el['value'];
      }
      protected function resourceFile(FieldEAbstract $el) {
        // ---------------------->  в значении поля всё ещё есть "+", вырезаем
        return DATA_PATH.'/'.$el['name'].'_'.ltrim($el['value'], '+');
      }
      // создаём ресурс (файл)
      function elAfterCreateUpdate(FieldEAbstract $el) {
        file_put_contents($this->resourceFile($el));
      }
      // записываем в лог, например
      function elAfterUpdate(FieldEAbstract $el) {
        log('phone changed: '.$el['value']);
      }
      // удаляем ресурс
      function elBeforeDelete(FieldEAbstract $el) {
        unlink($this->resourceFile($el));
      }
    }

##Панель управления##
Для каждого веб-проекта, работающего под управлением Ngn, доступна _Панель управления_.
В пустом проекте будут доступны всего несколько разделов: _Логи_, _Конфигурация_ и _Структуры_.

__Логи__ - это веб-отображения логов сайта. Сюда сваливаются сообщения оставленные через метод `LogWriter::v('logName', 'message')`. Их можно найти в папке `project/site/logs`. Файлы с префиксом `r_`.

__Конфигурация__ - это веб-отображение конфигурации проекта. Значения по умолчанию можно найти в папках `ngn/more/config/vars`, `ngn/more/config/constants`. Описание структуры переменных и констант в папке `ngn/more/config/struct`.

__Структуры__ - веб-интерфейс для создания dd-структур.

Ссылки в главное меню _Панели управления_ добавляются через конфиг `project/site/config/vars/adminTopLinks.php`.

    <?php

    return [
      [
        'link'  => Tt()->getPath(1).'/ddItemsFilter/orders',
        'class' => 'list',
        'title' => 'Заявки',
      ]
    ];


##Работа с БД##
Для работы с базой данных Ngn предоставляет:

- 2 низкоуровневых инструмента:
  - объект создания плоских запросов `Db`
  - объект условий `DbCond`
- 2 реализации объектно-реляционного проецирования:
  - модели `DbModelCore`
  - библиотека `Dynamic Data`

Рассмотрим каждый отдельно.

###1. Выполнение SQL-запросов###

Основной задачей класса Db является прозрачная защита от SQL-инъекций при составлением запросов с помощью плейсхолдеров.

Экземпляр этого класса с настройками подключения к локальной базе проекта всегда доступен через глобальную функцию `db()`.

<div class="api" markdown="1"><div class="help">@api</div>- `Db::[b]select[/b](string $query [, $arg1] [, $arg2] ...);`<br>Выполняет запрос и возвращает результат в виде массива
- `Db::[b]query[/b](string $query [, $arg1] [, $arg2] ...);`<br>Алиас для select(). Может быть использована для INSERT или UPDATE запросов
- `Db::[b]selectRow[/b](string $query [, $arg1] [,$arg2] ...);`<br>Возвращает первую строчку из результата запроса. При ошибках вернёт null и установит последнюю ошибку. Если запрос вернул пустой результат, метод возвращает пустой массив. Это удобно при отладке, потому что PHP не выбрасывает NOTICE на выражении `$row['abc']`, если `$row === null` или `$row === false`. Но если $row - путой массив NOTICE выбросится. *
- `Db::[b]selectCol[/b](string $query [, $arg1] [,$arg2] ...);`<br>Возвращает первую колонку из результата запроса
- `Db::[b]selectCell[/b](string $query [, $arg1] [, $arg2] ...);`<br>Возвращает первую ячейку первой колонки результата запроса. Если не выбрано ниодной строки, возвращает null.
</div>

Пример запроса с плейсхолдерами:

    db()->query('SELECT * FROM users WHERE id>?d AND NAME LIKE ?', 3, 'a%');

Для "распаковки" параметров и получения конечного SQL-выражения используйте `Db::prepareQuery('QUERY', param1, param2, ...)`. Выполнение `prepareQuery` для предыдущего запроса вернёт строку:

    SELECT * FROM users WHERE id>3 AND NAME 'a%'

Первый параметр, благодаря уточнению `?d` (digit) был выведен без кавычек, а значение явно приведено к числовому типу.

###2. Объект SQL-условий###
При компонентном подходе часто бывает необходимо представить условие в SQL запросе в виде объекта. Изменяя <i>Объект условий</i> в существующем компоненте, можно легко влиять на данные внутри него, не изменяя код самого компонента.

Класс `DbCond` имеет ряд методов для реализации различных вариаций SQL-фильтрации. Метод `DbCond::all()` преобразует все условия обратно в SQL-строку для подстановки её в конечный запрос.

Рассмотрим несколько примеров использования <i>Объекта условий</i>:

    (new DbCond)->addF('id', 3)->all();
    // вернёт `WHERE 1 AND id = 3`

    (new DbCond)->addFromFilter('id', 3)->all();
    // вернёт `WHERE 1 AND id > 3`

    (new DbCond)->addRangeFilter('id', 3, 5)->all();
    // вернёт `WHERE 1 AND id > 3 AND id < 5`

Пример с полным запросом:

    $cond = new DbCond('info');
    $cond->addRangeFilter('age', 17, 60, null, true);
    $goodWorkers = db()->select(
      'SELET users.* FROM users LEFT JOIN info ON info.userId=user.id'. //
      $cond->all()
    );

Конечный запрос:

    SQL:
    SELECT users.* FROM users
    LEFT JOIN info ON info.userId=user.id
    WHERE 1 AND info.age > "17" AND info.age < '60';

Следующие методы добавляют в _Объект условий_ фильтры:

<div class="api" markdown="1"><div class="help">@api</div>  - `DbCond::[b]addF[/b]($key, $value, [$func]);`<br>Добавляет фильтр по одному значению
  - `DbCond::[b]addRangeFilter[/b]($key, $from, [$to], [$params], [$strict]);`<br>Добавляет фильтр по диапазону
  - `DbCond::[b]addLikeFilter[/b]($key, $text);`<br>Добавляет фильтр поиска по маске
  - `DbCond::[b]addNullFilter[/b]($key, [$isNull]);`<br>Добавляет фильтр по значению NULL
  - `DbCond::[b]addFromFilter[/b]($key, $from, [$func], [$strict]);`<br>Добавляет фильтр "больше чем ..."
  - `DbCond::[b]addToFilter[/b]($key, $to, [$func], [$strict]);`<br>Добавляет фильтр "меньше чем ..."
  - `DbCond::[b]addNotInFilter[/b]($key, $value);`<br>Добавляет фильтр "не входит в ..."
  - `DbCond::[b]addExprFilter[/b]($key, $expr);`<br>Добавляет фильтр по выражению
</div>


###3. db-модели###

Класс `DbModelCore` предоставляет интерфейс для работы с db-моделями.

__db-модель__ - это объект класса `DbModel` (или его предка), созданный на основе данных одной строки таблицы базы данных.

При работе с db-моделями существует 4 основных процедуры: создание, измненение, получение, удаление:

<div class="api" markdown="1"><div class="help">@api</div>- `DbModelCore::[b]get[/b]($table, $value, [$param]);`<br>Возвращает db-модель
- `DbModelCore::[b]take[/b]($table, $value, [$param]);`<br>Возвращает db-модель. Если не существует выбрасывает исключение
- `DbModelCore::[b]getClass[/b]($table);`<br>Возвращает класс db-модели
- `DbModelCore::[b]create[/b]($table, $data, [$filterByFields]);`<br>Создаёт запись с данными `$data` в таблице `$table`. Если `$filterByFields=true`, данные фильтруются по именам полей
- `DbModelCore::[b]update[/b]($table, $id, $data, [$filterByFields]);`<br>Изменяет на строчку с `ID=$id` в таблице `$table` на основе данных `$data`. Если $filterByFields=true, данные фильтруются по именам полей
- `DbModelCore::[b]replace[/b]($table, $id, $data, [$filterByFields]);`<br>Если строка с `ID=$id` существует, выполняет `DbModelCore::update()`, иначе `DbModelCore::create()`
- `DbModelCore::[b]delete[/b]($table, $id);`<br>Удаляет строку
- `DbModelCore::[b]collection[/b]($table, [$cond], [$mode]);`<br>Возвращает набор db-моделей
- `DbModelCore::[b]count[/b]($table, [$cond]);`<br>Возвращает количество записей в таблице
</div>

###4. dd (Dynamic Data). Cистема динамических данных###

__Cистема динамических данных (dd)__ — это система по управлению таблицами и записями базы данных.
Она имеет богатый встроенный функционал, позволяющий создавать источники данных с готовыми интерфейсами для их управления.
Dd-компоненты так же ускоряют реализацию своих систем управления, CRM, адмиок и т.п.

Dd-система имеет веб-интерфейс для управления. Он обеспечивает управление dd-структурами, dd-полями, dd-тегами и dd-записями.
Это не значит, что вы не можете использовать своём проекте генерацию dd-сущностей через API. Но для наших целей этого не требуется.
Рассмотрим пример, в котором использование dd-системы будет крайне актуально.

Пусть у нас имеется база пользователей. Каждый пользователь имеет профиль с личной информацией,
, а так же свои фотографии. Нам необходимо позволить редактировать всё это только этому пользователю
и администратору сайта.

В БД такая структура выглядела бы, как один-к-одному для информации профиля и один-ко-многим
для фотографий. В dd-системе всё будет так же, только в разы проще.

Создадим через _Панель управления_ 2 структуры: "профиль" и "фотографии". Пользователи
находятся в системной таблице `users`. Она уже существует.

В структуру "профиль" добавим следующие поля:

<table>
<tr>
  <td>Ф.И.О.</td>
  <td>text</td>
</tr>
<tr>
  <td>Пара слов о себе</td>
  <td>wisiwigSimple</td>
</tr>
<tr>
  <td>Секретный вопрос</td>
  <td>text</td>
</tr>
</table>

В структуру "фотографии" добавим:

<table>
<tr>
  <td>Изображение</td>
  <td>image</td>
</tr>
<tr>
  <td>Описание</td>
  <td>description</td>
</tr>
</table>

Теперь настроим контроллер для работы с ними:

    class CtrlProfile extends CtrlBase {
      function action_show() {
        $this->d['someVar'] = (new Ddo('profile', 'siteItem'))->els();
      }
      function action_edit() {
        $manager = new DdItemsManager('profile');
        if ($manager->getItem($this->req['id'])['userId'] != Auth::get('id'))
          throw new AccessDenied;
        if ($manager->requestUpdate($this->req['id'])) {
          $this->redirect('show');
        } else {
          $this->d['someVar'] = $manager->form->html();
        }
      }
    }

    class CtrlPhotos extends CtrlBase {
      function action_new() {
        $manager = new DdItemsManager('photos');
        if ($manager->requestCreate()) {
          $this->d['someVar'] = 'Фото добавлено';
        } else {
          $this->d['someVar'] = $manager->form->html();
        }
      }
    }

В листинге присутствует 2 новых класса: `Ddo` и `DdItemsManager`. `DdItemsManager` - это _Менеджер данных_ dd-системы.
`Ddo` (Dynamic Data Output) - класс для автоматического HTML-рендеринга для dd-данных.





####dd-cтруктура####
__dd-структура__ — это MySQL-таблица для записей с префиксом `dd_i_`, имеющая ряд системных полей: `id`, `oid`, `active`, `dateCreate`, `dateUpdate`, `ip`, `userId`. Параметры структуры сохраняются в таблице `dd_structures`.

^^Управление _dd-структурами_ в _Панели управления_: `/admin/ddStructure`

####dd-поле####
__dd-поле__ — это поле MySQL-таблицы. Параметры поля сохраняются в отдельную таблицу `dd_fields`.

^^Управление _dd-полями_ в _Панели управления_: `/admin/ddField`

####dd-запись####
Это строка в таблице структуры

^^Управление _dd-записями_ в _Панели управления_: `/admin/ddItems/имя_структуры`.

####dd-тег####
__dd-тег__ — это своеобразный маркер, которым наделяется dd-запись.

Одно из свойств _dd-тега_ - это мультивыбор. Оно определяет можно ли добавлять для _dd-записи_ больше одного тега.

Ещё одно свойство - древовидность. Оно позволяет связывать теги между собой в иерархические структуры. Таким образом при назначении dd-записи корневого маркера, ей будут назначены и все дочерние, связанные с ним.

_dd-теги_ создаются в _Панели управления_, но есть так же и свойство, разрешающее создание тегов "по требованию", если они присутствуют в dd-записи.

^^Управление _dd-тегами_ в _Панели управления_: `/admin/ddField/имя_структуры`. Ссылка на редактирование появится около _Тегового поля_. Его нужно создать

_Теговое поле_ - поле dd-формы реализующее _Элемент поля_ и _Экшены поля_ для работы с _dd-тегами_.

_dd-форма_ - не представляет собой ничего интересного
