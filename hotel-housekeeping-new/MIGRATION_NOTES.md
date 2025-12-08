# Database Schema Migration - Hotel Housekeeping System

## Overview
This document outlines the changes made to migrate from the old database schema to the new normalized schema using the following tables:
- `users` - Staff/housekeeper login accounts
- `admins` - Admin login accounts
- `housekeepers` - Housekeeper information
- `rooms` - Room details and status
- `shifts` - Work shift definitions
- `tasks` - Cleaning tasks

## Database Schema Changes

### New Tables Structure

#### 1. **users** (Staff Authentication)
```sql
UserID int AI PK 
Username varchar(50) 
Password varchar(255) 
Department enum('HouseKeeping','Maintenance')
```

#### 2. **admins** (Admin Authentication)
```sql
AdminID int AI PK 
Username varchar(50) 
Password varchar(255)
```

#### 3. **housekeepers** (Extended from your original)
```sql
HousekeeperID int AI PK 
FullName varchar(100) 
Phone varchar(15) 
Email varchar(100) 
HireDate date
AssignedFloor int (ADDED)
Availability enum('Available','Busy','Off Duty') (ADDED)
```

#### 4. **rooms** (Extended from your original)
```sql
RoomID int AI PK 
RoomNumber int 
RoomType varchar(50) 
Status enum('Clean','Dirty','In Progress','Maintenance')
Floor int (ADDED)
AssignedHousekeeperID int (ADDED)
CleaningTime int (ADDED - stores seconds)
MaintenanceNote text (ADDED)
```

#### 5. **shifts** (From your original)
```sql
ShiftID int AI PK 
DayOfWeek enum('Monday','Tuesday','Wednesday','Thursday','Friday') 
StartTime time 
EndTime time
```

#### 6. **tasks** (From your original)
```sql
TaskID int AI PK 
Description varchar(255) 
RoomID int
```

#### 7. **assignments** (From your original - Central tracking table)
```sql
AssignmentID int AI PK 
RoomID int 
HousekeeperID int 
TaskID int 
AssignedDate date 
Status enum('Pending','In Progress','Completed')
```

#### 8. **housekeeper_shifts** (NEW - Junction Table)
```sql
HousekeeperID int
ShiftID int
PRIMARY KEY (HousekeeperID, ShiftID)
```

## Files Modified

### Authentication
- **`includes/login-process.php`** 
  - Now checks `admins` table first for admin logins
  - Then checks `users` table for staff logins
  - Sets `$_SESSION['accType']` to "admin" or "staff"
  - Stores `Department` in session for staff users

### Admin Dashboard
- **`admin-dashboard.php`**
  - Changed from `staff` table to `housekeepers` table
  - Uses `FullName` instead of `StaffMember`
  - Fetches schedules from `housekeeper_shifts` and `shifts` tables
  - Updated staff list selector for reports

- **`includes/admin-dashboard(inc).php`**
  - Changed table name from `Rooms` to `rooms`
  - Uses `Status` column (no change needed as both use it)

### Staff Dashboard
- **`staff-dashboard.php`**
  - Fetches housekeeper info using username from session
  - Uses `AssignedHousekeeperID` to find assigned rooms
  - Changed from `Stat` to `Status` column
  - Uses `RoomType` instead of `note` for room description

### Room Management
- **`includes/fetch-Overviewtable.php`**
  - Changed `Stat` to `Status`
  - Joins with `housekeepers` table to get `FullName`
  - Uses `AssignedHousekeeperID` instead of `assignedTo`
  - Uses `MaintenanceNote` instead of `maintenance`

- **`includes/fetch-assignmentsTable.php`**
  - Uses `Floor` instead of `floor`
  - Changed `Stat` to `Status`
  - Joins with `housekeepers` and `tasks` tables
  - Shows `TaskDescription` or `RoomType` for task info

- **`includes/update-room-status.php`**
  - Changed `Stat` to `Status`
  - Uses `CleaningTime` instead of `cleaningTime`

- **`includes/report-room-issue.php`**
  - Changed `maintenance` to `MaintenanceNote`

### Staff Management
- **`includes/fetch-Staff_table.php`**
  - Changed from `staff` table to `housekeepers` table
  - Uses `HousekeeperID` instead of `StaffID`
  - Uses `FullName` instead of `StaffMember`
  - Fetches schedules from `housekeeper_shifts` junction table
  - Shows email and phone instead of skills

- **`includes/get_staff.php`**
  - Changed to `housekeepers` table
  - Uses `HousekeeperID` and `FullName`
  - Uses `AssignedFloor` column
  - Returns backwards-compatible keys for JavaScript

- **`includes/update_floor.php`**
  - Changed to `housekeepers` table
  - Uses `HousekeeperID` instead of `StaffID`

- **`includes/update_schedule.php`**
  - Complete rewrite to use `shifts` and `housekeeper_shifts` tables
  - Parses schedule string and creates/links shift records
  - Maps abbreviated days to full names (Mon → Monday)

- **`includes/assign-room-staff.php`**
  - Looks up `HousekeeperID` from `FullName`
  - Updates `AssignedHousekeeperID` in rooms table

### Reports
- **`includes/fetch-staff-report.php`**
  - Gets `HousekeeperID` from `FullName`
  - Uses `AssignedHousekeeperID` instead of `assignedTo`
  - Changed `Stat` to `Status`
  - Uses `CleaningTime` and `MaintenanceNote`

## Migration Steps

### 1. Backup Your Database
```bash
mysqldump -u root -p webdb > backup_before_migration.sql
```

### 2. Run Migration Script
```bash
mysql -u root -p webdb < database_migration.sql
```

### 3. Migrate Existing Data (if applicable)
If you have data in old tables, you'll need to:
- Map old staff records to housekeepers table
- Update room assignments to use HousekeeperID
- Create user accounts for housekeepers in the `users` table
- Create shift records and link them via `housekeeper_shifts`

### 4. Test the System
- Test admin login
- Test staff login
- Verify room assignments work
- Check staff schedule updates
- Test room status updates
- Verify reports display correctly

## Important Notes

### Session Variables
The system maintains these session variables:
- `$_SESSION['username']` - Username from login
- `$_SESSION['accType']` - "admin" or "staff"
- `$_SESSION['staffName']` - Staff member name (for staff users)
- `$_SESSION['department']` - Department from users table (for staff)

### Backwards Compatibility
Several files return backwards-compatible data structures for JavaScript:
- `get_staff.php` returns both `StaffMember`/`FullName` and `StaffID`/`HousekeeperID`

### Password Security
⚠️ **IMPORTANT**: The current login system uses plain text password comparison. You should implement proper password hashing:

```php
// When creating users:
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// When logging in:
if (password_verify($password, $user['Password'])) {
    // Login successful
}
```

## Testing Checklist

- [ ] Admin can log in successfully
- [ ] Staff can log in successfully  
- [ ] Admin dashboard shows room statistics
- [ ] Admin can view room status board
- [ ] Admin can assign housekeepers to rooms
- [ ] Admin can view staff by floor
- [ ] Admin can update housekeeper schedules
- [ ] Admin can update housekeeper floor assignments
- [ ] Admin can view staff performance reports
- [ ] Staff can view assigned rooms
- [ ] Staff can start cleaning (timer works)
- [ ] Staff can mark room as done (saves time)
- [ ] Staff can report room issues
- [ ] Room status filters work correctly

## Known Limitations

1. **Schedule Parsing**: The schedule update system expects format "Mon, Tue, Wed 08:00-16:00". Any other format may not work correctly.

2. **User-Housekeeper Link**: The staff dashboard tries to link users to housekeepers by matching username to FullName. Ensure these match or modify the query logic.

3. **Saturday/Sunday**: Your `shifts` table enum only includes Monday-Friday. If you need weekend shifts, update the enum:
   ```sql
   ALTER TABLE shifts MODIFY COLUMN DayOfWeek 
   ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
   ```

## Questions or Issues?

If you encounter any issues:
1. Check the browser console for JavaScript errors
2. Check PHP error logs in XAMPP
3. Verify all required columns exist in your tables
4. Ensure foreign keys are properly set up
5. Check that you have sample data in all related tables
