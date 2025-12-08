-- Database Migration Script for Hotel Housekeeping System
-- Run this script to add the necessary columns to your new schema

-- Add additional columns to rooms table
ALTER TABLE rooms 
ADD COLUMN IF NOT EXISTS Floor INT AFTER RoomType,
ADD COLUMN IF NOT EXISTS CleaningTime INT AFTER Floor;

-- Add additional columns to housekeepers table
ALTER TABLE housekeepers 
ADD COLUMN IF NOT EXISTS AssignedFloor INT AFTER HireDate,
ADD COLUMN IF NOT EXISTS Availability ENUM('Available','On Break','Absent','On Leave','Unavailable') DEFAULT 'Available' AFTER AssignedFloor;

-- Create assignments table (main table for tracking task assignments)
CREATE TABLE IF NOT EXISTS assignments (
  AssignmentID INT AUTO_INCREMENT PRIMARY KEY,
  RoomID INT NOT NULL,
  HousekeeperID INT NOT NULL,
  TaskID INT,
  AssignedDate DATE NOT NULL,
  Status ENUM('Pending','In Progress','Completed') DEFAULT 'Pending',
  FOREIGN KEY (RoomID) REFERENCES rooms(RoomID) ON DELETE CASCADE,
  FOREIGN KEY (HousekeeperID) REFERENCES housekeepers(HousekeeperID) ON DELETE CASCADE,
  FOREIGN KEY (TaskID) REFERENCES tasks(TaskID) ON DELETE SET NULL
);

-- Create maintenancerequests table for tracking maintenance issues
CREATE TABLE IF NOT EXISTS maintenancerequests (
  RequestID INT AUTO_INCREMENT PRIMARY KEY,
  RoomID INT NOT NULL,
  Description VARCHAR(500) NOT NULL,
  ReportedDate DATE NOT NULL,
  Status ENUM('Open','In Progress','Resolved') DEFAULT 'Open',
  FOREIGN KEY (RoomID) REFERENCES rooms(RoomID) ON DELETE CASCADE
);

-- Create housekeepershifts junction table
CREATE TABLE IF NOT EXISTS housekeepershifts (
  HousekeeperID INT NOT NULL,
  ShiftID INT NOT NULL,
  PRIMARY KEY (HousekeeperID, ShiftID),
  FOREIGN KEY (HousekeeperID) REFERENCES housekeepers(HousekeeperID) ON DELETE CASCADE,
  FOREIGN KEY (ShiftID) REFERENCES shifts(ShiftID) ON DELETE CASCADE
);

-- Add foreign key constraints to rooms table
ALTER TABLE rooms
ADD CONSTRAINT fk_rooms_housekeeper 
  FOREIGN KEY (AssignedHousekeeperID) REFERENCES housekeepers(HousekeeperID) 
  ON DELETE SET NULL;

-- Add foreign key constraints to tasks table (tasks are templates, not assignments)
ALTER TABLE tasks
ADD CONSTRAINT fk_tasks_room 
  FOREIGN KEY (RoomID) REFERENCES rooms(RoomID) 
  ON DELETE CASCADE;

-- Add indexes for better performance
CREATE INDEX idx_rooms_status ON rooms(Status);
CREATE INDEX idx_rooms_floor ON rooms(Floor);
CREATE INDEX idx_rooms_assigned ON rooms(AssignedHousekeeperID);
CREATE INDEX idx_housekeepers_floor ON housekeepers(AssignedFloor);
CREATE INDEX idx_tasks_room ON tasks(RoomID);
CREATE INDEX idx_assignments_room ON assignments(RoomID);
CREATE INDEX idx_assignments_housekeeper ON assignments(HousekeeperID);
CREATE INDEX idx_assignments_status ON assignments(Status);
CREATE INDEX idx_assignments_date ON assignments(AssignedDate);
CREATE INDEX idx_maintenance_room ON maintenancerequests(RoomID);
CREATE INDEX idx_maintenance_status ON maintenancerequests(Status);
CREATE INDEX idx_maintenance_date ON maintenancerequests(ReportedDate);

-- Optional: Sample data migration from old schema (if old tables still exist)
-- Uncomment and modify as needed if migrating from old data

-- UPDATE rooms r
-- LEFT JOIN staff s ON r.assignedTo = s.StaffMember
-- LEFT JOIN housekeepers h ON s.StaffMember = h.FullName
-- SET r.AssignedHousekeeperID = h.HousekeeperID
-- WHERE r.assignedTo IS NOT NULL;

-- Migration complete!
-- Note: Make sure to backup your database before running this script.
