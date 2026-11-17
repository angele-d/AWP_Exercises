# Exercise 3 - Laravel Project Management with Multilingual Support

A lightweight Laravel web application for project management featuring role-based permissions (Manager vs. Team Member) and session-based multilingual support (English & Polish).

---

## Technical Stack

- **Framework:** Laravel (v11/v12)
- **Database:** MariaDB / MySQL
- **Frontend:** Laravel Blade + Tailwind CSS (via CDN) + Alpine.js (via CDN)
- **Authentication:** Laravel Breeze

---

## How to Run the Project

### Prerequisites

- PHP >= 8.2
- Composer
- MariaDB or MySQL

### Installation & Setup

1. **Install PHP dependencies:**
```bash
   composer install
```

2. **Configure Environment:**
Ensure your `.env` file is configured with your database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), then generate the application key:
```bash
php artisan key:generate
```


3. **Run Database Migrations:**
```bash
php artisan migrate
```

### Starting the Application

Launch the local development server:

```bash
php artisan serve
```

Access the application in your browser at `http://127.0.0.1:8000`.

> **Note on Frontend Assets:** The application relies directly on Tailwind CSS and Alpine.js CDN scripts included in the layout files (`app.blade.php` and `guest.blade.php`). Running `npm run dev` or Vite is not required.

---

## Test Scenarios

### Scenario 1: Role-Based Access Control (Task 1)

**Step A: User Registration**

1. Navigate to the registration page (`/register`).
2. Register the first user: **Alice** (`alice@test.com`).
3. Log out.
4. Register the second user: **Bob** (`bob@test.com`).

**Step B: Manager Role Testing**

1. Log in using **Alice**'s account.
2. Go to **My Projects** (`/dashboard`) and click **+ New Project**.
3. Fill out the project form (Name, Description, Price, Start Date, End Date) and select **Bob** from the team members list.
4. Submit the form. The project will appear under the **"Projects I Manage (Manager)"** section.
5. Click **Edit (All)** and verify that Alice can edit all fields (Name, Price, Description, Dates, and "Done jobs" status).

**Step C: Team Member Role Testing**

1. Log out of Alice's account and log in using **Bob**'s account.
2. On the dashboard, verify that the project created by Alice appears under the **"Projects I Participate In (Member)"** section.
3. Click **Edit Status**.
4. **Verification:** Bob must **not** have access to edit the Name, Price, Description, or Dates fields. He should only be able to toggle the **"Done jobs"** checkbox and save.

---

### Scenario 2: Multilingual Support (Task 2)

1. While logged in, locate the language switcher (**🇬🇧 EN / 🇵🇱 PL**) in the navigation header.
2. Click **🇵🇱 PL**: verify that the interface text across the dashboard (`/projects`), creation form (`/projects/create`), and edit form (`/projects/{id}/edit`) switches to Polish.
3. Click **🇬🇧 EN**: verify that the interface text switches back to English.
4. Navigate between pages to ensure the language preference persists across the PHP session.

---

## Implementation Summary & Modified Files

### 1. Database & Models

Generated the `Project` model, migration, and `project_user` pivot table:

```bash
php artisan make:model Project -m
php artisan make:migration create_project_user_table
php artisan migrate
```

* `app/Models/Project.php`
* `app/Models/User.php`
* `database/migrations/xxxx_xx_xx_xxxxxx_create_projects_table.php`
* `database/migrations/xxxx_xx_xx_xxxxxx_create_project_user_table.php`

### 2. Logic, Authorization & Routing

Generated the controller and authorization policy:

```bash
php artisan make:controller ProjectController
php artisan make:policy ProjectPolicy --model=Project
```

* `app/Http/Controllers/ProjectController.php`
* `app/Policies/ProjectPolicy.php`
* `routes/web.php`

### 3. Frontend Layouts (CDN Configuration)

Replaced Vite asset directives with Tailwind CSS & Alpine.js CDNs:

* `resources/views/layouts/guest.blade.php`
* `resources/views/layouts/app.blade.php`

### 4. Internationalization (Task 2)

Created locale middleware and translation files:

```bash
php artisan make:middleware SetLocaleMiddleware
php artisan lang:publish
```

* `app/Http/Middleware/SetLocaleMiddleware.php`
* `bootstrap/app.php`
* `lang/en/messages.php`
* `lang/pl/messages.php`

### 5. Application Views

Updated Blade templates with translation keys and language switcher:

* `resources/views/projects/index.blade.php`
* `resources/views/projects/create.blade.php`
* `resources/views/projects/edit.blade.php`

