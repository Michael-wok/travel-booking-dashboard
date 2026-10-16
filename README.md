# Travel Booking Management Dashboard

A full-stack web application for managing travel bookings, built with Vue 3, PHP, and MySQL.

This project demonstrates frontend development, backend API integration, database operations, and version control using Git and GitHub.

## Features

- **View bookings** — Display booking records from a MySQL database.
- **Search bookings** — Find bookings by ID or customer name.
- **Create bookings** — Add new bookings with customer and destination details.
- **Edit bookings** — Update existing booking information.
- **Manage booking status** — Change bookings between Pending and Confirmed.
- **Delete bookings** — Remove bookings from the database.
- **Persistent storage** — Save changes to MySQL so they remain after refreshing the page.

## Tech Stack

| Technology | Purpose |
|---|---|
| Vue 3 | Frontend user interface |
| JavaScript | Frontend logic and API requests |
| PHP | Backend API |
| MySQL | Relational database |
| PDO | PHP database connectivity |
| Vite | Frontend development server |
| Git and GitHub | Version control and source code hosting |

## Project Structure

```text
travel-booking-dashboard/
├── backend/
│   ├── config.example.php
│   ├── config.local.php
│   ├── db.php
│   └── bookings.php
├── src/
│   ├── assets/
│   ├── components/
│   ├── App.vue
│   ├── main.js
│   └── style.css
├── public/
├── index.html
├── package.json
├── package-lock.json
└── README.md
```

`config.local.php` contains local database credentials and should not be committed to Git.

## Getting Started

### Prerequisites

Install the following software:

- Node.js and npm
- PHP with PDO MySQL support
- MySQL Server
- Git

### 1. Clone the repository

```bash
git clone https://github.com/Michael-wok/travel-booking-dashboard.git
cd travel-booking-dashboard
```

### 2. Install frontend dependencies

```bash
npm install
```

### 3. Set up the MySQL database

Open MySQL and create the database and bookings table:

```sql
CREATE DATABASE travel_booking;

USE travel_booking;

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending'
);
```

Optionally, insert sample records:

```sql
INSERT INTO bookings (customer, destination, status)
VALUES
    ('Alex Chen', 'Tokyo', 'Confirmed'),
    ('Sarah Lee', 'Melbourne', 'Pending'),
    ('David Wong', 'Singapore', 'Confirmed');
```

Create a MySQL user with appropriate permissions for the application, rather than using the database root account.

### 4. Configure the backend

Copy `backend/config.example.php` to `backend/config.local.php`.

Update `config.local.php` with your own local MySQL credentials. Do not commit this file or share database passwords publicly.

### 5. Start the PHP backend

From the project root, run:

```bash
php -S 127.0.0.1:8000 -t backend
```

The backend API will be available at:

`http://127.0.0.1:8000/bookings.php`

### 6. Start the Vue frontend

Open a second terminal window in the project directory and run:

```bash
npm run dev
```

Open the local URL shown by Vite, usually:

`http://localhost:5173`

Keep both development servers running while using the application.

## API Endpoints

The backend exposes a single endpoint: `/bookings.php`.

| Method | Operation | Description |
|---|---|---|
| GET | Read | Retrieve all bookings |
| POST | Create | Add a new booking |
| PUT | Update | Edit a booking's customer and destination |
| PATCH | Update | Change a booking's status |
| DELETE | Delete | Remove a booking |

The API accepts JSON request bodies for write operations and returns JSON responses.

## Learning Objectives

This project was developed to practise:

- Building interactive interfaces with Vue 3.
- Managing application state and form inputs.
- Connecting a frontend to a PHP backend using HTTP requests.
- Implementing CRUD operations with MySQL.
- Using PDO prepared statements for database queries.
- Organising a project with Git and GitHub.

## Future Improvements

- Improve responsive design and dashboard styling.
- Add stronger form validation and user feedback.
- Add booking filters and pagination.
- Add automated tests.
- Deploy the application for public demonstration.

## Author
Michael
Developed as a full-stack web development portfolio project.
