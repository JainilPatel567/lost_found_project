# 🔍 Lost & Found Portal

A web-based platform designed for college communities to report lost items, claim found belongings, and resolve active listings efficiently[cite: 19]. Built as part of a PHP & SQL project.

---

## 🚀 Features

*   **User Registration & Authentication**: Secure registration with server-side validation (including strict 10-digit phone verification, email uniqueness checks, and secure password hashing), login, session handling, and logout functionality[cite: 20, 21, 24].
*   **Item Posting**: Users can post either **Lost** or **Found** items, complete with detailed descriptions, location tags, and optional image uploads[cite: 23].
*   **Search & Filter Bar**: Instantly filter posts by type (Lost/Found) or search items by keywords and locations[cite: 17].
*   **Dashboard & Statistics**: Real-time counter metrics tracking active lost items, found items, resolved listings, and total visible posts[cite: 17].
*   **Post Management (`My Posts`)**: Dedicated management page allowing users to resolve active posts, re-open items, or delete posts along with associated uploads[cite: 22].
*   **Secure Image Uploading**: Strict MIME-type checking and file size limits (max 2MB) for secure image attachments[cite: 23].

---

## 🛠️ Technology Stack

*   **Backend**: PHP (Data Objects / MySQLi with Prepared Statements)[cite: 17, 23, 24]
*   **Database**: MySQL / MariaDB (phpMyAdmin)
*   **Frontend**: HTML5, CSS3, Responsive Layouts[cite: 19, 20, 24]

---

## 📁 Project Structure

```text
├── css/
│   └── style.css            # Global stylesheet
├── includes/
│   ├── auth.php             # Authentication checks[cite: 17, 19, 20, 21, 22, 23]
│   └── config.php           # Database connection configuration[cite: 17, 20, 21, 23, 24]
├── uploads/                 # User-uploaded item images directory[cite: 23]
├── dashboard.php            # Main dashboard showing active community posts[cite: 17]
├── index.php                # Landing page for unauthenticated visitors[cite: 19]
├── login.php                # User login page[cite: 20]
├── logout.php               # Session destruction script[cite: 21]
├── my_posts.php             # User post management (resolve, re-open, delete)[cite: 22]
├── post_item.php            # Form to submit a new lost/found item[cite: 23]
├── register.php             # User account registration page[cite: 24]
└── README.md                # Project documentation

⚙️ Setup and Installation
1.Clone the Repository
git clone [https://github.com/your-username/lost-and-found-portal.git](https://github.com/your-username/lost-and-found-portal.git)

2.Configure Database
Import the provided SQL schema into your MySQL/MariaDB database (e.g., via phpMyAdmin). Name the database lost_found_portal[cite: 18].
Update your database connection settings in includes/config.php if required.

3.Run Locally
Place the project directory inside your local server root (such as htdocs for XAMPP).
Start Apache and MySQL from your control panel.
Open your browser and navigate to:
http://localhost/lost-and-found-portal/index.php

📄 License & Credits
Developed as a PHP & SQL project. All rights reserved.
