# Dosage Reminders System Guide

## Overview
The Dosage Reminders System helps healthcare staff manage and track patient medication schedules. It provides automated reminders, dosage logging, and compliance tracking to ensure patients take their medications on time.

## Features

### 1. Create Dosage Reminders
- **Location**: `/staff/dosage-reminders`
- **Form Fields**:
  - **Patient**: Select from existing patients
  - **Medicine**: Select from available medicines
  - **Prescription**: Link to existing prescription
  - **Dosage Instruction**: e.g., "1 tablet twice daily"
  - **Reminder Time**: Time of day for reminders
  - **Frequency**: Daily, twice daily, thrice daily, weekly, or custom
  - **Start/End Date**: Reminder period
  - **Notification Options**: SMS and email reminders
  - **Notes**: Additional information

### 2. View All Reminders
- **Table displays**:
  - Patient name
  - Medicine name and brand
  - Dosage instructions
  - Reminder time
  - Frequency
  - Active period
  - Status (Active/Inactive)
  - Action buttons

### 3. Today's Reminders
- **Location**: `/staff/today-reminders`
- **Features**:
  - View all pending reminders for today
  - Mark medications as taken or skipped
  - Visual indicators for overdue medications
  - Summary statistics

### 4. Dosage Logging
- **Track medication compliance**:
  - Mark as "Taken" with timestamp
  - Mark as "Skipped" if patient chooses not to take
  - Automatic "Missed" status for overdue medications
  - Complete audit trail

## Database Tables

### 1. prescriptions (existing)
```sql
- id
- patient_id (foreign key to patients)
- medicine_id (foreign key to medicines)
- dosage (e.g., "1 tablet twice daily")
- duration (in days)
- start_date
- created_at, updated_at
```

### 2. dosage_reminders (new)
```sql
- id
- patient_id (foreign key to patients)
- prescription_id (foreign key to prescriptions)
- medicine_id (foreign key to medicines)
- dosage_instruction
- reminder_time (time of day)
- frequency (daily, twice_daily, thrice_daily, weekly, custom)
- custom_schedule (JSON for custom schedules)
- start_date, end_date
- is_active (boolean)
- send_sms, send_email (boolean)
- notes
- created_at, updated_at
```

### 3. dosage_logs (new)
```sql
- id
- dosage_reminder_id (foreign key to dosage_reminders)
- patient_id (foreign key to patients)
- medicine_id (foreign key to medicines)
- scheduled_date, scheduled_time
- taken_at (nullable timestamp)
- status (pending, taken, missed, skipped)
- notes
- created_at, updated_at
```

## Routes

- `GET /staff/dosage-reminders` - View all reminders
- `POST /dosage-reminders` - Create new reminder
- `GET /dosage-reminders/{id}` - View reminder details
- `PUT /dosage-reminders/{id}` - Update reminder
- `DELETE /dosage-reminders/{id}` - Delete reminder
- `PATCH /dosage-logs/{id}/taken` - Mark as taken
- `PATCH /dosage-logs/{id}/skipped` - Mark as skipped
- `GET /staff/today-reminders` - Today's pending reminders
- `GET /staff/patient-reminders/{patientId}` - Patient-specific reminders

## Models

### Prescription Model
- Relationships: `belongsTo(Patient::class)`, `belongsTo(Medicine::class)`, `hasMany(DosageReminder::class)`
- Methods: `getEndDateAttribute()`, `isActive()`

### DosageReminder Model
- Relationships: `belongsTo(Patient::class)`, `belongsTo(Prescription::class)`, `belongsTo(Medicine::class)`, `hasMany(DosageLog::class)`
- Methods: `isActive()`, `getNextReminderTime()`, `getFrequencyText()`

### DosageLog Model
- Relationships: `belongsTo(DosageReminder::class)`, `belongsTo(Patient::class)`, `belongsTo(Medicine::class)`
- Methods: `isOverdue()`, `getStatusBadgeClass()`

## Controller Methods

### DosageReminderController

1. **index()** - Display all reminders with patient and medicine data
2. **store()** - Create new reminder with automatic log generation
3. **show()** - Display reminder details with dosage logs
4. **update()** - Update reminder and regenerate logs if needed
5. **destroy()** - Delete reminder and associated logs
6. **markAsTaken()** - Mark dosage as taken with timestamp
7. **markAsSkipped()** - Mark dosage as skipped
8. **getTodayReminders()** - Display today's pending reminders
9. **getPatientReminders()** - Patient-specific reminder view

## Frequency Options

- **Daily**: Once per day at specified time
- **Twice Daily**: Morning and evening (12 hours apart)
- **Thrice Daily**: Morning, afternoon, evening (8 hours apart)
- **Weekly**: Once per week
- **Custom**: User-defined schedule (JSON format)

## Status Tracking

### DosageLog Statuses:
- **Pending**: Scheduled but not yet taken
- **Taken**: Medication taken with timestamp
- **Missed**: Overdue (2+ hours past scheduled time)
- **Skipped**: Patient chose not to take

### Reminder Statuses:
- **Active**: Currently within start/end date range
- **Inactive**: Outside active period

## UI Features

- **Responsive Design**: Works on desktop and mobile
- **Status Indicators**: Color-coded badges for different statuses
- **Overdue Highlighting**: Red background for overdue medications
- **Action Buttons**: Context-sensitive actions
- **Summary Statistics**: Visual stats for today's reminders
- **Form Validation**: Client-side and server-side validation
- **Success/Error Messages**: User feedback for all actions

## Sample Data

The system includes sample prescriptions and reminders:
- Paracetamol: 1 tablet twice daily for 7 days
- Ibuprofen: 1 tablet once daily for 14 days
- Amoxicillin: 1 tablet three times daily for 10 days

## Integration

### With Existing Systems:
- **Medicine Pick-Up**: Links to medicine inventory
- **Patient Management**: Uses existing patient data
- **Prescription System**: Integrates with prescription records

### Future Enhancements:
- **SMS/Email Notifications**: Automated reminder delivery
- **Mobile App**: Patient-facing reminder interface
- **Compliance Reports**: Detailed adherence analytics
- **Refill Reminders**: Automatic refill notifications

## Access

Navigate to the Dosage Reminders page through the staff sidebar menu. The system is fully integrated with the existing Laravel application structure and provides comprehensive medication management capabilities. 