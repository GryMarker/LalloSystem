# Follow-Up Checkups System Guide

## Overview
The Follow-Up Checkups System allows healthcare staff to schedule, manage, and track patient follow-up appointments. It provides comprehensive follow-up management with priority levels, status tracking, and integration with health monitoring records to ensure proper patient care continuity.

## Features

### 1. Schedule Follow-up Checkups
- **Location**: `/staff/follow-up-checkups`
- **Comprehensive Form Fields**:
  - **Patient Selection**: Choose from existing patients
  - **Health Monitoring Link**: Optional link to health monitoring records
  - **Staff Assignment**: Assign specific staff members
  - **Reason & Description**: Detailed explanation of follow-up need
  - **Follow-up Type**: General, specialist, lab test, imaging, medication review
  - **Priority Levels**: Low, medium, high, urgent
  - **Scheduling**: Date, time, duration, location
  - **Urgent Flagging**: Mark urgent cases with reason
  - **Notes**: Additional information and instructions

### 2. View Follow-up Checkups
- **Table displays**:
  - Patient name and assigned staff
  - Reason for follow-up with urgent indicators
  - Follow-up type and scheduled date/time
  - Priority and status badges
  - Action buttons for management
  - Overdue highlighting

### 3. Priority Management
- **Priority Levels**:
  - **Low**: Routine follow-ups, non-urgent cases
  - **Medium**: Standard follow-ups requiring attention
  - **High**: Important follow-ups needing prompt attention
  - **Urgent**: Critical cases requiring immediate action
- **Urgent Flagging**: Additional urgent status with reason documentation

### 4. Status Tracking
- **Status Types**:
  - **Scheduled**: Follow-up is planned
  - **Confirmed**: Patient has confirmed attendance
  - **In Progress**: Follow-up is currently happening
  - **Completed**: Follow-up finished with outcomes recorded
  - **Cancelled**: Follow-up was cancelled with reason
  - **No Show**: Patient didn't attend

### 5. Follow-up Types
- **General Checkup**: Routine health assessment
- **Specialist Consultation**: Referral to specialist
- **Laboratory Test**: Blood work or other lab tests
- **Imaging Study**: X-rays, MRIs, CT scans
- **Medication Review**: Prescription and dosage review

## Database Tables

### follow_up_checkups
```sql
- id
- patient_id (foreign key to patients)
- health_monitoring_id (foreign key to health_monitorings)
- appointment_id (foreign key to appointments)
- staff_id (foreign key to users)
- reason (string)
- description (text)
- priority (low/medium/high/urgent)
- type (general/specialist/lab_test/imaging/medication_review)
- scheduled_date, scheduled_time
- duration (minutes)
- location (string)
- status (scheduled/confirmed/in_progress/completed/cancelled/no_show)
- completed_at, cancelled_at (timestamps)
- findings (text)
- recommendations (text)
- next_steps (text)
- requires_additional_follow_up (boolean)
- next_follow_up_date (date)
- notes (text)
- attachments (JSON)
- is_urgent (boolean)
- urgent_reason (text)
- created_at, updated_at
```

## Routes

- `GET /staff/follow-up-checkups` - View all follow-ups
- `POST /follow-up-checkups` - Create new follow-up
- `GET /follow-up-checkups/{id}` - View follow-up details
- `PUT /follow-up-checkups/{id}` - Update follow-up
- `DELETE /follow-up-checkups/{id}` - Delete follow-up
- `PATCH /follow-up-checkups/{id}/complete` - Mark as completed
- `PATCH /follow-up-checkups/{id}/cancel` - Mark as cancelled
- `PATCH /follow-up-checkups/{id}/no-show` - Mark as no-show
- `GET /staff/urgent-follow-ups` - View urgent follow-ups
- `GET /staff/overdue-follow-ups` - View overdue follow-ups
- `GET /staff/today-follow-ups` - View today's follow-ups
- `GET /staff/patient-follow-ups/{patientId}` - Patient-specific follow-ups
- `GET /staff/create-follow-up-from-health/{healthMonitoringId}` - Create from health record
- `POST /follow-up-checkups/report` - Generate follow-up reports

## Models

### FollowUpCheckup Model
- **Relationships**: 
  - `belongsTo(Patient::class)`
  - `belongsTo(HealthMonitoring::class)`
  - `belongsTo(Appointment::class)`
  - `belongsTo(User::class, 'staff_id')`
- **Key Methods**:
  - `getStatusBadgeClass()` - CSS classes for status badges
  - `getPriorityBadgeClass()` - CSS classes for priority badges
  - `getTypeText()` - Human-readable type descriptions
  - `getPriorityText()` - Human-readable priority descriptions
  - `isOverdue()` - Check if follow-up is overdue
  - `isToday()` - Check if scheduled for today
  - `isUpcoming()` - Check if scheduled for future
  - `getScheduledDateTime()` - Get combined date/time
  - `getDurationText()` - Formatted duration display
  - `markAsCompleted()` - Mark follow-up as completed
  - `markAsCancelled()` - Mark follow-up as cancelled
  - `markAsNoShow()` - Mark follow-up as no-show
  - `isUrgent()` - Check if urgent priority
  - `getAttachmentsText()` - Display attachments
  - **Query Scopes**: `urgent()`, `overdue()`, `today()`, `upcoming()`, `byPriority()`, `byStatus()`

## Controller Methods

### FollowUpCheckupController

1. **index()** - Display all follow-ups with summary statistics
2. **store()** - Create new follow-up with validation
3. **show()** - Display detailed follow-up with patient history
4. **update()** - Update follow-up and handle status changes
5. **destroy()** - Delete follow-up
6. **markAsCompleted()** - Mark follow-up as completed
7. **markAsCancelled()** - Mark follow-up as cancelled with reason
8. **markAsNoShow()** - Mark follow-up as no-show
9. **getUrgentFollowUps()** - Display urgent follow-ups
10. **getOverdueFollowUps()** - Display overdue follow-ups
11. **getTodayFollowUps()** - Display today's follow-ups
12. **getPatientFollowUps()** - Patient-specific follow-up history
13. **createFromHealthMonitoring()** - Create follow-up from health record
14. **generateReport()** - Generate follow-up analytics reports

## Priority Management

### Priority Levels:
- **Low Priority**: Green badge, routine follow-ups
- **Medium Priority**: Yellow badge, standard follow-ups
- **High Priority**: Orange badge, important follow-ups
- **Urgent Priority**: Red badge, critical cases

### Urgent Flagging:
- Additional urgent status beyond priority
- Requires urgent reason documentation
- Special highlighting in interface
- Prioritized in urgent follow-ups view

## Status Management

### Status Workflow:
1. **Scheduled** → **Confirmed** → **In Progress** → **Completed**
2. **Scheduled** → **Cancelled** (with reason)
3. **Scheduled** → **No Show** (patient didn't attend)

### Status Actions:
- **Complete**: Records findings, recommendations, next steps
- **Cancel**: Records cancellation reason and timestamp
- **No Show**: Marks patient as not attending

## Follow-up Types

### Type Categories:
- **General Checkup**: Routine health assessment
- **Specialist Consultation**: Referral to medical specialist
- **Laboratory Test**: Blood work, urine tests, etc.
- **Imaging Study**: X-rays, ultrasounds, MRIs, CT scans
- **Medication Review**: Prescription and dosage evaluation

## Integration Features

### With Health Monitoring:
- Link follow-ups to health monitoring records
- Automatic follow-up flagging from health records
- Health status context in follow-up details
- Integration with health monitoring alerts

### With Appointments:
- Link to existing appointment system
- Coordinate scheduling between systems
- Unified patient care timeline

### With Patient Management:
- Patient history integration
- Previous follow-up tracking
- Patient-specific follow-up timeline

## UI Features

- **Responsive Design**: Works on desktop and mobile
- **Status Indicators**: Color-coded badges for all statuses
- **Priority Highlighting**: Visual priority indicators
- **Overdue Alerts**: Red highlighting for overdue follow-ups
- **Summary Statistics**: Dashboard with key metrics
- **Quick Actions**: Easy access to urgent/overdue/today views
- **Form Validation**: Client-side and server-side validation
- **Smart Scheduling**: Date/time validation and constraints
- **Urgent Flagging**: Dynamic form fields for urgent cases

## Sample Data

The system includes sample follow-ups with:
- **High Priority**: Blood pressure monitoring
- **Urgent**: Chest pain evaluation
- **Medium Priority**: Blood sugar control review
- **Low Priority**: Routine lab work
- **Completed**: Follow-up with outcomes recorded
- **No Show**: Missed appointment tracking

## Quick Actions

### Dashboard Features:
- **Urgent Follow-ups**: View all urgent cases
- **Overdue Follow-ups**: View missed appointments
- **Today's Follow-ups**: View today's schedule
- **Patient History**: View patient-specific follow-ups

### Management Actions:
- **Schedule New**: Create follow-up from scratch
- **Create from Health**: Link to health monitoring records
- **Mark Complete**: Record outcomes and recommendations
- **Cancel/Reschedule**: Handle appointment changes
- **Generate Reports**: Analytics and statistics

## Integration

### With Existing Systems:
- **Health Monitoring**: Automatic follow-up creation
- **Patient Management**: Patient data integration
- **Appointment System**: Unified scheduling
- **Staff Management**: Staff assignment and tracking

### Future Enhancements:
- **Automated Reminders**: Patient notification system
- **Calendar Integration**: External calendar sync
- **Mobile App**: Patient-facing follow-up management
- **Telemedicine**: Virtual follow-up appointments
- **Analytics Dashboard**: Advanced reporting and insights
- **Workflow Automation**: Automated follow-up scheduling

## Access

Navigate to the Follow-Up Checkups page through the staff sidebar menu. The system is fully integrated with the existing Laravel application structure and provides comprehensive follow-up management capabilities for improved patient care continuity. 