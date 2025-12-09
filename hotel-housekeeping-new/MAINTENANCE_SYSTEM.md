# Maintenance Request System

## Overview

The `maintenancerequests` table provides a dedicated system for tracking maintenance issues reported for rooms. This separates maintenance tracking from the rooms table, allowing multiple issues per room and historical tracking.

## Table Structure

```sql
CREATE TABLE maintenancerequests (
  RequestID INT AUTO_INCREMENT PRIMARY KEY,
  RoomID INT NOT NULL,
  Description VARCHAR(500) NOT NULL,
  ReportedDate DATE NOT NULL,
  Status ENUM('Open','In Progress','Resolved') DEFAULT 'Open'
);
```

## Key Features

### ✅ Dedicated Issue Tracking
- Each maintenance issue is a separate record
- Multiple issues can exist for the same room
- Issues remain even after resolved (historical record)

### ✅ Status Workflow
```
Open → In Progress → Resolved
```

### ✅ Date Tracking
- `ReportedDate` captures when issue was reported
- Can query issues by date range
- Track resolution time (when status changes to Resolved)

## How It Works

### 1. Staff Reports Issue
When a housekeeper finds a problem while cleaning:

**Staff Dashboard** → Click "Report Issue" → Enter description → Submit

```php
// includes/report-room-issue.php
INSERT INTO maintenancerequests (RoomID, Description, ReportedDate, Status) 
VALUES (?, ?, CURDATE(), 'Open');

// Optionally set room status to 'Maintenance'
UPDATE rooms SET Status = 'Maintenance' WHERE RoomID = ?;
```

### 2. Admin Views Issues
Admin can see maintenance request counts in room overview:

```php
// includes/fetch-Overviewtable.php
SELECT COUNT(*) as issue_count 
FROM maintenancerequests 
WHERE RoomID = ? AND Status IN ('Open', 'In Progress');
```

### 3. Maintenance Team Works
Maintenance staff can query open issues:

```sql
-- Get all open maintenance requests
SELECT r.RoomNumber, r.Floor, m.Description, m.ReportedDate
FROM maintenancerequests m
JOIN rooms r ON m.RoomID = r.RoomID
WHERE m.Status = 'Open'
ORDER BY m.ReportedDate ASC;
```

### 4. Mark as Resolved
When maintenance is completed:

```sql
UPDATE maintenancerequests 
SET Status = 'Resolved' 
WHERE RequestID = ?;

-- Update room status back to normal
UPDATE rooms 
SET Status = 'Dirty' 
WHERE RoomID = ?;
```

## Integration with System

### Staff Dashboard
- "Report Issue" button on each room card
- Modal dialog to enter issue description
- Creates maintenance request when submitted

### Admin Dashboard
**Room Status Board:**
- Shows maintenance issue count per room
- "3 open issue(s)" instead of single note
- Click to view details (can be implemented)

**Reports Tab:**
- Can show maintenance issues reported by each housekeeper
- Tracks which staff members are most attentive

### Room Status
When maintenance request is created with status 'Open':
- Room status can be automatically set to 'Maintenance'
- Room appears as requiring maintenance attention
- Assignment may be paused until issue resolved

## Query Examples

### Active Issues by Room
```sql
SELECT r.RoomNumber, COUNT(m.RequestID) as issue_count
FROM rooms r
LEFT JOIN maintenancerequests m ON r.RoomID = m.RoomID 
  AND m.Status IN ('Open', 'In Progress')
GROUP BY r.RoomID
HAVING issue_count > 0;
```

### Issues by Housekeeper Who Reported
```sql
-- Find which housekeepers reported maintenance issues
SELECT h.FullName, COUNT(m.RequestID) as issues_reported
FROM maintenancerequests m
JOIN rooms r ON m.RoomID = r.RoomID
JOIN assignments a ON r.RoomID = a.RoomID
JOIN housekeepers h ON a.HousekeeperID = h.HousekeeperID
WHERE m.ReportedDate = a.AssignedDate  -- Same day reporting
GROUP BY h.HousekeeperID
ORDER BY issues_reported DESC;
```

### Average Resolution Time
```sql
-- If you add ResolvedDate column:
SELECT AVG(DATEDIFF(ResolvedDate, ReportedDate)) as avg_days_to_resolve
FROM maintenancerequests
WHERE Status = 'Resolved' AND ResolvedDate IS NOT NULL;
```

### Issues by Room Type
```sql
SELECT r.RoomType, COUNT(m.RequestID) as issue_count
FROM maintenancerequests m
JOIN rooms r ON m.RoomID = r.RoomID
WHERE m.Status IN ('Open', 'In Progress')
GROUP BY r.RoomType
ORDER BY issue_count DESC;
```

## Benefits

### 📊 Better Tracking
- See full history of all maintenance issues
- Track recurring problems in specific rooms
- Identify rooms that need frequent maintenance

### 🔍 Improved Reporting
- Which room types have most issues?
- Which housekeepers are most observant?
- How long does maintenance take?
- What are common issues?

### ⚡ Faster Response
- Maintenance team sees all open issues in one view
- Prioritize by date or room type
- No issues lost or forgotten

### 📈 Data Analysis
- Historical data for budgeting
- Preventive maintenance scheduling
- Equipment replacement decisions

## Workflow Example

### Morning Shift
**Maria (Housekeeper)** starts cleaning Room 301:
- Notices AC making strange noise
- Clicks "Report Issue"
- Enters: "AC unit making loud rattling noise"
- Submits

**System:**
```sql
INSERT INTO maintenancerequests (RoomID, Description, ReportedDate, Status)
VALUES (11, 'AC unit making loud rattling noise', '2025-11-18', 'Open');

UPDATE rooms SET Status = 'Maintenance' WHERE RoomID = 11;
```

### Admin Review
**Admin** views Room Status Board:
- Room 301 shows "1 open issue(s)"
- Can click to see details (future enhancement)
- Notifies maintenance team

### Maintenance Team
**Maintenance tech** queries open requests:
```sql
SELECT * FROM maintenancerequests 
WHERE Status = 'Open' 
ORDER BY ReportedDate;
```

Goes to Room 301, updates status:
```sql
UPDATE maintenancerequests 
SET Status = 'In Progress' 
WHERE RequestID = 15;
```

### Resolution
After fixing AC:
```sql
UPDATE maintenancerequests 
SET Status = 'Resolved' 
WHERE RequestID = 15;

UPDATE rooms 
SET Status = 'Dirty' 
WHERE RoomID = 11;
```

Room 301 now available for cleaning again.

## Future Enhancements

### 1. Add More Fields
```sql
ALTER TABLE maintenancerequests
ADD COLUMN ResolvedDate DATE,
ADD COLUMN AssignedMaintenanceStaff INT,
ADD COLUMN Priority ENUM('Low','Medium','High','Urgent') DEFAULT 'Medium',
ADD COLUMN Category VARCHAR(50), -- 'Plumbing', 'Electrical', 'HVAC', etc.
ADD COLUMN ResolutionNotes TEXT;
```

### 2. Maintenance Staff Table
```sql
CREATE TABLE maintenancestaff (
  StaffID INT AUTO_INCREMENT PRIMARY KEY,
  FullName VARCHAR(100),
  Specialty VARCHAR(50),
  Phone VARCHAR(15)
);
```

### 3. Link to Assignments
Track which housekeeper reported each issue:
```sql
ALTER TABLE maintenancerequests
ADD COLUMN ReportedByHousekeeperID INT;
```

### 4. Photo Attachments
Store image paths of issues:
```sql
ALTER TABLE maintenancerequests
ADD COLUMN PhotoPath VARCHAR(255);
```

### 5. Maintenance Dashboard
Create dedicated maintenance interface:
- View all open issues
- Assign to maintenance staff
- Update status in real-time
- Print work orders

## Database Indexes

For optimal performance:

```sql
CREATE INDEX idx_maintenance_room ON maintenancerequests(RoomID);
CREATE INDEX idx_maintenance_status ON maintenancerequests(Status);
CREATE INDEX idx_maintenance_date ON maintenancerequests(ReportedDate);
CREATE INDEX idx_maintenance_active ON maintenancerequests(Status, ReportedDate);
```

## Sample Data

```sql
-- Various maintenance issues
INSERT INTO maintenancerequests (RoomID, Description, ReportedDate, Status) VALUES
(1, 'Bathroom faucet dripping', '2025-11-18', 'Open'),
(5, 'TV remote not working', '2025-11-18', 'Open'),
(9, 'AC not cooling properly', '2025-11-17', 'In Progress'),
(12, 'Light switch broken in bathroom', '2025-11-16', 'Resolved'),
(15, 'Thermostat display not working', '2025-11-18', 'Open');
```

## Migration from Old System

If you had `MaintenanceNote` field in rooms table:

```sql
-- Convert old notes to maintenance requests
INSERT INTO maintenancerequests (RoomID, Description, ReportedDate, Status)
SELECT RoomID, MaintenanceNote, CURDATE(), 'Open'
FROM rooms
WHERE MaintenanceNote IS NOT NULL AND MaintenanceNote != '';

-- Remove old column
ALTER TABLE rooms DROP COLUMN MaintenanceNote;
```

## Summary

The `maintenancerequests` table transforms maintenance tracking from a simple note field to a comprehensive issue management system with:
- ✅ Multiple issues per room
- ✅ Status workflow (Open → In Progress → Resolved)
- ✅ Date tracking
- ✅ Historical records
- ✅ Better reporting and analytics
- ✅ Scalable for future enhancements

This provides a professional, maintainable system for tracking and resolving maintenance issues in your hotel.
