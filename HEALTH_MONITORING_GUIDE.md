# Health Monitoring System Guide

## Overview
The Health Monitoring System allows healthcare staff to record, track, and analyze patient vital signs and health metrics. It provides comprehensive health assessment, trend analysis, and automated status evaluation to ensure timely intervention when needed.

## Features

### 1. Record Health Data
- **Location**: `/staff/health-monitoring`
- **Comprehensive Form Fields**:
  - **Patient Selection**: Choose from existing patients
  - **Date & Time**: Record when measurements were taken
  - **Vital Signs**: Temperature, blood pressure, pulse rate, respiratory rate, oxygen saturation
  - **Body Measurements**: Weight, height, BMI (auto-calculated)
  - **Additional Metrics**: Blood sugar, pain level, pain location
  - **Mental Health**: Mood score and notes
  - **Symptoms**: Checkbox selection for common symptoms
  - **Clinical Notes**: Observations and additional notes
  - **Follow-up Flag**: Mark if patient requires follow-up

### 2. View Health Records
- **Table displays**:
  - Patient name and record date/time
  - Key vital signs summary
  - BMI with category classification
  - Pain and mood assessment
  - Status indicators (Normal/Warning/Critical)
  - Action buttons for detailed view

### 3. Health Status Assessment
- **Automatic Evaluation**: System assesses vital signs and assigns status
- **Status Levels**:
  - **Normal**: All vital signs within normal ranges
  - **Warning**: Some values outside normal range but not critical
  - **Critical**: Values requiring immediate attention
- **Alert System**: Automatic generation of alert notes for abnormal values

### 4. Patient Health History
- **Location**: `/staff/patient-health-history/{patientId}`
- **Features**:
  - Complete health record timeline
  - Trend analysis and charts
  - Progress tracking over time
  - Comparison with previous records

### 5. Critical Alerts
- **Location**: `/staff/critical-alerts`
- **Features**:
  - View all critical health records
  - Prioritized patient care
  - Immediate attention required cases

## Database Tables

### health_monitorings
```sql
- id
- patient_id (foreign key to patients)
- recorded_date, recorded_time
- temperature (decimal 4,1 - Celsius)
- blood_pressure_systolic, blood_pressure_diastolic (mmHg)
- pulse_rate (beats per minute)
- respiratory_rate (breaths per minute)
- oxygen_saturation (SpO2 %)
- weight, height (kg, cm)
- bmi (calculated)
- blood_sugar (mg/dL)
- pain_level (0-10 scale)
- pain_location (string)
- mood_score (1-10 scale)
- mood_notes (text)
- symptoms (JSON array)
- observations, notes (text)
- status (normal/warning/critical)
- requires_follow_up (boolean)
- alert_notes (text)
- created_at, updated_at
```

## Routes

- `GET /staff/health-monitoring` - View all health records
- `POST /health-monitoring` - Create new health record
- `GET /health-monitoring/{id}` - View record details
- `PUT /health-monitoring/{id}` - Update health record
- `DELETE /health-monitoring/{id}` - Delete health record
- `GET /staff/patient-health-history/{patientId}` - Patient health history
- `GET /staff/critical-alerts` - Critical health alerts
- `GET /staff/today-health-records` - Today's health records
- `POST /health-monitoring/report` - Generate health reports

## Models

### HealthMonitoring Model
- **Relationships**: `belongsTo(Patient::class)`
- **Key Methods**:
  - `getStatusBadgeClass()` - CSS classes for status badges
  - `getBloodPressureText()` - Formatted BP display
  - `getBmiCategory()` - BMI classification
  - `getBmiCategoryClass()` - CSS classes for BMI categories
  - `isTemperatureNormal()` - Temperature assessment
  - `isBloodPressureNormal()` - BP assessment
  - `isPulseRateNormal()` - Pulse assessment
  - `isOxygenSaturationNormal()` - O2 assessment
  - `getPainLevelText()` - Pain level description
  - `getMoodText()` - Mood description
  - `getSymptomsText()` - Symptoms display
  - `calculateBmi()` - Auto-calculate BMI
  - `assessStatus()` - Automatic status assessment

## Controller Methods

### HealthMonitoringController

1. **index()** - Display all health records with summary statistics
2. **store()** - Create new health record with validation and auto-assessment
3. **show()** - Display detailed health record with recent history
4. **update()** - Update health record and reassess status
5. **destroy()** - Delete health record
6. **getPatientHistory()** - Patient-specific health timeline
7. **getCriticalAlerts()** - Critical health cases
8. **getTrendData()** - Generate trend data for charts
9. **getTodayRecords()** - Today's health records
10. **generateReport()** - Generate health reports with averages

## Health Assessment Criteria

### Normal Ranges:
- **Temperature**: 36.0°C - 37.5°C
- **Blood Pressure**: < 140/90 mmHg
- **Pulse Rate**: 60-100 bpm
- **Oxygen Saturation**: ≥ 95%
- **Pain Level**: 0-6 (0-10 scale)

### Warning Ranges:
- **Temperature**: 37.5°C - 38.0°C or 36.0°C - 35.0°C
- **Blood Pressure**: 140/90 - 179/109 mmHg
- **Pulse Rate**: 100-120 bpm or 50-60 bpm
- **Oxygen Saturation**: 90-94%
- **Pain Level**: 7-10

### Critical Ranges:
- **Temperature**: > 38.0°C or < 35.0°C
- **Blood Pressure**: ≥ 180/110 mmHg
- **Pulse Rate**: > 120 bpm or < 50 bpm
- **Oxygen Saturation**: < 90%

## BMI Categories

- **Underweight**: < 18.5
- **Normal weight**: 18.5 - 24.9
- **Overweight**: 25.0 - 29.9
- **Obese**: ≥ 30.0

## Pain Level Descriptions

- **0**: No pain
- **1-2**: Mild pain
- **3-4**: Moderate pain
- **5-6**: Moderately severe pain
- **7-8**: Severe pain
- **9-10**: Very severe pain

## Mood Score Descriptions

- **1-2**: Very poor
- **3-4**: Poor
- **5-6**: Fair
- **7-8**: Good
- **9-10**: Excellent

## Common Symptoms

The system includes predefined symptoms:
- Fever, Cough, Headache, Fatigue
- Nausea, Dizziness, Chest Pain
- Shortness of Breath, Joint Pain
- Swelling, Rash, Insomnia

## UI Features

- **Responsive Design**: Works on desktop and mobile
- **Status Indicators**: Color-coded badges (Green/Yellow/Red)
- **Summary Statistics**: Visual dashboard with key metrics
- **Form Validation**: Client-side and server-side validation
- **Auto-calculation**: BMI calculation from weight/height
- **Smart Assessment**: Automatic status evaluation
- **Trend Analysis**: Historical data visualization
- **Quick Actions**: Easy access to critical alerts and today's records

## Sample Data

The system includes sample health records with:
- **Normal cases**: Healthy patients with normal vital signs
- **Warning cases**: Patients with slightly elevated values
- **Critical cases**: Patients requiring immediate attention
- **Recovery tracking**: Progress from critical to normal status

## Integration

### With Existing Systems:
- **Patient Management**: Uses existing patient data
- **Appointment System**: Links to patient appointments
- **Medicine Management**: Integrates with medication records
- **Dosage Reminders**: Correlates with medication compliance

### Future Enhancements:
- **Real-time Monitoring**: Live vital sign tracking
- **Mobile App**: Patient self-reporting interface
- **Telemedicine Integration**: Remote health monitoring
- **AI Analysis**: Predictive health analytics
- **Alert Notifications**: Automated staff notifications
- **Report Generation**: Detailed health analytics reports

## Access

Navigate to the Health Monitoring page through the staff sidebar menu. The system is fully integrated with the existing Laravel application structure and provides comprehensive health tracking capabilities for improved patient care. 