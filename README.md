# NexaTech — Business Portfolio Website

A responsive, database-driven business portfolio website built with PHP and MySQL. NexaTech helps businesses present their services, projects, and company information through a professional website, with an admin panel for managing supported content.

## Project Overview

NexaTech was developed as part of my web development training at Violet Programming. The project demonstrates my practical experience in building dynamic PHP websites, integrating relational databases, and implementing content management functionality.

The website separates the public-facing experience from the administration interface, allowing visitors to explore business information while authorized users manage website content.

## Key Features

### Public Website

- Responsive layout for different screen sizes.
- Home and About sections.
- Dynamic Services section.
- Portfolio gallery with project categories and filtering.
- Frequently Asked Questions (FAQ) section.
- Contact section displaying company information.

### Admin Panel

- Admin login and logout.
- Dashboard for website content management.
- Create, edit, and delete portfolio projects.
- Manage website sections and services.
- Manage frequently asked questions.
- Activate and deactivate supported content.
- Update company contact information.
- Store and retrieve website content through MySQL.

## My Contribution

I developed the website using PHP, HTML, CSS, JavaScript, and MySQL. My work included:

- Building and styling the public website interface.
- Connecting dynamic website sections and services to database records.
- Implementing portfolio filtering and navigation.
- Developing administrative content management functionality.
- Organizing project files and troubleshooting database and layout issues.

## Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL, SQL
- **Database Management:** phpMyAdmin
- **Local Development:** XAMPP, Apache
- **Version Control:** Git, GitHub

## Project Structure

```text
business-portfolio/
├── admin/
├── assets/
│   ├── css/
│   └── images/
├── includes/
│   └── db.php
├── index.php
├── business_portfolio_db.sql
├── .gitignore
└── README.md
```

*The structure above summarizes the main project files and directories.*

## Installation and Setup

### Prerequisites

- XAMPP with PHP and MySQL.
- Apache and MySQL services.
- phpMyAdmin.

### Steps

1. Install [XAMPP](https://www.apachefriends.org/).
2. Clone or download this repository into your XAMPP `htdocs` directory.
3. Start Apache and MySQL from the XAMPP Control Panel.
4. Open phpMyAdmin at `http://localhost/phpmyadmin`.
5. Create a database named `business_portfolio_db`.
6. Import the `business_portfolio_db.sql` file.
7. Configure your local database connection in `includes/db.php`. If the file is excluded from the repository, create it locally using your own database credentials.
8. Open the website at `http://localhost/business-portfolio/`.

**Security note:** Do not commit database passwords or other private credentials to a public repository.

## Screenshots

Screenshots of the public website and admin panel can be added here to demonstrate the interface and content management features.

## What I Learned

- Building dynamic web applications with PHP and MySQL.
- Integrating PHP pages with a relational database.
- Implementing CRUD operations for website content.
- Managing dynamic sections, services, and portfolio records.
- Creating responsive layouts and interactive portfolio filtering.
- Organizing code and debugging a local application with XAMPP.
- Using Git and GitHub for version control.

## Developer

**Sally Kadi**

- **GitHub:** [sally-kadi](https://github.com/sally-kadi)
- **LinkedIn:** [Sally Kadi](https://www.linkedin.com/in/sally-kadi/)
- **Email:** [Contact Me](mailto:sallykadi888@gmail.com)

## Related Portfolio

Explore my other projects and development experience through my personal portfolio.

[Personal GitHub Profile](https://github.com/sally-kadi)

---

*Developed as a web development training project.*

© 2026 Sally Kadi
