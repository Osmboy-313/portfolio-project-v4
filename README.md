Forum-Style News CMS with Multi-Role Access – PHP MVC

A full-featured **Content Management System** built in **pure procedural PHP**, designed to mimic **Reddit-like functionality** with advanced **multi-role access**, **real-time validation**, **file handling**, **AJAX-based dynamic features**, and **clean MVC structure**. This is **not your typical news app** – users can post, manage their categories, and interact in a forum-style layout.

---

## Tech Stack

- **Backend**: PHP (Procedural MVC), MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **AJAX / API**: Fetch API (JSON responses), JS-driven CRUD
- **Automation**: Cron Jobs
- **Hosting Tested On**: InfinityFree

---

## Key Features

### Authentication & Authorization

- Three user roles: `User`, `Admin`, `Boss`
- Role-based access restrictions
- Registration code system for Admin/Boss (managed only by Boss)
- Secure login/logout system using sessions
- Password hashing with bcrypt
- JS-powered real-time username/email validation
- BOSS manages registration codes for admin/boss registration and conversion through profile update

### UI/UX

- Fully custom-designed frontend (no templates)
- Semi Responsive (CMS/post login pages arent responsive, but I plan to), modern design
- One common modal system reused across features
- Animated form transitions (login/register switch)
- Tabbed interfaces for user lists and code management and hybrid rendering for tabbed data

### File Upload System (with 5-case logic)

- Temporarily stores uploads in sessions
- Handles reloads, navigation, errors, and cleanup scenarios
- Moves to permanent storage upon success
- Automatically cleaned by cron if abandoned

  #### MORE DETAILED EXPLANATION OF FILE UPLOAD

    - if user submits a file with errors in the rest of the form then keep that session + file
    - if user submits a file with errors in the rest of the form then reloads the page then remove the session + file
    - if user submits a file with errors in the rest of the form but open another page then remove the session + file 
    - if user submits a file with errors in the rest of the form but closes the browser then crons job removes both of them
    - if user submits a file with no errors in the rest of the form, then file is moved to permanent storage and the session is removed

### Dynamic Content via JavaScript

- JS Fetch used for:
  - Authentication (login/register)
  - Categories CRUD
  - Codes CRUD
  - User listing & role-based deletion
- Real-time category, codes and username, email validation

### CRUD & CMS Features

- Users, Admins, and Bosses can all post
- Users used to have private categories (forum-style), old feature in version 3
- Global categories introduced (Reddit-style), it's a new feature applied in version 4
- Tag/chip system for posts
- Pagination for large datasets
- Search and filter by categories, tags, title, content

### Admin & Boss Controls

- Tabbed user management (Users/Admins/Bosses)
- Boss can delete any user and their posts
- Code management interface (Boss-only)
- Soft-deletion logic for categories

### Automation (Cron Jobs)

- Cleanup of:
  - Temp files older than 24 hours
  - Inactive (soft-deleted) categories older than 30 days
- Logging for cron tasks
- Scheduled via system cron or manual trigger

###  Security

- SQL Injection Prevention (Prepared Statements)
- XSS Protection (HTML escaping/sanitization)
- File upload security:
  - Type/size validation
  - Sanitized filenames
  - Directory protection
- Role-based access control
- PRG (Post/Redirect/Get) pattern to prevent form resubmission

---

## Folder Structure

/project-root
│
├── /app
│ ├── controllers
│ ├── models
│ ├── core
│ ├── cron
│ └── views
│
├── /public
│ ├── index.php # Front controller
│ └── assets
│     ├── javascript
│     ├── style
│     ├── uploads
│     └── fonts



---

##  Architecture

- **MVC Structure**: Pure procedural PHP (no frameworks), fully custom
- **API Layer**: REST-like PHP endpoints for JS Fetch calls
- **Routing**: Front controller pattern
- **View rendering**: Done server-side in PHP
- **Dynamic UI rendering**: Handled via JavaScript and modals
- **Form Handling**: All critical forms use PRG technique

---

## Development Stages

| Stage | UI               | Functionality                         |
|-------|------------------|----------------------------------------|
| 1     | Prototype UI     | News-only system (admin-post only)     |
| 2     | Improved layout  | Still news-style, MVC attempt failed   |
| 3     | Better MVC       | Users can also post, private categories |
| 4     | Modern UI        | Global categories, Reddit-style layout |
| 5     | (In Progress)    | Full REST API conversion               |

---

## Core Concepts Implemented

- 🔁 PRG Pattern
- 🔄 Debounced AJAX Validation
- ✅ Real-time error display
- 🧩 Reusable UI components (e.g., modals, tabbed interface)
- 🔐 Secure sessions + authentication
- 🗃️ Server-side pagination
- 🧹 Automated cleanup systems
- 🚫 Soft-deletes for reversible actions

---

## ⚡ How to Run Locally

> You must have PHP + MySQL installed (e.g., XAMPP/LAMPP or Laragon)

1. Clone the repository
2. Import the SQL dump to MySQL
3. Configure DB credentials in `/app/core/db.php`
4. Serve the `/public` folder via localhost (Apache/Nginx)
5. Set up a cron job pointing to `app/cron/master-cleanup.php`

---

## Security Notes

- **Passwords**: Enforced length, hashed with bcrypt
- **User Inputs**: Sanitized and validated
- **Uploads**: Checked for type/size, moved securely
- **Permissions**: Role-based access enforced at every level
- **Sessions**: Session timeout and cleanup strategies planned

---

## Cron Job Logic

### cleanup.php handles:

- Temp files (older than 24 hours)
- Soft-deleted categories (older than 30 days)
- Session + file cleanup for abandoned uploads

You can run it via:

https://osmboy.infinityfree.me/public/cron.php?key=a_big_super_secret

---

## Author

**Name**: *OSMBOY*  
**Role**: Full-Stack Developer  
**Education**: Intermediate in ICS and currently pursuing BSCS  
**Contact**: Osmanpak9912@gmail.com | osmboy.infinityfree.me  

---

## Credits

This project is 100% designed, developed, and architected by me — no frameworks, no templates. Just raw PHP, vanilla JavaScript, and tons of learning. ⚡

---

## Future Plans

- version 5 => under work | REST API Conversion + JWT TOken + JS page rendering (SPA) (Im not sure about this SPA, I might skip this)
- version 6 => Graph QL
- version 7 => OOP REST API
- version 8 => OOP Graph QL
- version 9 => Laravel 


---

## License

This project is licensed under the [MIT License](LICENSE).

---



