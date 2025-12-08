# Shift Assignment System - Updated

## Overview
The schedule assignment system has been simplified to use dropdown selection of predefined shifts instead of manual day/time input.

## Database Schema

### shifts Table
- **ShiftID**: Primary key (auto-increment)
- **StartDay**: Enum (Monday through Sunday) - First day of shift
- **EndDay**: Enum (Monday through Sunday) - Last day of shift
- **StartTime**: Enum (12:00 AM through 11:00 PM) - Shift start time
- **EndTime**: Enum (01:00 AM through 12:00 AM) - Shift end time

### housekeepershifts Table (Junction)
- **HousekeeperShiftID**: Primary key (auto-increment)
- **HousekeeperID**: Foreign key to housekeepers table
- **ShiftID**: Foreign key to shifts table

## How It Works

### Admin Side
1. Navigate to Staff Management tab in admin dashboard
2. Each housekeeper row has a "Schedule" column with a dropdown
3. Dropdown shows all available shifts in format: "Monday - Friday, 08:00 AM - 04:00 PM"
4. Select a shift from the dropdown to assign it
5. Select "-- Select Shift --" to remove shift assignment
6. Changes are saved automatically on selection

### Creating Shifts
Shifts must be pre-created in the database. Use the provided `sample_shifts.sql` file:

```bash
# In phpMyAdmin or MySQL command line
mysql -u root -p webdb < sample_shifts.sql
```

Or manually insert shifts:
```sql
INSERT INTO shifts (StartDay, EndDay, StartTime, EndTime) 
VALUES ('Monday', 'Friday', '08:00 AM', '04:00 PM');
```

### Shift Display Format
- **Single day**: "Monday, 08:00 AM - 04:00 PM"
- **Day range**: "Monday - Friday, 08:00 AM - 04:00 PM"
- **Weekend**: "Saturday - Sunday, 09:00 AM - 05:00 PM"

## Files Modified

### Backend (PHP)
- **includes/fetch-Staff_table.php**: 
  - Removed old schedule display logic
  - Added dropdown with all available shifts
  - Pre-selects current shift if assigned

- **includes/update_schedule.php**: 
  - Simplified to accept staffID and shiftID
  - Deletes existing shift assignment
  - Creates new assignment if shiftID provided

### Frontend (JavaScript)
- **scripts/update-database.js**:
  - Removed old schedule editor click handlers
  - Removed formatSchedule() and parseSchedule() functions
  - Added change event handler for shiftSelect dropdown
  - Sends staffID and shiftID to backend

## API Endpoint

### POST /includes/update_schedule.php
**Parameters**:
- `staffID` (required): The HousekeeperID
- `shiftID` (optional): The ShiftID to assign, empty to remove assignment

**Responses**:
- `200 OK`: Assignment successful
- `404 Not Found`: Shift doesn't exist
- `400 Bad Request`: Missing staffID parameter
- `405 Method Not Allowed`: Not a POST request

## Common Shift Patterns

The `sample_shifts.sql` includes:
- **Weekday morning**: Monday-Friday, 6 AM - 2 PM
- **Weekday standard**: Monday-Friday, 9 AM - 5 PM
- **Weekend**: Saturday-Sunday, 8 AM - 4 PM
- **Split week**: Monday-Wednesday / Thursday-Sunday
- **Part-time**: Various 6-hour shifts
- **Evening**: Monday-Friday, 2 PM - 10 PM

## Adding Custom Shifts

To add a new shift pattern:

```sql
INSERT INTO shifts (StartDay, EndDay, StartTime, EndTime) 
VALUES ('Monday', 'Thursday', '10:00 AM', '06:00 PM');
```

The new shift will automatically appear in the dropdown for all housekeepers.

## Migration from Old System

If you have existing schedule assignments using the old checkbox/time input system:

1. Run `sample_shifts.sql` to populate shifts table
2. Existing housekeepershifts records may need to be updated to reference new ShiftIDs
3. Old schedule strings are no longer used

## Advantages of New System

1. **Consistency**: All housekeepers use standard shift patterns
2. **Simplicity**: One-click assignment instead of multi-step input
3. **Management**: Easy to see who's on which shift
4. **Flexibility**: Admin can pre-define any shift pattern
5. **Validation**: No invalid time/day combinations

## Troubleshooting

**Dropdown is empty**: 
- Check if shifts exist: `SELECT * FROM shifts;`
- Run `sample_shifts.sql` to populate shifts

**Assignment not saving**:
- Check browser console for JavaScript errors
- Verify housekeepershifts table exists
- Check PHP error log at `c:\xampp\apache\logs\error.log`

**Shift not showing as selected**:
- Verify housekeepershifts record exists: 
  ```sql
  SELECT * FROM housekeepershifts WHERE HousekeeperID = ?;
  ```

## Testing

1. Go to Admin Dashboard → Staff Management
2. Click on shift dropdown for any housekeeper
3. Select a shift (e.g., "Monday - Friday, 08:00 AM - 04:00 PM")
4. Verify shift is selected in dropdown
5. Refresh page - shift should still be selected
6. Check database:
   ```sql
   SELECT h.FullName, s.StartDay, s.EndDay, s.StartTime, s.EndTime
   FROM housekeepers h
   JOIN housekeepershifts hs ON h.HousekeeperID = hs.HousekeeperID
   JOIN shifts s ON hs.ShiftID = s.ShiftID;
   ```
