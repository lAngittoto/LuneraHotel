-- Sample Data for Hotel Housekeeping System
-- This script creates test data for the new schema

-- Insert admin accounts
INSERT INTO admins (Username, Password) VALUES
('admin', 'admin123'),
('manager', 'manager123');

-- Insert housekeepers
INSERT INTO housekeepers (FullName, Phone, Email, HireDate, AssignedFloor, Availability) VALUES
('Maria Santos', '555-0101', 'maria.santos@hotel.com', '2024-01-15', 1, 'Available'),
('John Smith', '555-0102', 'john.smith@hotel.com', '2024-02-01', 1, 'Available'),
('Lisa Johnson', '555-0103', 'lisa.johnson@hotel.com', '2024-01-20', 2, 'Available'),
('Carlos Rodriguez', '555-0104', 'carlos.r@hotel.com', '2024-03-01', 2, 'On Break'),
('Sarah Williams', '555-0105', 'sarah.w@hotel.com', '2024-02-15', 3, 'Available');

-- Insert user accounts for housekeepers (for staff login)
INSERT INTO users (Username, Password, Department) VALUES
('Maria Santos', 'maria123', 'HouseKeeping'),
('John Smith', 'john123', 'HouseKeeping'),
('Lisa Johnson', 'lisa123', 'HouseKeeping'),
('Carlos Rodriguez', 'carlos123', 'HouseKeeping'),
('Sarah Williams', 'sarah123', 'HouseKeeping');

-- Insert shifts
INSERT INTO shifts (DayOfWeek, StartTime, EndTime) VALUES
('Monday', '08:00:00', '16:00:00'),
('Tuesday', '08:00:00', '16:00:00'),
('Wednesday', '08:00:00', '16:00:00'),
('Thursday', '08:00:00', '16:00:00'),
('Friday', '08:00:00', '16:00:00'),
('Monday', '16:00:00', '00:00:00'),
('Tuesday', '16:00:00', '00:00:00'),
('Wednesday', '16:00:00', '00:00:00'),
('Thursday', '16:00:00', '00:00:00'),
('Friday', '16:00:00', '00:00:00');

-- Link housekeepers to shifts (Maria works Mon-Fri morning)
INSERT INTO housekeepershifts (HousekeeperID, ShiftID) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5);

-- John works Mon-Wed morning
INSERT INTO housekeepershifts (HousekeeperID, ShiftID) VALUES
(2, 1), (2, 2), (2, 3);

-- Lisa works Mon-Fri morning
INSERT INTO housekeepershifts (HousekeeperID, ShiftID) VALUES
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5);

-- Carlos works Mon-Thu morning
INSERT INTO housekeepershifts (HousekeeperID, ShiftID) VALUES
(4, 1), (4, 2), (4, 3), (4, 4);

-- Sarah works Tue-Fri morning
INSERT INTO housekeepershifts (HousekeeperID, ShiftID) VALUES
(5, 2), (5, 3), (5, 4), (5, 5);

-- Insert rooms for Floor 1
INSERT INTO rooms (RoomNumber, RoomType, Status, Floor) VALUES
(101, 'Standard Double', 'Dirty', 1),
(102, 'Standard Double', 'Clean', 1),
(103, 'Deluxe Suite', 'Dirty', 1),
(104, 'Standard Double', 'In Progress', 1),
(105, 'Standard Single', 'Clean', 1);

-- Insert rooms for Floor 2
INSERT INTO rooms (RoomNumber, RoomType, Status, Floor) VALUES
(201, 'Standard Double', 'Dirty', 2),
(202, 'Deluxe Double', 'Clean', 2),
(203, 'Standard Double', 'Dirty', 2),
(204, 'Deluxe Suite', 'Maintenance', 2),
(205, 'Standard Single', 'Clean', 2);

-- Insert rooms for Floor 3
INSERT INTO rooms (RoomNumber, RoomType, Status, Floor) VALUES
(301, 'Presidential Suite', 'Dirty', 3),
(302, 'Deluxe Double', 'Clean', 3),
(303, 'Standard Double', 'Dirty', 3),
(304, 'Standard Double', 'Clean', 3),
(305, 'Deluxe Suite', 'In Progress', 3);

-- Insert some tasks
INSERT INTO tasks (Description, RoomID) VALUES
('Deep clean bathroom', 1),
('Replace linens', 1),
('Vacuum and mop', 3),
('Window cleaning', 6),
('Deep clean bathroom', 8),
('Replace all linens', 11);

-- Create assignments (linking housekeepers to rooms with tasks)
INSERT INTO assignments (RoomID, HousekeeperID, TaskID, AssignedDate, Status) VALUES
(1, 1, 1, '2025-11-18', 'Pending'),      -- Room 101 -> Maria Santos
(2, 1, 2, '2025-11-18', 'Completed'),    -- Room 102 -> Maria Santos (completed)
(3, 2, 3, '2025-11-18', 'Pending'),      -- Room 103 -> John Smith
(4, 2, NULL, '2025-11-18', 'In Progress'), -- Room 104 -> John Smith (in progress)
(6, 3, 4, '2025-11-18', 'In Progress'),  -- Room 201 -> Lisa Johnson
(8, 4, 5, '2025-11-18', 'Pending'),      -- Room 203 -> Carlos Rodriguez
(9, 4, NULL, '2025-11-18', 'Pending'),   -- Room 204 -> Carlos Rodriguez
(11, 5, 6, '2025-11-18', 'In Progress'), -- Room 301 -> Sarah Williams
(15, 5, NULL, '2025-11-18', 'In Progress'); -- Room 305 -> Sarah Williams

-- Add some completed assignments from previous days
INSERT INTO assignments (RoomID, HousekeeperID, TaskID, AssignedDate, Status) VALUES
(2, 1, NULL, '2025-11-17', 'Completed'),
(7, 3, NULL, '2025-11-17', 'Completed'),
(12, 5, NULL, '2025-11-17', 'Completed'),
(10, 4, NULL, '2025-11-16', 'Completed'),
(14, 5, NULL, '2025-11-16', 'Completed');

-- Add some maintenance requests
INSERT INTO maintenancerequests (RoomID, Description, ReportedDate, Status) VALUES
(9, 'AC not working properly', '2025-11-17', 'In Progress'),
(5, 'Light bulb needs replacement in bathroom', '2025-11-18', 'Open'),
(11, 'Shower drain slow', '2025-11-16', 'Resolved'),
(3, 'Thermostat not responding', '2025-11-18', 'Open');

-- Add some cleaning times to completed rooms
UPDATE rooms SET CleaningTime = 1200 WHERE RoomNumber = 102; -- 20 minutes
UPDATE rooms SET CleaningTime = 1500 WHERE RoomNumber = 202; -- 25 minutes
UPDATE rooms SET CleaningTime = 1800 WHERE RoomNumber = 302; -- 30 minutes
UPDATE rooms SET CleaningTime = 1350 WHERE RoomNumber = 205; -- 22.5 minutes
UPDATE rooms SET CleaningTime = 1650 WHERE RoomNumber = 304; -- 27.5 minutes

-- Sample Data Complete!
-- 
-- Test Credentials:
-- Admin Login: admin / admin123
-- Staff Login: Maria Santos / maria123
--              John Smith / john123
--              Lisa Johnson / lisa123
--              Carlos Rodriguez / carlos123
--              Sarah Williams / sarah123
