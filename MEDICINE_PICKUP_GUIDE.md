# Medicine Pick-Up System Guide

## Overview
The Medicine Pick-Up System allows staff to schedule and manage medicine pickups for patients. The system tracks which patient picks up which medicine, when it's scheduled, and when it's actually picked up.

## Features

### 1. Schedule New Pickup
- **Location**: `/staff/medicine-pickups`
- **Form Fields**:
  - **Patient**: Select from existing patients
  - **Medicine**: Select from available medicines (shows current stock)
  - **Quantity**: Number of units to pick up
  - **Scheduled Date**: When the pickup is scheduled for

### 2. View Pickup Requests
- **Table displays**:
  - Patient name
  - Medicine name and brand
  - Quantity
  - Scheduled date
  - Pickup date (if completed)
  - Status (Pending/Picked Up)
  - Action buttons

### 3. Mark as Picked Up
- **Action**: Click "Mark as Picked Up" button
- **What happens**:
  - Status changes from "pending" to "picked_up"
  - Pickup date is set to current date/time
  - Medicine stock quantity is reduced by the pickup quantity
  - Pickup is logged in history

### 4. Cancel Pickup
- **Action**: Click "Cancel" button
- **What happens**: Pickup request is deleted from the system

## Database Tables

### 1. medicines (existing)
```sql
- id
- name
- brand
- dosage
- stock_quantity
- expiration_date
- created_at
- updated_at
```

### 2. medicine_pickups (new)
```sql
- id
- patient_id (foreign key to patients)
- medicine_id (foreign key to medicines)
- quantity
- scheduled_date
- pickup_date (nullable)
- status (pending/picked_up)
- created_at
- updated_at
```

## Routes

- `GET /staff/medicine-pickups` - View all pickups
- `POST /medicine-pickups` - Create new pickup
- `PATCH /medicine-pickups/{id}/pickup` - Mark as picked up
- `DELETE /medicine-pickups/{id}` - Cancel pickup

## Models

### Medicine Model
- Relationships: `hasMany(MedicinePickup::class)`
- Fillable fields: name, brand, dosage, stock_quantity, expiration_date

### MedicinePickup Model
- Relationships: `belongsTo(Patient::class)`, `belongsTo(Medicine::class)`
- Fillable fields: patient_id, medicine_id, quantity, scheduled_date, pickup_date, status

### Patient Model
- Added relationship: `hasMany(MedicinePickup::class)`

## Controller Methods

### MedicinePickupController

1. **index()** - Display all pickups with patient and medicine data
2. **store()** - Create new pickup with validation
3. **markAsPickedUp()** - Mark pickup as complete and reduce stock
4. **destroy()** - Cancel/delete pickup

## Validation Rules

- `patient_id`: Required, must exist in patients table
- `medicine_id`: Required, must exist in medicines table
- `quantity`: Required, integer, minimum 1
- `scheduled_date`: Required, valid date

## Stock Management

- When scheduling a pickup, the system checks if there's sufficient stock
- When marking as picked up, the stock quantity is automatically reduced
- Stock validation prevents scheduling pickups for unavailable quantities

## UI Features

- **Responsive Design**: Works on desktop and mobile
- **Status Indicators**: Color-coded status badges
- **Action Buttons**: Context-sensitive actions based on pickup status
- **Form Validation**: Client-side and server-side validation
- **Success/Error Messages**: User feedback for all actions

## Sample Data

The system includes sample medicines:
- Paracetamol (Tylenol) - 500mg
- Ibuprofen (Advil) - 400mg
- Amoxicillin (Amoxil) - 250mg
- Omeprazole (Prilosec) - 20mg
- Metformin (Glucophage) - 500mg

## Access

Navigate to the Medicine Pick-Up page through the staff sidebar menu. The system is fully integrated with the existing Laravel application structure. 