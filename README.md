# Final Project: Marathon Website

**Student:** Hoang Tien Trung Kien - 21110088

## Project Description:

### Admin Features:
- Editing rights (Add/Delete) for marathon events.
- Editing rights (Add/Delete) for athlete achievements.
- Editing rights (Add/Delete) for athlete's personal information.

### User Features:
- Ability to register for a marathon event.
- Ability to view a list of achievements (ranked by completion time).

## User Interface: (With Navbar - No Footer)
### Admin Section:
- Dedicated template for managing marathon events (Each event table divided into two sub-tables based on gender).
- Dedicated template for managing marathon achievements (Each achievement table divided into two sub-tables based on gender).
- Dedicated template for managing personal information of marathon athletes (Displayed by event and divided into two sub-tables with gender differentiation).

### User Section:
- Homepage template.
- Template for registering for an event.
- Template for viewing the list of achievements.

## Backend:

**Using XAMPP:**
- Database file: connect.php
- Databases (3 databases - 3 tables):
  - **achievements (Database 1):**
    - Table Data (participants):
      - Ranks int(11)
      - event_name varchar(255)
      - fullName varchar(255)
      - time_record varchar(255)
      - nationality varchar(255)
  - **event (Database 2):**
    - Table Data (participants):
      - marathon_ID int(11)
      - event_name varchar(255)
      - registration_deadline date
      - competition_day date
  - **user (Database 3):**
    - Table Data (participants):
      - fullName varchar(255)
      - nationality varchar(255)
      - sex varchar(255)
      - dob int(11)
      - email varchar(100)
      - phoneNumber int(11)
      - address text
      - event_name varchar(100)
      - passport varchar(100)

**Languages Used:**
- HTML/CSS/JS/PHP

**Framework:**
- Bootstrap 5 (CSS)

## System Design:

**Folder:** RDB - **File:** SystemDesign_WebsiteMarathon.png

