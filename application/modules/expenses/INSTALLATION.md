# Руководство по установке модуля учета расходов для InvoicePlane

## Пошаговая инструкция установки

### 1. Резервное копирование
Создайте резервную копию вашей базы данных и файлов InvoicePlane перед установкой.

### 2. Копирование файлов модуля
Скопируйте папку `expenses_module` в директорию `/application/modules/` вашей установки InvoicePlane.

### 3. Выполнение SQL скрипта
Выполните SQL скрипт `sql/create_tables.sql` в вашей базе данных MySQL.

**Пример через phpMyAdmin:**
1. Откройте phpMyAdmin
2. Выберите базу данных InvoicePlane
3. Перейдите на вкладку "SQL"
4. Скопируйте содержимое файла `sql/create_tables.sql`
5. Нажмите "Выполнить"

### 4. Настройка навигации
Откройте файл `/application/config/navigation.php` и добавьте следующую строку в массив `$nav`:

```php
$nav['expenses'] = [
    'title' => trans('expenses'),
    'class' => 'fa fa-money',
    'href'  => site_url('expenses'),
    'position' => 50
];
```

### 5. Загрузка хелпера
Откройте файл `/application/config/autoload.php` и добавьте `'expense'` в массив `$autoload['helper']`:

```php
$autoload['helper'] = array(
    'url',
    'form',
    'html',
    'invoice',
    'client',
    'currency',
    'invoice_pdf',
    'pdf',
    'date',
    'string',
    'pagination',
    'array',
    'custom_values',
    'expense'  // Добавить эту строку
);
```

### 6. Добавление переводов
Добавьте переводы в файлы языков `/application/language/*/invoiceplane_lang.php`:

```php
// Добавьте эти строки в конец файла
$lang['expenses'] = 'Расходы';
$lang['expense'] = 'Расход';
$lang['create_expense'] = 'Создать расход';
$lang['edit_expense'] = 'Редактировать расход';
$lang['expense_number'] = 'Номер расхода';
$lang['expense_date'] = 'Дата расхода';
$lang['expense_category'] = 'Категория расхода';
$lang['expense_categories'] = 'Категории расходов';
$lang['new'] = 'Новый';
$lang['confirmed'] = 'Подтвержденный';
$lang['paid'] = 'Оплаченный';
$lang['overdue'] = 'Просрочен';
$lang['expense_details'] = 'Детали расхода';
$lang['expense_items'] = 'Позиции расхода';
$lang['expense_taxes'] = 'Налоги расхода';
$lang['mark_confirmed'] = 'Отметить как подтвержденный';
$lang['mark_paid'] = 'Отметить как оплаченный';
$lang['notes'] = 'Примечания';
$lang['terms'] = 'Условия';
$lang['item'] = 'Позиция';
$lang['quantity'] = 'Количество';
$lang['price'] = 'Цена';
$lang['amount'] = 'Сумма';
$lang['balance'] = 'Остаток';
$lang['category'] = 'Категория';
$lang['subtotal'] = 'Подсумма';
$lang['item_tax_total'] = 'Налог на позиции';
$lang['expense_tax_total'] = 'Налог расхода';
$lang['filter_expenses'] = 'Фильтр расходов';
$lang['confirm_expense_status_change'] = 'Изменить статус расхода?';
$lang['expense_notes_placeholder'] = 'Дополнительные примечания к расходу...';
$lang['expense_terms_placeholder'] = 'Условия оплаты...';
$lang['select_category'] = 'Выберите категорию';
$lang['select_tax_rate'] = 'Выберите налоговую ставку';
$lang['no_items_found'] = 'Позиции не найдены';
$lang['no_results'] = 'Нет результатов';
$lang['include_item_tax'] = 'Включать налог на позиции';
$lang['include_tax'] = 'Налог включен';
$lang['created_by'] = 'Создан пользователем';
$lang['basic_info'] = 'Основная информация';
$lang['notes_and_terms'] = 'Примечания и условия';
$lang['add_item'] = 'Добавить позицию';
$lang['add_tax'] = 'Добавить налог';
$lang['save'] = 'Сохранить';
$lang['cancel'] = 'Отменить';
$lang['more'] = 'Еще';
$lang['print'] = 'Печать';
$lang['options'] = 'Опции';
$lang['view'] = 'Просмотр';
$lang['edit'] = 'Редактировать';
$lang['status'] = 'Статус';
$lang['currency'] = 'Валюта';
$lang['total'] = 'Итого';
$lang['due_date'] = 'Срок оплаты';
$lang['items_found'] = 'найдено позиций';
$lang['included'] = 'включено';
$lang['confirm_delete'] = 'Вы уверены, что хотите удалить?';
```

### 7. Проверка установки
1. Войдите в админ панель InvoicePlane
2. Проверьте, что в меню появился раздел "Расходы"
3. Перейдите в раздел "Расходы"
4. Попробуйте создать тестовый расход

## Структура файлов после установки

После установки ваша структура файлов должна выглядеть так:

```
/application/modules/
└── expenses_module/
    ├── controllers/
    │   ├── Expenses.php
    │   └── Ajax.php
    ├── models/
    │   ├── Mdl_Expenses.php
    │   ├── Mdl_Expense_Amounts.php
    │   ├── Mdl_Expense_Items.php
    │   ├── Mdl_Expense_Item_Amounts.php
    │   ├── Mdl_Expense_Tax_Rates.php
    │   └── Mdl_Expense_Custom.php
    ├── views/
    │   ├── index.php
    │   ├── view.php
    │   ├── partial_expense_table.php
    │   ├── modal_create_expense.php
    │   └── modal_edit_expense.php
    ├── helpers/
    │   └── expense_helper.php
    └── sql/
        └── create_tables.sql
```

## Удаление модуля

Для удаления модуля:

1. **Удалите таблицы базы данных** (опционально):
   ```sql
   DROP TABLE IF EXISTS ip_expense_custom;
   DROP TABLE IF EXISTS ip_expense_payments;
   DROP TABLE IF EXISTS ip_expense_tax_rates;
   DROP TABLE IF EXISTS ip_expense_item_amounts;
   DROP TABLE IF EXISTS ip_expense_items;
   DROP TABLE IF EXISTS ip_expense_amounts;
   DROP TABLE IF EXISTS ip_expenses;
   DROP TABLE IF EXISTS ip_expense_categories;
   ```

2. **Удалите файлы модуля**:
   ```
   /application/modules/expenses_module/
   ```

3. **Удалите настройки** из `navigation.php` и `autoload.php`

4. **Удалите переводы** из файлов языков

5. **Удалите настройки модуля** (если создавались в настройках системы)

## Возможные проблемы и решения

### Проблема: Ошибка "Class not found"
**Решение**: Убедитесь, что файл `expense_helper.php` загружается в `autoload.php`

### Проблема: Не отображается меню "Расходы"
**Решение**: Проверьте, что настройки навигации добавлены правильно

### Проблема: Ошибки базы данных
**Решение**: Убедитесь, что все SQL команды выполнились без ошибок

### Проблема: Не работают переводы
**Решение**: Добавьте все переводы в файлы языков

## Поддержка

Для получения поддержки:
1. Проверьте логи ошибок в `/application/logs/`
2. Убедитесь, что все файлы модуля загружены корректно
3. Проверьте права доступа к файлам (должны быть 644 для файлов, 755 для директорий)
