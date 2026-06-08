# CSTE CLUB Voting System

A web-based voting platform designed for managing elections and voting processes in the CSTE (Computer Science and Engineering) Club. This system provides a secure, user-friendly interface for conducting digital elections with real-time vote tracking and administrative controls.

## 📋 Table of Contents

- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Project Structure](#project-structure)
- [Usage Guide](#usage-guide)
  - [For Voters](#for-voters)
  - [For Administrators](#for-administrators)
- [Database Schema](#database-schema)
- [Configuration](#configuration)
- [Security Features](#security-features)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### Voter Features
- **Secure Login**: Authentication with voter ID and password
- **Interactive Voting Interface**: User-friendly ballot with radio buttons and checkboxes
- **Candidate Information**: View candidate platforms and details before voting
- **Vote Preview**: Review your selections before submission
- **Vote Validation**: Ensures all voting requirements are met
- **Single Vote Per Election**: Prevents duplicate voting

### Administrative Features
- **Admin Dashboard**: Comprehensive overview of election statistics
- **Real-time Analytics**: 
  - Number of positions
  - Number of candidates
  - Total registered voters
  - Vote count and participation rate
- **Vote Tally Charts**: Visual representation of voting results for each position
- **Voter Management**: Add and manage registered voters
- **Candidate Management**: Configure candidates and their information
- **Position Management**: Set up election positions with voting rules
- **Vote Tracking**: Monitor and view submitted votes
- **CSV Import**: Bulk import voter data from CSV files

### System Features
- **Flexible Voting Rules**: Support for single-choice and multi-choice positions
- **Position Priorities**: Organize positions in display order
- **Responsive Design**: Works on desktop and mobile devices
- **Password Security**: Passwords are hashed for security
- **Session Management**: Secure session handling for authenticated users
- **PDF Export**: Generate voting records and reports

## 🖥️ System Requirements

### Server Requirements
- **PHP**: Version 7.0 or higher
- **MySQL/MariaDB**: Version 5.5 or higher
- **Apache/Nginx**: Web server with PHP support
- **Extensions**: MySQLi, JSON

### Client Requirements
- Modern web browser (Chrome, Firefox, Safari, Edge)
- JavaScript enabled
- Internet connectivity

## 📦 Installation

### Step 1: Clone the Repository
```bash
git clone https://github.com/Md-Tasrik/Voting-System-For-CSTE-CLUB.git
cd Voting-System-For-CSTE-CLUB
```

### Step 2: Database Setup
1. Open your MySQL/MariaDB client
2. Create a new database:
   ```sql
   CREATE DATABASE votesystem;
   ```
3. Import the database schema:
   ```bash
   mysql -u your_username -p votesystem < database/votesystem.sql
   ```

### Step 3: Configure Database Connection
1. Open `includes/conn.php`
2. Update the database credentials:
   ```php
   $conn = new mysqli('localhost', 'username', 'password', 'votesystem');
   ```

### Step 4: Set Up Web Server
- Copy the project folder to your web server directory (e.g., `/var/www/html/` for Apache)
- Ensure proper file permissions (755 for directories, 644 for files)

### Step 5: Access the Application
- **Voter Login**: http://localhost/Voting-System-For-CSTE-CLUB/
- **Admin Login**: http://localhost/Voting-System-For-CSTE-CLUB/admin/

### Step 6: Initial Setup
1. Log in to the admin panel with default credentials
2. Configure election title in `admin/config.ini`
3. Add positions for the election
4. Add candidates for each position
5. Import or add voter credentials

## 📁 Project Structure

```
Voting-System-For-CSTE-CLUB/
├── index.php                    # Voter login page
├── home.php                     # Main voting interface
├── login.php                    # Voter login handler
├── logout.php                   # Logout handler
├── preview.php                  # Ballot preview
├── submit_ballot.php            # Vote submission handler
│
├── admin/
│   ├── index.php               # Admin login page
│   ├── login.php               # Admin authentication
│   ├── home.php                # Admin dashboard
│   ├── config.ini              # Election configuration
│   ├── positions.php           # Manage positions
│   ├── candidates.php          # Manage candidates
│   ├── voters.php              # Manage voters
│   └── votes.php               # View vote results
│
├── includes/
│   ├── conn.php                # Database connection
│   ├── header.php              # HTML header template
│   ├── navbar.php              # Navigation bar
│   ├── footer.php              # Footer template
│   ├── session.php             # Session management
│   ├── scripts.php             # JavaScript includes
│   ├── slugify.php             # URL slug utility
│   ├── loader.php              # Loading indicator
│   └── ballot_modal.php        # Ballot modal component
│
├── database/
│   └── votesystem.sql          # Database schema
│
├── images/
│   └── [Candidate photos]      # Candidate profile images
│
├── csv file format/
│   └── [Sample CSV files]      # Voter data import templates
│
└── tcpdf/
    └── [PDF library]           # PDF generation library
```

## 🚀 Usage Guide

### For Voters

#### 1. **Login**
- Navigate to the voter login page
- Enter your Voter ID and password
- Click "Sign In"

#### 2. **View Ballot**
The voting page displays all positions for the current election:
- Each position shows eligible candidates
- Instructions indicate whether to select one or multiple candidates
- Candidate information button reveals their platform

#### 3. **Cast Your Vote**
- Click on candidate names to select them
- For single-choice positions: Only one selection allowed (radio button)
- For multi-choice positions: Multiple selections allowed (checkboxes)
- Use "Reset" button to clear selections for a position

#### 4. **Review Before Submitting**
- Click "Preview" to review your ballot
- Ensure all required selections are made
- Cancel and return to make changes if needed

#### 5. **Submit Your Vote**
- Click "Submit" to finalize your vote
- Confirmation message indicates successful submission
- You cannot vote again in the same election

#### 6. **View Your Ballot**
- After voting, click "View Ballot" to see your submission
- Your voting record is now stored in the system

### For Administrators

#### 1. **Admin Login**
- Navigate to `/admin/`
- Enter username and password
- Access the admin dashboard

#### 2. **Dashboard Overview**
The dashboard displays:
- Total number of positions
- Total number of candidates
- Total registered voters
- Number of votes cast
- Real-time vote tally charts for each position

#### 3. **Manage Positions**
- Add new election positions
- Set maximum votes allowed per position
- Define priority/display order
- Edit or delete positions as needed

#### 4. **Manage Candidates**
- Add candidates to positions
- Upload candidate photos
- Add candidate platform/biography
- Edit or remove candidates

#### 5. **Manage Voters**
- Add individual voter accounts
- Bulk import voters from CSV files
- Set voter IDs and passwords
- Activate/deactivate voter accounts

#### 6. **Monitor Results**
- View real-time vote tallies
- Access detailed voting reports
- Generate PDF reports
- Track voter participation

#### 7. **Election Configuration**
- Edit election title in `admin/config.ini`
- Customize election settings
- Configure voting rules

## 🗄️ Database Schema

### Main Tables

#### `positions` Table
- `id`: Position ID (Primary Key)
- `description`: Position name (e.g., "President", "Secretary")
- `max_vote`: Maximum votes allowed for this position
- `priority`: Display order

#### `candidates` Table
- `id`: Candidate ID (Primary Key)
- `position_id`: Associated position (Foreign Key)
- `firstname`: Candidate first name
- `lastname`: Candidate last name
- `photo`: Profile photo filename
- `platform`: Candidate's platform/biography

#### `voters` Table
- `id`: Voter ID (Primary Key)
- `voters_id`: Unique voter identifier
- `password`: Hashed password
- `firstname`: Voter first name
- `lastname`: Voter last name

#### `votes` Table
- `id`: Vote ID (Primary Key)
- `voters_id`: Voter who cast the vote (Foreign Key)
- `candidate_id`: Selected candidate (Foreign Key)
- `position_id`: Associated position (Foreign Key)
- `timestamp`: Vote submission time

#### `admin` Table
- `id`: Admin ID (Primary Key)
- `username`: Admin username
- `password`: Admin password (MD5 hashed)

## ⚙️ Configuration

### Election Settings
Edit `admin/config.ini`:
```ini
[settings]
election_title = "CSTE Club Elections 2024"
election_date = "2024-06-15"
; Add more settings as needed
```

### Database Configuration
Edit `includes/conn.php`:
```php
$conn = new mysqli(
    'localhost',     // Server address
    'username',      // Database user
    'password',      // Database password
    'votesystem'     // Database name
);
```

### CSV Import Format
Create voter CSV files with the following format:
```csv
voters_id,password,firstname,lastname
V001,password123,John,Doe
V002,password456,Jane,Smith
```

## 🔒 Security Features

- **Password Hashing**: Voter passwords are hashed using PHP's `password_hash()` function
- **Admin Password Security**: Admin passwords are hashed with MD5 (consider upgrading to bcrypt)
- **Session Management**: Server-side session validation prevents unauthorized access
- **SQL Injection Prevention**: Uses prepared statements for database queries
- **Vote Uniqueness**: Database constraints prevent duplicate voting
- **Login Validation**: Credential verification before granting access

### Recommended Security Enhancements
- Upgrade admin password hashing from MD5 to bcrypt
- Implement HTTPS/SSL encryption
- Add CSRF tokens to forms
- Implement rate limiting on login attempts
- Regular backup of voting database
- Implement audit logging

## 🐛 Troubleshooting

### Issue: Database Connection Error
**Solution**:
- Verify MySQL service is running
- Check database credentials in `includes/conn.php`
- Ensure database `votesystem` exists
- Verify user has appropriate permissions

### Issue: Login Page Shows Error
**Solution**:
- Clear browser cookies and cache
- Verify voter credentials exist in database
- Check database connection
- Review error messages in browser console

### Issue: Candidate Photos Not Displaying
**Solution**:
- Verify image files are in the `images/` directory
- Check image file permissions (644)
- Verify image filename in database matches actual file
- Supported formats: JPG, PNG, GIF

### Issue: Vote Not Submitting
**Solution**:
- Ensure at least one candidate is selected
- Verify all position requirements are met
- Check form validation messages
- Review browser console for JavaScript errors

### Issue: Admin Dashboard Charts Not Loading
**Solution**:
- Verify Chart.js library is loaded
- Check browser console for JavaScript errors
- Ensure votes exist in database
- Clear browser cache

## 📝 CSV Voter Import

1. Prepare a CSV file with columns: `voters_id`, `password`, `firstname`, `lastname`
2. Log in to admin panel
3. Go to "Manage Voters"
4. Click "Import CSV"
5. Select and upload the CSV file
6. Verify imported data

## 📊 Reports and Exports

- **Vote Tally Report**: View from admin dashboard
- **PDF Export**: Generate voting results (requires TCPDF library)
- **Participation Report**: Monitor voter turnout
- **Detailed Results**: Candidate-wise vote breakdown

## 🤝 Contributing

Contributions are welcome! To contribute:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/improvement`)
3. Make your changes
4. Commit your changes (`git commit -am 'Add improvement'`)
5. Push to the branch (`git push origin feature/improvement`)
6. Create a Pull Request

## 📄 License

This project is open source and available under the MIT License.

## 👤 Author

**Md-Tasrik** - Project Creator and Maintainer

## 📞 Support

For issues, bugs, or feature requests, please open an issue on the GitHub repository.

## 🎯 Project Goals

- Provide a secure, accessible voting platform for club elections
- Enable transparent and fair election processes
- Reduce manual vote counting and administrative burden
- Create a maintainable and scalable voting system
- Improve election transparency and accuracy

## 📅 Future Enhancements

- [ ] Two-factor authentication
- [ ] Email notifications for voters
- [ ] Advanced analytics and visualizations
- [ ] Multi-language support
- [ ] Mobile app integration
- [ ] Automated voter list generation
- [ ] Blockchain-based vote verification
- [ ] Real-time result updates

---

**Last Updated**: February 2026

**Note**: To Vote is an option, but if you want to do it, then it must be done well.
