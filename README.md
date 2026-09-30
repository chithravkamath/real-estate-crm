# Real Estate CRM System

A web-based **Real Estate Customer Relationship Management (CRM) System** developed using **Laravel, PHP, MySQL, Blade, Bootstrap, HTML, CSS, and JavaScript**.

The system is designed to help real estate consultants manage properties, clients, leads, site visits, deals, billing, reports, users, reminders, and client interactions through a centralized web application.

## Project Overview

The Real Estate CRM System provides role-based access for different users involved in real estate operations.

The application includes an administrative management panel and a client portal. It helps manage the complete workflow from property and lead management to site visits, deals, billing, and client services.

## Features

### 1. Authentication & Role-Based Access

- User authentication and login
- Role-based access control
- Admin role
- Agent role
- Accountant role
- Client role
- Role-specific dashboard redirection
- Protected modules using authorization middleware

### 2. Dashboard

- Dashboard overview
- Property statistics
- Client statistics
- Lead information
- Deal information
- Billing information
- Role-specific dashboards

### 3. Property Management

- Add properties
- Edit properties
- View property details
- Delete properties
- Property status management
- Property search and filtering
- Property images
- Property documents
- Property videos

### 4. Client Management

- Add clients
- Edit client information
- View client profiles
- Delete clients
- Client search and filtering
- VIP client filtering
- Client-property interaction
- Client-related site visits

### 5. Lead Management

- Create and manage leads
- Lead status tracking
- Lead search and filtering
- Lead follow-up management
- Lead assignment

### 6. Site Visit Management

- Schedule site visits
- Manage site visit information
- Associate site visits with clients
- Track visit status
- Client site-visit requests
- Manage site visits from the admin panel

### 7. Deal Management

- Create and manage deals
- Track deal status
- Agent information
- Deal updates
- Booking and sale information
- Commission-related information

### 8. Billing & Payment Management

- Create and manage billing records
- Advance payment tracking
- Final payment tracking
- Due amount tracking
- Payment status management
- Invoice generation
- Commission-related information

### 9. Reports

- Generate CRM reports
- Search and filter report data
- View business-related information
- Generate PDF reports

### 10. User Management

- Add users
- Edit users
- View users
- Delete users
- Manage user roles
- Role-based access management

### 11. Client Portal

Clients can:

- Log in securely
- View available properties
- View property details
- View bookings
- View payment information
- View updates
- Request site visits
- Access client-specific information

### 12. Audit Logs

- Record important system activities
- Track user actions
- Maintain activity records

### 13. Reminders

- Create reminders
- Manage reminders
- Track follow-up activities

### 14. Communication Records

- Record communication activities
- Manage communication information
- Maintain client-related communication records

## User Roles

The system provides different functionality based on the user's role.

| Role | Main Responsibilities |
|------|-----------------------|
| Admin | Manage users, properties, clients, leads, deals, billing and reports |
| Agent | Manage leads, clients, properties and site visits |
| Accountant | Manage billing and payment-related information |
| Client | View properties, bookings, payments and updates |

## Technology Stack

| Technology | Purpose |
|------------|---------|
| Laravel | Backend web framework |
| PHP | Server-side programming |
| MySQL | Database |
| Blade | Server-side templating |
| Bootstrap 3.4 | User interface and responsive design |
| HTML5 | Web page structure |
| CSS3 | Styling |
| JavaScript | Client-side functionality |
| XAMPP | Local development environment |
| VS Code | Development environment |
| Git | Version control |
| GitHub | Source code repository |

## Architecture

The application follows the **Laravel MVC (Model-View-Controller) architecture**.

### Model

Handles database entities, relationships, and data interaction.

### View

Uses Laravel Blade templates to provide the user interface.

### Controller

Handles application requests, business logic, validation, and communication between models and views.

### Routes

Defines the application's web routes and module access.

### Middleware

Handles authentication and role-based authorization.

### Migrations

Manages the structure of the MySQL database.

## Main Modules

The system includes the following major modules:

1. Authentication & Role-Based Access
2. Dashboard
3. Property Management
4. Client Management
5. Lead Management
6. Site Visit Management
7. Deal Management
8. Billing & Commission Management
9. Reports
10. User Management
11. Client Portal
12. Audit Logs
13. Reminders
14. Communication Records

## Project Structure

```text
real-estate-crm/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Mail/
│   ├── Models/
│   └── Providers/
│
├── bootstrap/
│
├── config/
│
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│
├── public/
│   ├── property-images/
│   └── ...
│
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│
├── routes/
│
├── storage/
│
├── tests/
│
├── .env.example
├── artisan
├── composer.json
├── composer.lock
├── package.json
└── README.md

## Internship Project

This Real Estate CRM System was developed as part of my **Software Engineer Internship at Udupi Web Solutions**.

The project involved developing a web-based CRM application for managing real estate properties, clients, leads, site visits, deals, billing, reports, and client interactions using Laravel, PHP, MySQL, Bootstrap, HTML, CSS, and JavaScript.

## Author

**Chithra V Kamath**

MCA Graduate

GitHub: https://github.com/chithravkamath
