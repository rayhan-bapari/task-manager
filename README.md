# Task Management System

A simple task management system built with Laravel to help teams organize daily work.

## Features

- Create, read, update, and delete tasks
- Track task status (Pending, In Progress, Completed)
- Set due dates for tasks
- Clean and intuitive web interface using Tailwind css
- RESTful API endpoints
- Comprehensive test coverage

## Technologies Used

- **Backend**: Laravel 13.x
- **Database**: Mysql
- **Frontend**: Blade templates with Tailwind css
- **Testing**: PHPUnit
- **PHP**: 8.4

## Setup Instructions

1. **Clone the repository**

2. **Install dependencies**:

    ```bash
    composer install
    ```

3. **Environment setup**:
    - Copy `.env.example` to `.env`
    - Generate application key:
        ```bash
        php artisan key:generate
        ```

4. **Database setup**:
    - The project uses SQLite for simplicity.
    - Run migrations:
        ```bash
        php artisan migrate
        ```

5. **Start the development server**:
    ```bash
    composer run dev
    ```
    The application will be available at `http://localhost:8000`

## Usage

- Visit the home page to view all tasks
- Click "Create New Task" to add a new task
- Use the Edit and Delete buttons to manage existing tasks
- Tasks can have statuses: Pending, In Progress, or Completed
- Optional due dates can be set for tasks

## Testing

Run the test suite to ensure everything works correctly:

```bash
php artisan test
```

The tests cover:

- Viewing tasks index
- Creating new tasks
- Viewing individual tasks
- Updating tasks
- Deleting tasks
- Validation (required fields, valid status values)

## Assumptions and Decisions

- Used Mysql for database to keep setup simple and portable
- Implemented a basic web interface with Blade templates and Tailwind CSS for styling
- Focused on core CRUD operations without authentication (can be added later)
- Used resource controllers for clean RESTful routing
- Added basic validation for required fields and status enum
- Included timestamps for created/updated tracking
- Made description and due_date optional fields
