# CMSC126 Study Session Management System

A comprehensive web-based study session management system designed for university students to organize, schedule, and manage collaborative study sessions. Built as the final project for CMSC126 (Web-based Systems) course.

## 📚 Table of Contents

- [Project Overview](#-project-overview)
- [Features](#-features)
- [Technology Stack](#-technology-stack)
- [Database Schema](#-database-schema)
- [Installation & Setup](#-installation--setup)
- [Usage Guide](#-usage-guide)
- [API Documentation](#-api-documentation)
- [Project Structure](#-project-structure)
- [Team Roles](#-team-roles)
- [Development Approach](#-development-approach)
- [Security Features](#-security-features)
- [Contributing](#-contributing)

## 🎯 Project Overview

The **Study Session Management System** is a full-stack web application that enables university students to create, manage, and participate in collaborative study sessions. The system provides a centralized platform for organizing study groups, scheduling review sessions, and tracking attendance across various academic subjects.

### Key Objectives
- **Centralized Study Organization**: Provide a unified platform for managing study sessions across different subjects
- **Enhanced Collaboration**: Enable students to discover and join study sessions based on their course enrollment
- **Efficient Scheduling**: Streamline the process of scheduling and managing study sessions with conflict detection
- **User-Friendly Interface**: Deliver an intuitive, responsive design that works across devices
- **Administrative Control**: Provide administrative oversight for session and user management

## ✨ Features

### 🔐 Authentication & User Management
- **Secure Registration/Login**: Password hashing with Argon2ID encryption
- **Role-Based Access Control**: User and Admin roles with different permissions
- **Session Management**: Secure session handling with CSRF protection
- **User Profiles**: Personalized user profiles with course enrollment information

### 📅 Study Session Management
- **Create Sessions**: Users can create detailed study sessions with:
  - Title, subject, and topic information
  - Date and time scheduling
  - Location details
  - Comprehensive descriptions
- **Session Discovery**: Browse and filter sessions by subject and date
- **Session Status Tracking**: Monitor session states (scheduled, ongoing, completed, cancelled)
- **Permission-Based Actions**: Session creators can edit and delete their sessions

### 📊 Dashboard & Analytics
- **Personal Dashboard**: Overview of upcoming sessions and statistics
- **Real-time Statistics**: 
  - Total sessions count
  - Upcoming sessions
  - Subject distribution
  - Attendance tracking
- **Activity Feed**: Recent session activities and updates

### 🎨 User Interface
- **Modern Responsive Design**: Mobile-first approach with clean, intuitive interface
- **Interactive Components**: Modal dialogs, dropdown menus, and toast notifications
- **Accessibility Features**: Screen reader support and keyboard navigation
- **Dark/Light Theme Support**: User preference-based theming

### 👑 Administrative Features
- **User Management**: View, monitor, and delete user accounts
- **Session Oversight**: Monitor all study sessions across the platform
- **Course Management**: Manage available courses and subjects
- **System Analytics**: Platform-wide usage statistics

## 🛠 Technology Stack

### Backend
- **PHP 8.2+**: Server-side logic and application framework
- **MySQL 8.0+**: Relational database management
- **PDO (PHP Data Objects)**: Database abstraction layer with prepared statements
- **MVC Architecture**: Model-View-Controller design pattern

### Frontend
- **HTML5**: Semantic markup structure
- **CSS3**: Modern styling with Flexbox and Grid
- **Vanilla JavaScript**: Interactive functionality without external frameworks
- **Feather Icons**: Lightweight icon library
- **Responsive Design**: Mobile-first CSS methodology

### Development Environment
- **XAMPP**: Local development stack (Apache, MySQL, PHP)
- **MariaDB**: MySQL-compatible database server
- **Apache**: Web server with mod_rewrite support

### Security Implementation
- **CSRF Protection**: Cross-Site Request Forgery prevention
- **SQL Injection Prevention**: Parameterized queries and input sanitization
- **XSS Protection**: Output escaping and input validation
- **Password Security**: Argon2ID hashing algorithm
- **Session Security**: Secure session configuration

## 🗃 Database Schema

The application uses a relational database with the following core entities:

### Tables Overview

**Users Table (`user`)**
```sql
- userID (Primary Key, Auto Increment)
- userName (Unique, VARCHAR(50))
- email (Unique, VARCHAR(50))
- password (VARCHAR(255) - Hashed)
- courseID (Foreign Key to courses)
- role (ENUM: 'user', 'admin')
```

**Courses Table (`courses`)**
```sql
- courseID (Primary Key, Auto Increment)
- courseName (VARCHAR(100))
```

**Subjects Table (`subjects`)**
```sql
- subjectID (Primary Key, Auto Increment)
- subjectName (VARCHAR(100))
- courseID (Foreign Key to courses)
```

**Review Sessions Table (`reviewsession`)**
```sql
- reviewSessionID (Primary Key, Auto Increment)
- creatorUserID (Foreign Key to user)
- subjectID (Foreign Key to subjects)
- reviewTitle (VARCHAR(50))
- reviewDate (DATE)
- reviewStartTime (TIME)
- reviewEndTime (TIME)
- reviewLocation (VARCHAR(200))
- reviewDescription (VARCHAR(200))
- reviewTopic (VARCHAR(200))
- reviewStatus (ENUM: 'scheduled', 'ongoing', 'completed', 'cancelled')
```

### Entity Relationships
- **Users** belong to **Courses** (Many-to-One)
- **Subjects** belong to **Courses** (Many-to-One)
- **Review Sessions** are created by **Users** (Many-to-One)
- **Review Sessions** are associated with **Subjects** (Many-to-One)

## 🚀 Installation & Setup

### Prerequisites
- **XAMPP** (or similar local server environment)
- **PHP 8.2** or higher
- **MySQL 8.0** or higher
- **Web browser** (Chrome, Firefox, Safari, Edge)

### Step-by-Step Installation

1. **Clone the Repository**
   ```bash
   git clone [repository-url]
   cd cmsc126-study-session-management-system
   ```

2. **Set Up Local Server**
   - Install and start XAMPP
   - Place project folder in `htdocs` directory
   - Start Apache and MySQL services

3. **Database Setup**
   ```bash
   # Access phpMyAdmin (http://localhost/phpmyadmin)
   # Create new database: cmsc126_database
   # Import database/cmsc126_database.sql
   ```

4. **Configuration**
   - Verify database credentials in `app/config/db_connection.php`
   - Default settings:
     ```php
     $db_host = "localhost";
     $db_name = "cmsc126_database";
     $db_user = "root";
     $db_pass = "";
     ```

5. **Access Application**
   - Navigate to `http://localhost/cmsc126-study-session-management-system/public/`
   - Register a new account or use existing credentials

### Default Admin Account
- **Username**: `admin`
- **Email**: `admin@example.com`
- **Password**: `admin123`

## 📖 Usage Guide

### For Students

1. **Registration**
   - Visit the registration page
   - Fill in required information including course selection
   - Verify email and complete profile setup

2. **Creating Study Sessions**
   - Navigate to Dashboard
   - Click "Add Session" button
   - Fill in session details (title, subject, date, time, location)
   - Submit to create the session

3. **Finding Sessions**
   - Browse sessions on the main dashboard
   - Use filters to find sessions by subject or date
   - View detailed session information

4. **Managing Your Sessions**
   - View sessions you've created in your profile
   - Edit session details as needed
   - Cancel sessions if necessary

### For Administrators

1. **User Management**
   - Access admin dashboard
   - View all registered users
   - Remove problematic accounts when necessary

2. **Session Oversight**
   - Monitor all study sessions
   - Remove inappropriate sessions
   - Generate usage reports

3. **System Maintenance**
   - Monitor database performance
   - Manage course and subject offerings
   - Review system logs

## 📡 API Documentation

### Authentication Endpoints

**POST** `/public/login`
- **Purpose**: User authentication
- **Body**: `username`, `password`
- **Response**: Session data or error message

**POST** `/public/register`
- **Purpose**: User registration
- **Body**: `username`, `email`, `password`, `confirmPassword`, `courseID`
- **Response**: Success confirmation or validation errors

### Session Management Endpoints

**GET** `/public/api/dashboard/stats`
- **Purpose**: Retrieve dashboard statistics
- **Authentication**: Required
- **Response**: Session counts, subjects, attendance data

**POST** `/public/create-session.php`
- **Purpose**: Create new study session
- **Authentication**: Required
- **Body**: Session details (title, subject, date, time, location, etc.)
- **Response**: Session creation confirmation

**POST** `/public/update-session.php`
- **Purpose**: Update existing session
- **Authentication**: Required (session creator only)
- **Body**: Session ID and updated fields
- **Response**: Update confirmation

**POST** `/public/delete-session.php`
- **Purpose**: Delete study session
- **Authentication**: Required (session creator or admin)
- **Body**: `reviewSessionID`, `csrf_token`
- **Response**: Deletion confirmation

### Data Formats

All API responses follow this structure:
```json
{
  "success": boolean,
  "message": "string",
  "data": object,
  "errors": array
}
```

## 📁 Project Structure

```
cmsc126-study-session-management-system/
├── app/
│   ├── config/
│   │   ├── db_connection.php      # Database configuration
│   │   └── init.php               # Application initialization
│   ├── controllers/
│   │   ├── AuthController.php     # Authentication logic
│   │   ├── DashboardController.php # Dashboard management
│   │   ├── StudySessionController.php # Session CRUD operations
│   │   └── AdminController.php    # Administrative functions
│   ├── core/
│   │   ├── Controller.php         # Base controller class
│   │   ├── Model.php             # Base model class
│   │   └── Router.php            # URL routing system
│   ├── Models/
│   │   ├── User.php              # User data model
│   │   ├── StudySession.php      # Session data model
│   │   └── CourseModel.php       # Course/Subject model
│   └── views/
│       ├── auth/
│       │   ├── login.php         # Login page
│       │   └── register.php      # Registration page
│       ├── includes/
│       │   ├── header.php        # Common header component
│       │   ├── sidebar.php       # Navigation sidebar
│       │   ├── delete-modal.php  # Reusable delete confirmation
│       │   └── db-init.php       # Shared database initialization
│       ├── dashboard.php         # Main dashboard
│       ├── profile.php          # User profile page
│       ├── subjects.php         # Subject management
│       ├── attendance.php       # Attendance tracking
│       ├── review-sessions.php  # Session details view
│       └── admin_dashboard.php  # Administrative interface
├── public/
│   ├── css/
│   │   ├── styles.css           # Main application styles
│   │   ├── loginRegister.css    # Authentication page styles
│   │   └── profile.css          # Profile page styles
│   ├── js/
│   │   ├── utils.js             # Utility functions
│   │   ├── dashboard.js         # Dashboard interactions
│   │   ├── attendance.js        # Attendance management
│   │   ├── review-sessions.js   # Session detail interactions
│   │   ├── dropdown.js          # Dropdown menu functionality
│   │   └── validation scripts   # Form validation
│   ├── api/
│   │   └── dashboard-stats.php  # API endpoint for statistics
│   ├── index.php               # Application entry point
│   ├── update-session.php      # Session update handler
│   ├── delete-session.php      # Session deletion handler
│   └── .htaccess               # Apache URL rewriting rules
├── database/
│   └── cmsc126_database.sql    # Database schema and sample data
└── README.md                   # Project documentation
```

## 👥 Team Roles

### Backend Developer & Database Architect
**Developer**: **Naza, Tirso Benedict J.**

**Primary Responsibilities:**
- Database design and optimization
- Server-side application logic
- API development and documentation
- Security implementation
- Performance optimization
- Code architecture and best practices

**Key Contributions:**
- Designed and implemented the complete database schema
- Developed the MVC architecture framework
- Created secure authentication and session management
- Implemented CRUD operations for all entities
- Built RESTful API endpoints
- Established security measures (CSRF, SQL injection prevention, XSS protection)

### Frontend Developer & UI/UX Designer
**Developer**: **Peña, Adriane Nathaniel L.**

**Primary Responsibilities:**
- User interface design and implementation
- Client-side functionality development
- Responsive design implementation
- User experience optimization
- Cross-browser compatibility
- Accessibility features

**Key Contributions:**
- Designed modern, responsive user interface
- Implemented interactive JavaScript functionality
- Created reusable UI components
- Developed mobile-first responsive design
- Optimized user experience flows
- Ensured accessibility standards compliance

## 🔄 Development Approach

### Methodology
- **Agile Development**: Iterative development with regular code reviews
- **Version Control**: Git-based collaboration with feature branches
- **Code Standards**: PSR-4 autoloading and consistent naming conventions
- **Testing**: Manual testing with comprehensive test scenarios
- **Documentation**: Inline code documentation and comprehensive README

### Architecture Decisions

**MVC Pattern Implementation**
- **Models**: Handle data persistence and business logic
- **Views**: Manage presentation layer and user interface
- **Controllers**: Coordinate between models and views, handle HTTP requests

**Database Design Principles**
- **Normalization**: Third normal form (3NF) compliance
- **Referential Integrity**: Foreign key constraints and cascading operations
- **Indexing**: Strategic indexing for performance optimization
- **Data Types**: Appropriate column types for storage efficiency

**Security-First Approach**
- **Input Validation**: Server-side validation for all user inputs
- **Output Escaping**: HTML entity encoding to prevent XSS
- **Parameterized Queries**: PDO prepared statements for SQL injection prevention
- **Session Security**: Secure session configuration and CSRF protection

## 🔒 Security Features

### Authentication Security
- **Password Hashing**: Argon2ID algorithm with salt
- **Session Management**: Secure session configuration
- **Login Throttling**: Protection against brute force attacks
- **Role-Based Authorization**: User and admin permission levels

### Data Protection
- **CSRF Protection**: Token-based request validation
- **SQL Injection Prevention**: Parameterized queries only
- **XSS Prevention**: Output escaping and input sanitization
- **Data Validation**: Server-side validation for all inputs

### Infrastructure Security
- **HTTPS Ready**: SSL/TLS certificate support
- **Error Handling**: Secure error messages without system disclosure
- **File Permissions**: Proper directory and file access controls
- **Database Security**: Restricted database user permissions

## 🤝 Contributing

This project was developed as an academic assignment for CMSC126 Web-based Systems. While not open for external contributions, the codebase serves as a learning resource for web development best practices.

### Code Review Guidelines
- Follow PSR-4 coding standards
- Maintain comprehensive documentation
- Implement proper error handling
- Ensure security best practices
- Write clean, readable code

### Academic Integrity Notice
This project is submitted as original work for academic evaluation. Any use of this code for academic submissions must be properly attributed and should comply with your institution's academic integrity policies.

---

**Course**: CMSC126 - Web-based Systems  
**Academic Year**: 2024-2025  
**Institution**: University of the Philippines Mindanao 
**Project Type**: Final Project - Study Session Management System