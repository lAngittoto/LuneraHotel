# Assignments Table Integration

## Overview

The `assignments` table is now the **central tracking mechanism** for all housekeeper-room assignments. This provides better tracking, historical records, and status management.

## Table Structure

```sql
CREATE TABLE assignments (
  AssignmentID INT AUTO_INCREMENT PRIMARY KEY,
  RoomID INT NOT NULL,
  HousekeeperID INT NOT NULL,
  TaskID INT,
  AssignedDate DATE NOT NULL,
  Status ENUM('Pending','In Progress','Completed') DEFAULT 'Pending'
);
```

## Key Concept: Separation of Concerns

### Before (Direct Assignment):
- Rooms table had `AssignedHousekeeperID` column
- One room could only be assigned to one housekeeper at a time
- No historical record of who cleaned what
- No date tracking for assignments

### After (Assignment Table):
- Rooms table stores only room properties (number, type, status, floor)
- `assignments` table tracks who is assigned to clean which room and when
- Multiple assignments can exist for the same room (historical data)
- Active assignments have status 'Pending' or 'In Progress'
- Completed assignments remain for reporting

## How It Works

### 1. Creating an Assignment
When admin assigns a housekeeper to a room:

```php
// includes/assign-room-staff.php
INSERT INTO assignments (RoomID, HousekeeperID, AssignedDate, Status) 
VALUES (?, ?, ?, 'Pending')
```

### 2. Viewing Active Assignments
Staff dashboard shows only active assignments:

```php
// staff-dashboard.php
SELECT r.*, a.AssignmentID, a.Status as AssignmentStatus 
FROM assignments a 
JOIN rooms r ON a.RoomID = r.RoomID 
WHERE a.HousekeeperID = ? 
AND a.Status IN ('Pending', 'In Progress')
```

### 3. Working on a Room
When staff starts cleaning:
- Assignment status changes from 'Pending' to 'In Progress'
- Room status might also update to 'In Progress'

### 4. Completing Cleaning
When staff marks room as done:
- Assignment status changes to 'Completed'
- Room status updates to 'Clean'
- CleaningTime is recorded on the room

### 5. Reporting
Performance reports query completed assignments:

```php
SELECT COUNT(*) as cleaned
FROM assignments a
WHERE a.HousekeeperID = ? AND a.Status = 'Completed'
```

## Benefits

### ✅ Historical Tracking
- Keep records of all past assignments
- Track who cleaned which room on which date
- Useful for auditing and performance reviews

### ✅ Better Status Management
- Room status (Clean/Dirty) is separate from assignment status (Pending/In Progress/Completed)
- A room can be dirty but have no active assignment
- A room can be clean with a completed assignment record

### ✅ Task Integration
- Assignments can link to specific tasks
- TaskID field allows tracking detailed cleaning instructions
- Optional field - can assign room without specific task

### ✅ Date-Based Queries
- Find assignments for today: `WHERE AssignedDate = CURDATE()`
- Find assignments for a date range
- Track daily workload

### ✅ Multiple Assignments Per Day
- Can reassign a room to different housekeeper same day
- Old assignment remains as historical record
- Only active assignments (Pending/In Progress) shown in UI

## Implementation Details

### Files Updated to Use Assignments

1. **`fetch-Overviewtable.php`** - Shows current assignments in room overview
2. **`fetch-assignmentsTable.php`** - Lists all rooms with active assignments
3. **`assign-room-staff.php`** - Creates/updates assignments
4. **`staff-dashboard.php`** - Shows staff member's active assignments
5. **`fetch-Staff_table.php`** - Counts active assignments per housekeeper
6. **`fetch-staff-report.php`** - Reports based on completed assignments

### Query Pattern

Most queries follow this pattern:
```sql
SELECT ... 
FROM assignments a
JOIN rooms r ON a.RoomID = r.RoomID
JOIN housekeepers h ON a.HousekeeperID = h.HousekeeperID
LEFT JOIN tasks t ON a.TaskID = t.TaskID
WHERE a.Status IN ('Pending', 'In Progress')  -- Active only
AND a.AssignedDate = CURDATE()                -- Today's assignments
```

## Workflow Example

### Morning: Admin Creates Assignments
```sql
-- Room 101 needs cleaning, assign to Maria
INSERT INTO assignments (RoomID, HousekeeperID, AssignedDate, Status)
VALUES (1, 1, '2025-11-18', 'Pending');
```

### Mid-Day: Maria Starts Cleaning
```sql
-- Maria clicks "Start Cleaning" on Room 101
UPDATE assignments 
SET Status = 'In Progress' 
WHERE AssignmentID = ?;

-- Optionally update room status too
UPDATE rooms 
SET Status = 'In Progress' 
WHERE RoomID = 1;
```

### Afternoon: Maria Finishes
```sql
-- Maria clicks "Done Cleaning"
UPDATE assignments 
SET Status = 'Completed' 
WHERE AssignmentID = ?;

UPDATE rooms 
SET Status = 'Clean', CleaningTime = 1200 
WHERE RoomID = 1;
```

### Evening: Reports
```sql
-- How many rooms did Maria clean today?
SELECT COUNT(*) 
FROM assignments 
WHERE HousekeeperID = 1 
AND AssignedDate = '2025-11-18' 
AND Status = 'Completed';
```

## Migration from Old System

If you had the old system with `AssignedHousekeeperID` in rooms:

```sql
-- Create assignments from existing room assignments
INSERT INTO assignments (RoomID, HousekeeperID, AssignedDate, Status)
SELECT RoomID, AssignedHousekeeperID, CURDATE(), 'Pending'
FROM rooms
WHERE AssignedHousekeeperID IS NOT NULL;

-- Remove old column (optional, but recommended)
ALTER TABLE rooms DROP COLUMN AssignedHousekeeperID;
```

## Best Practices

### ✅ DO:
- Create new assignment for each day's work
- Mark old assignments as 'Completed' when done
- Query only active assignments (`Status IN ('Pending', 'In Progress')`) for current work
- Keep completed assignments for historical reporting

### ❌ DON'T:
- Delete completed assignments (lose historical data)
- Reuse old assignments for new days
- Query without date filter (can be slow with many records)
- Update room's status without updating assignment status

## Database Indexes

For optimal performance:

```sql
CREATE INDEX idx_assignments_room ON assignments(RoomID);
CREATE INDEX idx_assignments_housekeeper ON assignments(HousekeeperID);
CREATE INDEX idx_assignments_status ON assignments(Status);
CREATE INDEX idx_assignments_date ON assignments(AssignedDate);

-- Composite index for common query pattern
CREATE INDEX idx_assignments_active ON assignments(HousekeeperID, Status, AssignedDate);
```

## Future Enhancements

Possible additions to leverage the assignments table:

1. **Priority Levels**: Add `Priority` field (High/Medium/Low)
2. **Time Estimates**: Add `EstimatedMinutes` and `ActualMinutes`
3. **Notes**: Add assignment-specific notes
4. **Completion Time**: Add `CompletedDateTime` timestamp
5. **Rating**: Add quality rating after completion
6. **Supervisor Approval**: Add `ApprovedBy` and `ApprovedDate`

## Summary

The `assignments` table transforms the system from simple room-housekeeper linking to comprehensive work tracking with history, status management, and powerful reporting capabilities.
