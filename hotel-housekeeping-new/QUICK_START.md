# Quick Start Guide - Hotel Housekeeping System Migration

## Prerequisites
- XAMPP installed and running (Apache + MySQL)
- Database name: `webdb`
- MySQL root password: `vince@21` (as per your database.php)

## Step-by-Step Setup

### 1. Create Database Tables

Open phpMyAdmin (http://localhost/phpmyadmin) and run these SQL commands:

```sql
-- Create admins table
CREATE TABLE admins (
  AdminID INT AUTO_INCREMENT PRIMARY KEY,
  Username VARCHAR(50) NOT NULL,
  Password VARCHAR(255) NOT NULL
);

-- Create users table
CREATE TABLE users (
  UserID INT AUTO_INCREMENT PRIMARY KEY,
  Username VARCHAR(50) NOT NULL,
  Password VARCHAR(255) NOT NULL,
  Department ENUM('HouseKeeping','Maintenance') NOT NULL
);

-- Create housekeepers table
CREATE TABLE housekeepers (
  HousekeeperID INT AUTO_INCREMENT PRIMARY KEY,
  FullName VARCHAR(100) NOT NULL,
  Phone VARCHAR(15),
  Email VARCHAR(100),
  HireDate DATE,
  AssignedFloor INT,
  Availability ENUM('Available','Busy','Off Duty') DEFAULT 'Available'
);

-- Create rooms table
CREATE TABLE rooms (
  RoomID INT AUTO_INCREMENT PRIMARY KEY,
  RoomNumber INT NOT NULL,
  RoomType VARCHAR(50),
  Status ENUM('Clean','Dirty','In Progress','Maintenance') DEFAULT 'Dirty',
  Floor INT,
  CleaningTime INT
);

-- Create shifts table
CREATE TABLE shifts (
  ShiftID INT AUTO_INCREMENT PRIMARY KEY,
  DayOfWeek ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  StartTime TIME NOT NULL,
  EndTime TIME NOT NULL
);

-- Create tasks table
CREATE TABLE tasks (
  TaskID INT AUTO_INCREMENT PRIMARY KEY,
  Description VARCHAR(255),
  RoomID INT
);

-- Create assignments table (tracks housekeeper assignments to rooms)
CREATE TABLE assignments (
  AssignmentID INT AUTO_INCREMENT PRIMARY KEY,
  RoomID INT NOT NULL,
  HousekeeperID INT NOT NULL,
  TaskID INT,
  AssignedDate DATE NOT NULL,
  Status ENUM('Pending','In Progress','Completed') DEFAULT 'Pending'
);

-- Create maintenancerequests table (tracks maintenance issues)
CREATE TABLE maintenancerequests (
  RequestID INT AUTO_INCREMENT PRIMARY KEY,
  RoomID INT NOT NULL,
  Description VARCHAR(500) NOT NULL,
  ReportedDate DATE NOT NULL,
  Status ENUM('Open','In Progress','Resolved') DEFAULT 'Open'
);

-- Create housekeeper_shifts junction table
CREATE TABLE housekeeper_shifts (
  HousekeeperID INT NOT NULL,
  ShiftID INT NOT NULL,
  PRIMARY KEY (HousekeeperID, ShiftID)
);
```

### 2. Run Migration Script

If you already have tables, run the migration script to add missing columns:

In phpMyAdmin, select your `webdb` database and go to SQL tab, then execute:
```bash
# Or use command line:
mysql -u root -p webdb < database_migration.sql
```

### 3. Add Sample Data

Run the sample data script:
```bash
mysql -u root -p webdb < sample_data.sql
```

### 4. Test Login

Open your browser and navigate to:
```
http://localhost/hotel-housekeeping-new/
```

**Test Credentials:**
- **Admin**: 
  - Username: `admin`
  - Password: `admin123`

- **Staff** (choose any):
  - Username: `Maria Santos` / Password: `maria123`
  - Username: `John Smith` / Password: `john123`
  - Username: `Lisa Johnson` / Password: `lisa123`

### 5. Verify Functionality

#### As Admin:
1. ✓ View dashboard with room statistics
2. ✓ View room status board
3. ✓ Go to Assignments tab - assign housekeepers to rooms
4. ✓ Go to Staff tab - view and manage housekeepers
5. ✓ Change floor assignments
6. ✓ Update schedules
7. ✓ Go to Reports tab - view performance reports

#### As Staff:
1. ✓ View assigned rooms
2. ✓ Filter by status (All/Dirty/In Progress/Clean)
3. ✓ Click "Start Cleaning" - timer should start
4. ✓ Click "Done Cleaning" - room status updates to Clean
5. ✓ Click "Report Issue" - creates maintenance request

## Troubleshooting

### Issue: Can't login
- Check that tables `admins` and `users` exist and have data
- Verify password matches (currently plain text: `admin123`)
- Check `includes/database.php` connection settings

### Issue: Rooms not showing
- Check that `rooms` table has data with Floor, RoomNumber
- Verify `assignments` table has active assignments
- Check for SQL errors in browser console or PHP error logs

### Issue: Staff dashboard shows no rooms
- Ensure staff username matches a `FullName` in `housekeepers` table
- Check that `assignments` table has records for that housekeeper
- Verify assignments have status 'Pending' or 'In Progress'
- Check that rooms have `AssignedHousekeeperID` set to that housekeeper
- Example: User 'Maria Santos' should match housekeeper FullName 'Maria Santos'

### Issue: Schedule not updating
- Check that `shifts` table exists
- Verify `housekeeper_shifts` junction table exists
- Ensure schedule format is: "Mon, Tue, Wed 08:00-16:00"
- Check browser console for JavaScript errors

### Issue: Assignment not working
- Verify `housekeepers` table has `AssignedFloor` column
- Check that `rooms` table has `AssignedHousekeeperID` column
- Check JavaScript console for AJAX errors
- Verify `includes/assign-room-staff.php` and `includes/get_staff.php` are accessible

### Enable Error Display
Add to the top of PHP files for debugging:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Database Structure Summary

```
admins               users                housekeepers
├─ AdminID          ├─ UserID           ├─ HousekeeperID
├─ Username         ├─ Username         ├─ FullName
└─ Password         ├─ Password         ├─ Phone
                    └─ Department       ├─ Email
                                        ├─ HireDate
                                        ├─ AssignedFloor
                                        └─ Availability

rooms                shifts              housekeeper_shifts
├─ RoomID           ├─ ShiftID          ├─ HousekeeperID (FK)
├─ RoomNumber       ├─ DayOfWeek        └─ ShiftID (FK)
├─ RoomType         ├─ StartTime
├─ Status           └─ EndTime
├─ Floor
└─ CleaningTime

tasks                assignments
├─ TaskID           ├─ AssignmentID
├─ Description      ├─ RoomID (FK)
└─ RoomID (FK)      ├─ HousekeeperID (FK)
                    ├─ TaskID (FK)
                    ├─ AssignedDate
                    └─ Status

maintenancerequests
├─ RequestID
├─ RoomID (FK)
├─ Description
├─ ReportedDate
└─ Status
```

## Next Steps

1. **Implement Password Hashing** - Current system uses plain text passwords
   ```php
   // Hash passwords when creating users:
   $hashed = password_hash($password, PASSWORD_DEFAULT);
   
   // Verify on login:
   password_verify($inputPassword, $storedHash);
   ```

2. **Add More Test Data** - Create more rooms, housekeepers, tasks

3. **Customize** - Adjust room types, floors, staff based on your hotel

4. **Backup** - Regularly backup your database
   ```bash
   mysqldump -u root -p webdb > backup_$(date +%Y%m%d).sql
   ```

## File Structure
```
hotel-housekeeping-new/
├── index.php                  (Login page)
├── admin-dashboard.php        (Admin interface)
├── staff-dashboard.php        (Staff interface)
├── logout.php                 (Logout handler)
├── database_migration.sql     (Schema migration)
├── sample_data.sql           (Test data)
├── MIGRATION_NOTES.md        (Detailed documentation)
├── includes/
│   ├── database.php          (DB connection)
│   ├── login-process.php     (Authentication)
│   ├── admin-dashboard(inc).php
│   ├── fetch-*.php           (Data fetchers)
│   ├── update-*.php          (Data updaters)
│   ├── assign-*.php          (Assignment logic)
│   └── report-*.php          (Reporting)
├── scripts/
│   └── *.js                  (Frontend JavaScript)
└── styles/
    └── *.css                 (Stylesheets)
```

## Support

For issues or questions:
1. Check MIGRATION_NOTES.md for detailed information
2. Review browser console for JavaScript errors
3. Check XAMPP error logs: `C:\xampp\apache\logs\error.log`
4. Verify all database tables and columns exist
5. Check that foreign keys are properly configured

---

**Remember:** Always backup your database before making changes!
