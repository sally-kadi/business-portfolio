# NexaTech — Business Portfolio Website

A responsive, database-driven business portfolio website built with PHP, MySQL, HTML, CSS, and JavaScript.

Developed as part of my Web Development Internship at Violet Programming.

## Project Overview

NexaTech is a business portfolio website designed to present company information, services, portfolio projects, frequently asked questions, and contact details.

The website includes a dynamic public interface and an administrative dashboard for managing website content.

## Features

### Public Website
- Responsive homepage and navigation
- Dynamic Home and About sections
- Services section powered by MySQL
- Portfolio gallery with category filters
- Frequently Asked Questions (FAQ)
- Dynamic company contact information
- Database-driven content

### Admin Dashboard
- Admin login and logout
- Session-based access protection
- Dashboard statistics for sections, services, portfolio projects, and FAQs
- Manage website sections
- Add, edit, delete, and activate or deactivate services
- Add, edit, delete, and activate or deactivate portfolio projects
- Manage frequently asked questions
- Update company contact information
- CSRF protection for login and logout requests

## Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- XAMPP
- phpMyAdmin
- Git and GitHub

## Database Structure

The project uses a MySQL database named `business_portfolio_db`.

Main tables:

- `admin_users` — Stores administrator account information.
- `sections` — Stores website section content and settings.
- `services` — Stores business services and display order.
- `portfolio_items` — Stores portfolio project information, categories, images, and status.
- `faqs` — Stores frequently asked questions and answers.
- `company_info` — Stores company contact information.

The database export is included in `business_portfolio_db.sql`.

## Installation and Setup

### Requirements

- XAMPP
- PHP
- MySQL or MariaDB
- A web browser

### Steps

1. Install and open XAMPP.
2. Start Apache and MySQL.
3. Clone or download this repository.
4. Place the project folder inside `C:\xampp\htdocs\`.
5. Open phpMyAdmin at `http://localhost/phpmyadmin/`.
6. Create a database named `business_portfolio_db`.
7. Select the database and import `business_portfolio_db.sql`.
8. Check the database connection settings in `includes/db.php`.
9. Start Apache and MySQL in XAMPP.
10. Open the website in your browser.

### Website URL

`http://localhost/business-portfolio/`

### Admin Login

`http://localhost/business-portfolio/admin/login.php`

Administrator credentials must correspond to an account stored in the `admin_users` table. Use the credentials configured for your local project.

## My Contribution

During my internship at Violet Programming, I worked on:

- Developing the responsive website interface.
- Integrating PHP with MySQL.
- Building dynamic website sections.
- Developing the services and portfolio interfaces.
- Implementing the FAQ section.
- Creating administrative content management features.
- Implementing login and logout functionality.
- Debugging PHP and database errors.
- Improving layouts and responsiveness.
- Using Git and GitHub to manage project changes.

## Technical Challenges

### Database Integration

Connected PHP pages to MySQL to retrieve and manage website content dynamically.

### Content Management

Implemented administrative pages for managing services, portfolio projects, FAQs, sections, and company information.

### Authentication and Session Management

Implemented administrator authentication, session handling, and protected admin pages.

### Responsive Design

Improved CSS layouts and spacing to provide a consistent experience across different screen sizes.

## What I Learned

- PHP and MySQL integration
- Database-driven website development
- CRUD operations
- Responsive CSS design
- Admin dashboard development
- Authentication and session management
- Debugging and troubleshooting
- Git and GitHub workflows

## Project Status

Completed as an internship project.

The application runs locally using XAMPP, PHP, and MySQL.

## Developer

**Sally Kadi**

Junior PHP Full-Stack Developer | PHP, MySQL & JavaScript

- Portfolio: https://sally-kadi.github.io/sally-portfolio/
- GitHub: https://github.com/sally-kadi
- LinkedIn: https://www.linkedin.com/in/sally-kadi/
