# NexaTech — Business Portfolio Website

A responsive business portfolio website built to showcase a company's services, projects, and contact information. The project includes an admin panel that allows authorized users to manage website content dynamically.

## Project Overview

NexaTech is a business portfolio website developed as part of my web development training. It demonstrates my practical experience in building dynamic websites, integrating a MySQL database, and implementing content management features using PHP.

## Features

### Public Website
- Responsive design for different screen sizes.
- Home and About sections.
- Dynamic Services section.
- Portfolio section with project categories and filtering.
- Frequently Asked Questions (FAQ) section.
- Contact section with editable company information.

### Admin Panel
- Admin login and logout.
- Dashboard for managing website content.
- Create, edit, and delete portfolio projects.
- Manage website sections and services.
- Manage frequently asked questions.
- Activate and deactivate supported content.
- Update company contact information.
- Store and retrieve content using MySQL.

## Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Local Development:** XAMPP and Apache
- **Database Management:** phpMyAdmin
- **Version Control:** Git and GitHub

## Installation and Setup

1. Install [XAMPP](https://www.apachefriends.org/).
2. Clone or download this repository into your XAMPP `htdocs` directory.
3. Start Apache and MySQL from the XAMPP Control Panel.
4. Open phpMyAdmin at `http://localhost/phpmyadmin`.
5. Create a database named `business_portfolio_db`.
6. Import `business_portfolio_db.sql` into the database.
7. Configure your local database connection in `includes/db.php`. This file is excluded from the public repository, so create or configure it locally if needed.
8. Open the website at `http://localhost/business-portfolio/`.

## Project Structure

```text
business-portfolio/
├── admin/                  # Admin panel and content management
├── assets/
│   ├── css/                # Stylesheets
│   └── images/             # Project images
├── includes/               # Database connection configuration
├── index.php               # Public website
├── business_portfolio_db.sql
├── .gitignore
└── README.md
```

## What I Learned

- Building dynamic websites using PHP and MySQL.
- Connecting PHP applications to a relational database.
- Implementing CRUD operations for website content.
- Managing project images and database records.
- Organizing project files and using Git and GitHub.
- Testing and debugging a local web application with XAMPP.

## Developer

**Sally Kadi**

- GitHub: [sally-kadi](https://github.com/sally-kadi)
- LinkedIn: [Sally Kadi](https://www.linkedin.com/in/sally-kadi/)

---

*Developed as a web development training project.*
