# Invoice Management

Тестовий full-stack модуль обліку інвойсів.
## Стек

- Frontend: Nuxt 4, Vue 3.5, TypeScript, TailwindCSS 4, vee-validate + zod
- Backend: PHP 8.3, Laravel 13
- БД: PostgreSQL 16

## Запуск

```bash
docker compose up -d --build
docker compose exec backend php artisan migrate
```

- Фронт: http://localhost:3000
- API: http://localhost:8000/api
- БД зовні: `127.0.0.1:5433`, юзер `invoices_user`, пароль `invoices_password`, db `invoices_db`

Всередині docker-мережі Laravel ходить до бази по `db:5432`.

## Backend

### Структура

```
backend/app/
├── Models/Invoice.php                       модель + константи статусів
├── Http/Controllers/InvoiceController.php   тонкий контролер
├── Http/Requests/StoreInvoiceRequest.php    валідація створення
├── Http/Requests/UpdateInvoiceRequest.php   валідація оновлення
└── Services/InvoiceService.php              створення / список / оновлення

backend/database/migrations/*create_invoices_table.php
backend/routes/api.php
```

### API

```
GET    /api/invoices
GET    /api/invoices/{id}
POST   /api/invoices
PUT    /api/invoices/{id}
```

### Правила

- `number` унікальний.
- `net_amount > 0`, `vat_amount >= 0`.
- `gross_amount` рахується на беку як `net_amount + vat_amount` — з фронту не приймається.
- `due_date >= issue_date`.
- Оновлювати можна тільки інвойси зі статусом `pending`. У PUT приймаються тільки `net_amount`, `vat_amount`, `due_date`.

### Приклад створення

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

## Frontend

### Структура

```
frontend/app/
├── assets/css/main.css         tailwind entry
├── components/
│   ├── BaseModal.vue           обгортка модалки (teleport, overlay, lock scroll)
│   ├── InvoiceStatusBadge.vue  бейдж статусу
│   ├── InvoiceCreateModal.vue  форма створення
│   └── InvoiceEditModal.vue    форма редагування
├── composables/useInvoicesApi.ts
├── pages/
│   ├── index.vue               редірект на /invoices
│   └── invoices/
│       ├── index.vue           список + пагінація + модалка створення
│       └── [id].vue            деталі + модалка редагування
├── types/invoice.ts
└── utils/format.ts             форматтери дати і грошей
```

## Відповіді на запитання з ТЗ

### Як структурував frontend і backend

Бек робив за стандартною Laravel структурою: контролер тримаю тонким, валідацію виніс у FormRequest, бізнес-логіку — у сервіс. Авторозрахунок `gross_amount` і перевірку, що оновлювати можна тільки `pending`-інвойси, тримаю в сервісі.

На фронті — файловий роутинг Nuxt. Сторінки в `pages/`, спільне розкидав по `components/`, `composables/` і `utils/`. Типи з бекенду лежать в одному файлі `types/invoice.ts`, щоб не плодити їх по компонентах. Модалки створення і редагування винесені окремо.

### Які компроміси

Створення інвойсу робив через модалку, а не окрему сторінку.

На беку відповіді віддаю напряму з Eloquent моделей і paginator-а, без API Resources. 

Один тип `Invoice` повторює відповідь API один-в-один, без окремого DTO. Decimal-поля тримаю як `string`, у число конвертую тільки у формі.

Двосторонній `baseURL` у `runtimeConfig` — `apiBase` для SSR (ходить до `backend:8000` через docker network) і `public.apiBase` для клієнта (`localhost:8000`).

Пагінація мінімальна: prev/next + "сторінка X з Y".

Аутентифікацію не робив, бо її не було в ТЗ.

### Що б покращив у production

Найочевидніше — додав би API Resources на беку.

На беку ще додав би: фільтри і пошук у списку інвойсів, строгішу валідацію валюти, логування важливих бізнес-помилок, авторизацію (інвойси прив'язані до користувачів).

На фронті: обгортку над `$fetch` з єдиною обробкою помилок (тости, 401/403, retry для мережевих), i18n замість захардкоджених укр-рядків, тести (Vitest на composables, Playwright на e2e), глобальний error boundary і моніторинг.

### UX edge cases

- Валідаційні повідомлення з бекенду — українською.
- Дублікати номерів, відʼємні суми, некоректні дати — відхиляються на беку.
- Інвойси `approved` / `rejected` оновити не можна.
- `gross_amount` ніколи не береться з фронту — завжди перераховується на беку.

- Помилки валідації з бекенду (422) розкидаю по полях через `setErrors`.
- `due_date >= issue_date` перевіряю двічі: у zod-схемі і атрибутом `min` на інпуті.
- `gross_amount` у формі — readonly і рахується на льоту.
- Після створення стрибаю на 1-у сторінку, бо новий запис іде першим (`created_at desc`).
- Список і деталі через `useAsyncData` — на hard reload нема "блимання" порожнім станом.

## Як розгорнути проєкт з нуля

Потрібен встановлений Docker і Docker Compose.

1. Клонувати репозиторій і зайти в папку:

   ```bash
   git clone <repo-url> invoice-management
   cd invoice-management
   ```

2. Підняти контейнери (бек, фронт, базу):

   ```bash
   docker compose up -d --build
   ```

   Перший білд тягне base images і ставить залежності — займе кілька хвилин.

3. Виконати міграції бази:

   ```bash
   docker compose exec backend php artisan migrate
   ```

4. Відкрити в браузері:

   - http://localhost:3000 — фронт
   - http://localhost:8000/api/invoices — API напряму

### Корисні команди

```bash
# логи фронта / бека / бази
docker compose logs -f frontend
docker compose logs -f backend
docker compose logs -f db

# перезапустити окремий сервіс
docker compose restart frontend

# зупинити все
docker compose down

# зупинити і прибрати volume з даними бази (повне очищення)
docker compose down -v
```

### Якщо щось зламалось

- **Фронт не бачить нові npm-пакети** — перебудувати образ: `docker compose up -d --build frontend`.
- **`Cannot find module ...` після зміни `package.json`** — анонімний volume `/app/node_modules` тримає старий стан. Допомагає `docker compose down` + `docker compose up -d --build`.
- **CSS не підтягуються після додавання нових файлів** — Tailwind 4 кешує контент, ребутни фронт: `docker compose restart frontend`.
- **Порт зайнятий** — змінити маппінг у `docker-compose.yml` (`3000`, `8000`, `5433`).
