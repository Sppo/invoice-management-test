# Invoice Management Backend

Backend API.

## Стек

- PHP 8.3
- Laravel 13
- PostgreSQL 16
- Docker Compose

## Структура

Backend розділений на частини з окремими відповідальностями:

- `app/Models/Invoice.php` - модель інвойсу та константи статусів.
- `app/Http/Controllers/InvoiceController.php` -  для invoice endpoints.
- `app/Http/Requests/StoreInvoiceRequest.php` - валідація створення інвойсу.
- `app/Http/Requests/UpdateInvoiceRequest.php` - валідація оновлення інвойсу.
- `app/Services/InvoiceService.php` -  створення, перегляд та оновлення інвойсів.
- `database/migrations/*create_invoices_table.php` - структура таблиці інвойсів.
- `routes/api.php` - API маршрути.

## API Endpoints

```text
GET    /api/invoices
GET    /api/invoices/{id}
POST   /api/invoices
PUT    /api/invoices/{id}
```

## Правила

- `number` є унікальним.
- `net_amount` має бути більше 0.
- `vat_amount` має бути більше або дорівнювати 0.
- `gross_amount` розраховується на backend як `net_amount + vat_amount`.
- `due_date` не може бути раніше за `issue_date`.
- Оновлювати можна тільки інвойси зі статусом `pending`.
- При оновленні можна змінювати тільки `net_amount`, `vat_amount` і `due_date`.

## Запуск

Запустити Docker-оточення з кореня проєкту:

```bash
docker compose up -d
```

Запустити міграції всередині backend container:

```bash
docker compose exec backend php artisan migrate
```

Backend API:

```text
http://localhost:8000/api
```

Підключення до бази з локальних інструментів:

```text
Host: 127.0.0.1
Port: 5433
Database: invoices_db
User: invoices_user
Password: invoices_password
```

Всередині Docker Laravel підключається до PostgreSQL через:

```text
DB_HOST=db
DB_PORT=5432
```

## Приклад створення інвойсу

```json
{
  "number": "INV-001",
  "supplier_name": "ТОВ Тест Постачальник",
  "supplier_tax_id": "12345678",
  "net_amount": 1000,
  "vat_amount": 200,
  "currency": "UAH",
  "status": "pending",
  "issue_date": "2026-06-16",
  "due_date": "2026-06-30"
}
```

## Компроміси

- API відповіді повертаються напряму з Eloquent моделей і paginator. У production-версії я б додав API Resources для стабільного формату відповідей.
- Статуси інвойсу зберігаються як рядки з константами в моделі.
- Розмір пагінації зафіксований у сервісі, щоб не ускладнювати API.
- Аутентифікація не реалізована, бо вона не потрібна за умовами завдання.

## Що б я покращив у production-версії

- Додав би API Resources для стабільного форматування відповідей.
- Додав би фільтрацію та пошук у списку інвойсів.
- Зробив би строгішу валідацію валюти.
- Додав би авторизацію, якщо інвойси будуть привʼязані до користувачів.
- Додав би логування для важливих бізнес-помилок, де це реально допомагає діагностиці.

## UX edge cases

- Backend повертає повідомлення валідації українською мовою.
- Дублікати номерів інвойсів відхиляються.
- Некоректні дати та відʼємні суми відхиляються.
- Інвойси зі статусом `approved` або `rejected` не можна оновлювати.
- `gross_amount` ніколи не береться з frontend і завжди перераховується на backend.
