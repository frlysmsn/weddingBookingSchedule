  

# St. Rita Mission Station - Church Wedding Hall Schedule System

  

## System Overview

The *St. Rita Mission Station - Church Wedding Hall Schedule System* is a web-based platform designed to streamline the scheduling and management of church weddings.
 
  

### **Objective**:

- Simplify the booking process for clients.

- Provide a centralized system for administrators to manage schedules and validate wedding requirements.

  

### **Core Features**:

- Clients can register, log in, and submit detailed wedding forms.

- Calendar integration ensures conflict-free wedding scheduling.

- Admin can approve bookings, verify uploaded documents, and manage wedding schedules.

  

### **Workflow**:

1. **Step 1**: Client submits the wedding application, selects a date, and uploads required documents.

2. **Step 2**: Admin reviews the application, checks date availability, and validates documents.

3. **Step 3**: Approval status is updated, and the client receives a confirmation email along with a waiver.

  

### **Technology Stack**:  

- **Frontend**: JavaScript (FullCalendar.js, Parsley.js, Dropzone.js, SweetAlert2, jQuery AJAX).

- **Backend**: PHP and MySQL for processing and storing data.

  

### **Expected Outcomes**:

- A user-friendly platform for clients to book wedding schedules efficiently.

- A reliable backend for the admin to manage bookings, calendars, and document verification.

  

## Key Components

  

### **Frontend Components**

1. **Calendar Component**: FullCalendar.js for selecting and displaying unavailable wedding dates.

2. **Form Validation**: Parsley.js to ensure required fields and file uploads are validated on the client side.

3. **File Upload Component**: Dropzone.js for user-friendly drag-and-drop document uploads.

4. **Notifications/Alerts**: SweetAlert2 for displaying success, error, and confirmation messages.

5. **Dynamic Data Loading**: jQuery AJAX to load content dynamically without reloading the page.

  

### **Backend Components**

1. **Programming Language**: PHP

2. **Database**: MySQL (for managing user details, bookings, and uploaded files)

3. **Authentication**: PHP sessions or JSON Web Tokens (JWT) for secure user and admin login.

4. **File Storage**: Local storage on the server (organized by user IDs) or cloud storage (e.g., AWS S3 or Google Drive API).

  

### **System Components Overview**:

1. **Landing Page**: Overview of services, login, and registration buttons for clients and admin.

2. **Client Dashboard**: Form submission for bride/groom details, file uploads, calendar for selecting wedding dates, and booking status updates.

3. **Admin Dashboard**: Admin controls for reviewing and managing bookings, verifying documents, and updating status.

4. **Calendar Management**: Dynamic calendar with availability and non-availability indicators.

5. **File Management**: System for uploading, validating, and organizing wedding-related documents.

6. **Notifications**: Email notifications to clients when their booking is approved.

  

### **Email Notification**:

After the admin approves the booking, the system sends an email notification to the client, confirming the booking and providing the waiver link.

  

---

  

## Frameworks and Libraries

1. **Frontend**:

   - FullCalendar.js (for date selection)

   - Parsley.js (for form validation)

   - Dropzone.js (for file uploads)

   - SweetAlert2 (for notifications)

   - jQuery AJAX (for dynamic content loading)

  

2. **Backend**:

   - PHP (for backend logic)

   - MySQL (for database management)

  

3. **Email Notification**:

   - PHPMailer (for sending email notifications)

  



## File and File Structure

  

### **Directory Structure**:

```

/st-rita-wedding-system

│

├── /assets

│   ├── /css

│   ├── /images

│   └── /js

│

├── /includes

│   ├── config.php

│   ├── db_connection.php

│   └── email.php

│

├── /views

│   ├── landing_page.php

│   ├── client_dashboard.php

│   ├── admin_dashboard.php

│   └── booking_form.php

│

├── /uploads

│   ├── /documents

│   └── /waivers

│

└── index.php

```

  

- **/assets**: Contains static assets like CSS, JavaScript, and images.

- **/includes**: Includes configuration and helper files like database connection and email logic.

- **/views**: Contains the PHP files for different views (landing page, dashboards, forms).

- **/uploads**: Stores uploaded documents and waivers.

- **index.php**: Main entry point for the application.

  

---

  

## Conclusion

This system is designed to manage wedding hall bookings effectively while offering a seamless user experience for both clients and administrators. With dynamic calendar features, easy document uploads, and email notifications, this platform ensures an efficient booking process for church weddings.





Fill-out Forms
	- Forms
		Inside the forms:
			- Name of the Bride ***Required**
					- Date of Birth
					- Birthplace
					- Mother's maiden name
					- Father's name
					Checkboxes **Required**
						- Already Interviewed for PRE-NUPTIAL or CANONICAL INTERVIEWER (Yes or No)
						- Has Seminar for PRE-CANA (Yes or No)
			- Name of the Groom
					- Date of Birth
					- Birthplace
					- Mother's maiden name
					- Father's name
					Checkboxes **Required**
						- Already Interviewed for PRE-NUPTIAL or CANONICAL INTERVER (Yes or No)
						- Has Seminar for PRE-CANA (Yes or No) 
				Below of the 2 forms there will be a calendar which the user can pick the date of their desired wedding date. If there is already taken dates by other user the date of calendar has description that indicates (Already Reserved)
	Buttons: Confirm & Cancel
