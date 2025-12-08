-- Sample shifts for hotel housekeeping
-- This provides common shift patterns that can be assigned to housekeepers

-- Morning shifts (weekdays)
INSERT INTO shifts (StartDay, EndDay, StartTime, EndTime) VALUES
('Monday', 'Friday', '06:00 AM', '02:00 PM'),
('Monday', 'Friday', '07:00 AM', '03:00 PM'),
('Monday', 'Friday', '08:00 AM', '04:00 PM');

-- Afternoon/Evening shifts (weekdays)
INSERT INTO shifts (StartDay, EndDay, StartTime, EndTime) VALUES
('Monday', 'Friday', '02:00 PM', '10:00 PM'),
('Monday', 'Friday', '03:00 PM', '11:00 PM');

-- Full-time weekday shifts
INSERT INTO shifts (StartDay, EndDay, StartTime, EndTime) VALUES
('Monday', 'Friday', '09:00 AM', '05:00 PM'),
('Monday', 'Friday', '08:00 AM', '05:00 PM');

-- Weekend shifts
INSERT INTO shifts (StartDay, EndDay, StartTime, EndTime) VALUES
('Saturday', 'Sunday', '08:00 AM', '04:00 PM'),
('Saturday', 'Sunday', '09:00 AM', '05:00 PM'),
('Saturday', 'Sunday', '07:00 AM', '03:00 PM');

-- Monday-Wednesday shifts
INSERT INTO shifts (StartDay, EndDay, StartTime, EndTime) VALUES
('Monday', 'Wednesday', '08:00 AM', '04:00 PM'),
('Monday', 'Wednesday', '09:00 AM', '05:00 PM');

-- Thursday-Sunday shifts
INSERT INTO shifts (StartDay, EndDay, StartTime, EndTime) VALUES
('Thursday', 'Sunday', '08:00 AM', '04:00 PM'),
('Thursday', 'Sunday', '09:00 AM', '05:00 PM');

-- Part-time shifts (3 days)
INSERT INTO shifts (StartDay, EndDay, StartTime, EndTime) VALUES
('Monday', 'Wednesday', '06:00 AM', '12:00 PM'),
('Thursday', 'Saturday', '06:00 AM', '12:00 PM');

-- Split shifts
INSERT INTO shifts (StartDay, EndDay, StartTime, EndTime) VALUES
('Monday', 'Friday', '06:00 AM', '10:00 AM'),
('Monday', 'Friday', '04:00 PM', '08:00 PM');

-- Note: You can add more custom shifts as needed
-- Each shift represents a work schedule that can be assigned to housekeepers
