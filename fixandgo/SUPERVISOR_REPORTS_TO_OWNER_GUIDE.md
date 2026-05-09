# Supervisor Reports to Owner Feature Guide

## Overview
Supervisors can now send monthly inventory reports to the owner, allowing owners to oversee the supervisor's work and track product flow. This creates accountability and transparency in the inventory management process.

## Features

### Supervisor Side
1. **Monthly Report Generation**
   - View products received from owner in any month
   - See statistics: total products, quantity, value, categories
   - Detailed product table with dates, names, prices

2. **Send to Owner**
   - One-click send button
   - Confirmation dialog before sending
   - Success/error feedback
   - Automatic report creation/update

### Owner Side
1. **View All Reports**
   - Grid view of all received reports
   - Organized by month and year
   - Quick statistics on each card
   - Click to view full details

2. **Report Details**
   - Complete product breakdown
   - Statistics summary
   - Sortable product table
   - Export-ready format

## User Workflows

### Supervisor: Send Report
1. **Login** as supervisor
2. **Navigate** to Reports page
3. **Select** month using navigation buttons
4. **Review** report data and statistics
5. **Click** "Send to Owner" button
6. **Confirm** in dialog
7. **Success**: Report sent to owner

### Owner: View Reports
1. **Login** as owner
2. **Navigate** to Dashboard
3. **Click** "Supervisor Reports" quick action
4. **View** all received reports in grid
5. **Click** any report card to see details
6. **Review** product breakdown and statistics

## Product Status Flow

```
Owner Creates/Purchases Product
         ↓
Owner Sends to Supervisor (status: 'sent_to_supervisor')
         ↓
Supervisor Reviews Products
         ↓
Supervisor Generates Monthly Report
         ↓
Supervisor Sends Report to Owner
         ↓
Owner Reviews Supervisor's Work
```

## Technical Details

### Database Schema

#### supervisor_reports Table
```sql
CREATE TABLE supervisor_reports (
  id              INT UNSIGNED    PRIMARY KEY AUTO_INCREMENT,
  supervisor_id   INT UNSIGNED    NOT NULL,
  owner_id        INT UNSIGNED    NOT NULL,
  report_year     INT             NOT NULL,
  report_month    INT             NOT NULL,
  total_products  INT             NOT NULL DEFAULT 0,
  total_quantity  INT             NOT NULL DEFAULT 0,
  total_value     DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  report_data     JSON            NULL,
  sent_at         DATETIME        NULL,
  created_at      DATETIME        NOT NULL,
  updated_at      DATETIME        NOT NULL,
  
  UNIQUE KEY (supervisor_id, owner_id, report_year, report_month),
  FOREIGN KEY (supervisor_id) REFERENCES users(id),
  FOREIGN KEY (owner_id) REFERENCES users(id)
);
```

### API Endpoints

#### Supervisor: Send Report
```
POST /backend/supervisor_reports.php
Content-Type: application/json

{
  "action": "send_to_owner",
  "year": 2026,
  "month": 5
}
```

**Response (Success)**:
```json
{
  "success": true,
  "message": "Report sent to owner successfully!",
  "report_id": 123
}
```

#### Owner: List Reports
```
GET /backend/owner_supervisor_reports.php?action=list
```

**Response**:
```json
{
  "success": true,
  "reports": [
    {
      "id": 123,
      "supervisor_id": 5,
      "report_year": 2026,
      "report_month": 5,
      "total_products": 15,
      "total_quantity": 250,
      "total_value": 125000.00,
      "report_data": "[...]",
      "sent_at": "2026-05-01 10:30:00",
      "supervisor_name": "John Doe",
      "supervisor_email": "john@example.com"
    }
  ],
  "total": 1
}
```

### Report Data Structure

The `report_data` JSON field contains:
```json
[
  {
    "id": 1,
    "category": "Screens",
    "brand": "Samsung",
    "item_description": "SAMSUNG A37 LCD OLED TYPE",
    "qty": 100,
    "srp": 1500.00,
    "updated_at": "2026-05-15 14:30:00"
  },
  ...
]
```

## UI Components

### Supervisor: Reports Page
```
┌─────────────────────────────────────────────────────────┐
│ Monthly Product Report    [Send to Owner] [◄] May 2026 [►]│
├─────────────────────────────────────────────────────────┤
│ [15 Products] [250 Qty] [₱125,000] [5 Categories]      │
├─────────────────────────────────────────────────────────┤
│ Date       | Product | Category | Qty | Price | Total  │
│ May 15     | LCD     | Screens  | 100 | ₱1,500| ₱150k  │
│ ...                                                      │
└─────────────────────────────────────────────────────────┘
```

### Owner: Reports Grid
```
┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│ May 2026     │ │ Apr 2026     │ │ Mar 2026     │
│ Sent: May 31 │ │ Sent: Apr 30 │ │ Sent: Mar 31 │
├──────────────┤ ├──────────────┤ ├──────────────┤
│ 15 Products  │ │ 12 Products  │ │ 18 Products  │
│ 250 Quantity │ │ 180 Quantity │ │ 300 Quantity │
│ ₱125,000     │ │ ₱95,000      │ │ ₱150,000     │
└──────────────┘ └──────────────┘ └──────────────┘
```

## Database Migration

### Required Migration
Run this SQL file to create the reports table:

```bash
# File: backend/migrate_supervisor_reports.sql
```

**⚠️ IMPORTANT**: This migration must be run before using the feature!

## Files Created/Modified

### Created
- ✅ `fixandgo/backend/migrate_supervisor_reports.sql` - Database migration
- ✅ `fixandgo/views/user/owner/supervisor-reports.html` - Owner reports view
- ✅ `fixandgo/views/user/owner/supervisor-reports.js` - Owner reports logic
- ✅ `fixandgo/backend/owner_supervisor_reports.php` - Owner API
- ✅ `fixandgo/SUPERVISOR_REPORTS_TO_OWNER_GUIDE.md` - Documentation

### Modified
- ✅ `fixandgo/views/user/supervisor/reports.html` - Added send button
- ✅ `fixandgo/views/user/supervisor/reports.js` - Added send logic
- ✅ `fixandgo/backend/supervisor_reports.php` - Added send endpoint
- ✅ `fixandgo/assets/js/dashboard.js` - Added owner quick action

## Testing Checklist

### Supervisor Side
- [ ] Migration run successfully
- [ ] Can view monthly reports
- [ ] Can navigate between months
- [ ] Statistics display correctly
- [ ] Product table shows all data
- [ ] "Send to Owner" button visible
- [ ] Confirmation dialog appears
- [ ] Success message shows after sending
- [ ] Can resend same month (updates existing)

### Owner Side
- [ ] "Supervisor Reports" link visible on dashboard
- [ ] Can view all received reports
- [ ] Reports display in grid format
- [ ] Statistics show correctly on cards
- [ ] Can click card to view details
- [ ] Modal shows full report data
- [ ] Product table displays correctly
- [ ] Can close modal
- [ ] Empty state shows when no reports

### Database
- [ ] `supervisor_reports` table created
- [ ] Reports saved correctly
- [ ] Unique constraint works (one report per month)
- [ ] Foreign keys working
- [ ] JSON data stored properly

## Benefits

### For Supervisors
- ✅ Easy monthly reporting
- ✅ Automated data collection
- ✅ Professional presentation
- ✅ Accountability tracking

### For Owners
- ✅ Oversight of supervisor work
- ✅ Monthly inventory tracking
- ✅ Performance monitoring
- ✅ Historical data access
- ✅ Decision-making insights

## Future Enhancements

- [ ] Export reports to PDF/Excel
- [ ] Email notifications when report sent
- [ ] Report comments/feedback from owner
- [ ] Comparison between months
- [ ] Trend analysis and charts
- [ ] Automated monthly report generation
- [ ] Report approval workflow
- [ ] Custom report date ranges
- [ ] Multiple supervisors support
- [ ] Report templates

## Troubleshooting

### Issue: "Send to Owner" button not working
- **Cause**: No products in selected month
- **Solution**: Select a month with products

### Issue: Reports not appearing for owner
- **Cause**: Report not sent or database issue
- **Solution**: Check supervisor sent report, verify database

### Issue: Duplicate report error
- **Cause**: Trying to send same month twice
- **Solution**: Feature updates existing report automatically

### Issue: Empty report data
- **Cause**: No products with status 'sent_to_supervisor'
- **Solution**: Owner must send products to supervisor first

## Support

For issues or questions:
1. Check browser console for JavaScript errors
2. Check PHP error logs for backend issues
3. Verify migration was run successfully
4. Ensure products have correct status
5. Check user roles are correct

---

**Status**: ✅ Feature Complete and Ready for Testing
