# ELTEE DMCC - Enterprise Resource Planning (ERP) System

## Project Overview

ELTEE DMCC is a comprehensive **Enterprise Resource Planning (ERP) system** designed for import-export businesses and trading companies. Originally developed for ELTEE DMCC, this system is a full-featured business management solution that handles the complete lifecycle of international trade operations—from lead generation and customer relationship management to purchase order processing, inventory management, payment tracking, and shipment logistics.

The system centralizes all business operations into a unified platform, enabling companies to manage:
- **Sales & Customer Management** - Track leads, clients, pro-forma invoices, and payments
- **Purchase Management** - Create purchase orders, manage suppliers, track payments and shipments
- **Inventory & Product Management** - Product cataloging, categorization, UOM tracking
- **Financial Operations** - Multi-currency support, payment tracking, bank account management
- **Logistics & Shipping** - Shipment tracking, port management, document handling
- **CRM & Lead Management** - Lead tracking, follow-ups, conversion management
- **User & Role Management** - Multi-branch operations with role-based access control

This is an **all-in-one solution** for businesses engaged in cross-border trade, eliminating the need for multiple disconnected systems.

---

## Business Context

This system serves as a complete **trade management platform** for companies that:
- Import and export goods internationally
- Need to manage multiple currencies and countries
- Handle complex shipping and logistics
- Require detailed financial tracking and payment management
- Operate across multiple branches or locations
- Need CRM capabilities for lead and customer management

---

## Detected Modules / Features

Based on comprehensive code analysis, the system includes the following major modules:

### 1. **Dashboard & Analytics**
- Real-time business metrics (Total Sales, Total Purchases, Receivables, Payables)
- Multi-currency financial tracking
- Sales vs Purchase graphical analysis
- Top users performance tracking
- Product-wise and Country-wise sales analysis
- Monthly trends and revenue forecasting

### 2. **Master Data Management**
- **User Management** - Employee records, roles, designations, access control
- **Branch Management** - Multi-branch operations with branch switching capability
- **Product Master** - Product catalog with categories, UOM, descriptions, packaging types
- **Client/Customer Master** - Complete customer profiles with contact details, addresses
- **Supplier/Vendor Master** - Supplier management with contact and business information
- **Country & Location Master** - Countries, states, cities, areas, ports
- **Bank Master** - Bank accounts, currency-specific account details
- **Terms & Conditions** - Configurable business terms for transactions
- **Shipment Documents** - Document type management

### 3. **Sales Management**
- **Pro-Forma Invoice Generation** - Create detailed commercial invoices
  - Product selection with packaging types and quantities
  - Multi-currency pricing
  - Incoterms support (FOB, CIF, CFR, etc.)
  - Country of origin and supply tracking
  - Port of loading and destination
  - Payment term configuration
  - Terms and conditions selection
  - Shipment document requirements
- **Pro-Forma Approval Workflow** - Request, approve, or reject invoices
- **Customer Payment Tracking** - Record and monitor customer payments
- **Payment Receipt Management** - Generate payment receipts
- **Sales Document Management** - Centralized document repository
- **Deactivated Pro-Forma Management** - Archive and manage cancelled invoices

### 4. **Purchase Management**
- **Purchase Order Creation**
  - Direct PO creation
  - PO from Pro-Forma Invoice
  - Link multiple PIs to single PO
- **Purchase Order Approval Workflow** - Multi-level approval system
- **Purchase Payment Management** - Track payments to suppliers
- **Purchase Receipt & Payment** - Transaction proof upload and approval
- **Refund Management** - Handle purchase refunds
- **Supplier Payment Tracking** - Monitor payment obligations
- **Purchase Order Cancellation** - Cancel with remarks tracking
- **Deactivated Purchase Orders** - Archive management

### 5. **Shipment & Logistics**
- **PO Shipment Management** - Container allocation and tracking
- **Shipment Details Entry** - Vessel, container, ETD/ETA tracking
- **Shipment Document Upload** - Bill of Lading, Packing List, COO, COA
- **Shipment Tracking Report** - Real-time tracking by PO/PI number
- **Port Management** - Loading and destination port configuration

### 6. **Lead & CRM Management**
- **Lead Master** - Capture new leads with complete information
  - Customer details and business information
  - Product interest tracking
  - Follow-up scheduling with date and time
  - Lead status tracking (Hot, Warm, Cold, Raw, Not Interested)
  - Kind attention and requirement details
- **Lead Summary & Reporting** - Track lead pipeline and conversions
- **Lead Fresh Visit** - First-time customer engagement
- **Follow-up Management** - Scheduled follow-up system
- **Lead Status Workflow** - Progress tracking from lead to customer

### 7. **Supplier Management**
- **Supplier Master** - Complete supplier profiles
- **Supplier Summary** - Performance and transaction history
- **Supplier Fresh Visit** - Initial supplier engagement
- **Supplier Follow-ups** - Relationship management

### 8. **Financial Management**
- **Multi-Currency Support** - Handle transactions in multiple currencies
- **Account Master** - Chart of accounts management
- **Payment Mode Configuration** - Cash, LC, TT, etc.
- **Payment Description Master** - Before/After shipment, delivery, etc.
- **Expense Tracking** - Business expense management
- **Financial Year Management** - Year-wise data segregation

### 9. **Reporting & Analytics**
- **Country-wise Client Reports** - Geographic distribution analysis
- **Shipment Tracking Reports** - Logistics monitoring
- **Sales Performance Reports** - Revenue analysis
- **Purchase Reports** - Spending analysis
- **Payment Reports** - Receivables and payables aging

### 10. **User Management & Access Control**
- **Role-Based Access Control (RBAC)** - Define roles and permissions
- **Module & Submodule Access** - Granular permission management
- **Branch Switching** - Multi-location access for authorized users
- **User Activity Tracking** - Login history and user visits
- **Financial Year Selection** - Work within specific fiscal periods

### 11. **Document Management**
- **Pro-Forma Documents** - Sales document repository
- **Purchase Documents** - Purchase-related files
- **Shipment Documents** - Logistics documentation
  - Bill of Lading (BL)
  - Packing List (PKL)
  - Certificate of Origin (COO)
  - Certificate of Analysis (COA)
- **Payment Proofs** - Transaction evidence
- **Email Integration** - Send documents via email

### 12. **Notification System**
- **Real-time Notifications** - Bell icon alerts for pending actions
- **PO Request Notifications** - New purchase order alerts
- **PO Approval Notifications** - Approval/rejection updates
- **PI Request Notifications** - Pro-forma invoice requests
- **PI Approval Notifications** - Invoice approval status
- **Payment Request Notifications** - Payment approval alerts
- **Payment Transaction Notifications** - Transaction proof updates
- **Badge Counters** - Visual count of pending items

### 13. **Printing & Export**
- **Print Pro-Forma Invoice** - Formatted invoice printing
- **Print Purchase Order** - PO document generation
- **Print Payment Receipt** - Payment vouchers
- **Print Shipment Documents** - COO, COA, PKL, Tax Invoice
- **PDF Generation** - Using mPDF library
- **Email Sending** - PHPMailer integration

---

## Technology Stack

### Backend Technologies
- **PHP 7.3+** - Server-side scripting language
- **MySQL/MariaDB** - Relational database management
- **PDO & MySQLi** - Database connectivity
- **Composer** - PHP dependency management
  - **mPDF (v8.2)** - PDF generation library
  - **PHPMailer (v6.10)** - Email sending library

### Frontend Technologies
- **HTML5 & CSS3** - Markup and styling
- **JavaScript (ES6+)** - Client-side interactivity
- **jQuery 3.7.1** - DOM manipulation and AJAX
- **Bootstrap 4.3.1** - Responsive UI framework
- **DataTables** - Advanced table features (sorting, searching, pagination)
- **Select2** - Enhanced select dropdowns
- **SweetAlert2** - Beautiful alert modals
- **Chart.js** - Data visualization
- **Flot Charts** - Time-series graphs
- **Font Awesome** - Icon library
- **Material Design Icons** - Additional iconography

### Development Tools
- **Gulp 4.0.2** - Task automation
  - SASS compilation
  - JavaScript minification
  - CSS autoprefixing
  - Browser synchronization
- **Bower** - Legacy frontend package management
- **npm** - Node package management

### Architecture & Design Patterns
- **MVC-like Structure** - Separation of concerns
  - `/production/` - View layer (UI pages)
  - `/production/operation/` - Controller/Business logic
  - `/configuration.php` - Database configuration
- **AJAX-Driven** - Asynchronous data operations
- **Component-Based** - Reusable UI components
- **Session Management** - User authentication and authorization
- **Input Sanitization** - Security functions (safeString, sanitizeInput)

---

## Project Architecture

### Directory Structure
```
ELTEE_DMCC/
├── index.php                      # Login page
├── configuration.php              # Database connection & config
├── component.php                  # Shared component operations
├── logout.php                     # Session termination
├── ajaxfunction.php              # AJAX utility functions
├── ChangeUser.php                # User switching functionality
│
├── production/                   # Main application modules
│   ├── index.php                 # Dashboard
│   ├── header.php                # Common header with navigation
│   ├── footer.php                # Common footer
│   │
│   ├── *_master.php             # Master data entry forms
│   ├── Pro-Forma-Invoice.php   # Sales invoice generation
│   ├── purchase_orders.php     # Purchase order management
│   ├── Lead-Master.php         # CRM lead management
│   ├── shipment_tracking_report.php  # Logistics tracking
│   │
│   ├── operation/               # Business logic layer
│   │   ├── CrudOperation.php   # Generic CRUD operations
│   │   ├── pro_forma_operation.php  # Sales logic
│   │   ├── purchase_order_operation.php  # Purchase logic
│   │   ├── LeadOperation.php   # CRM logic
│   │   ├── payment_operation.php  # Payment processing
│   │   └── shipment_operation.php  # Logistics operations
│   │
│   ├── print_*.php             # Print/PDF generation pages
│   └── images/                 # Application assets
│
├── sendmail/                    # Email functionality
│   └── mail.php                # Email sending script
│
├── build/                      # Compiled assets
│   ├── css/                    # Production CSS
│   └── js/                     # Production JavaScript
│
├── src/                        # Source files for build
│   ├── js/                     # Source JavaScript
│   └── scss/                   # SASS stylesheets
│
├── vendors/                    # Third-party libraries
│   ├── bootstrap/
│   ├── jquery/
│   ├── datatables/
│   ├── font-awesome/
│   └── [other libraries]
│
├── docs/                       # Documentation files
├── composer.json               # PHP dependencies
├── package.json                # Node dependencies
└── bower.json                  # Bower dependencies
```

### Database Architecture
- **63 Tables** - Comprehensive schema covering all business aspects
- **Key Table Categories:**
  - **User & Access Control** - user_master, role_type_master, assign_module
  - **Master Data** - product_master, client_master, country_master, bank_master
  - **Sales** - pro_forma_head, pro_forma_head_details, pro_forma_receipt_payment
  - **Purchase** - purchase_order, purchase_order_details, purchase_order_receipt_payment
  - **CRM** - client_master, tblfollowupmaster, leadsummery tables
  - **Shipping** - purchase_shipment_head, purchase_shipment_details
  - **Financial** - payment_mode, payment_description, financial_year

### Authentication & Authorization Flow
1. User logs in with credentials and selects financial year
2. Session established with user_id, role, branch, department
3. Dynamic menu generation based on role permissions (all_modules, all_submodule)
4. Module and submodule access controlled via assign_module/assign_submodule
5. Branch switching capability for multi-location users
6. User status verification on each page load

### Data Flow Pattern
1. **User Action** → Frontend form submission
2. **AJAX Request** → jQuery POST to operation PHP file
3. **Server Processing** → Business logic, validation, database operations
4. **Response** → JSON or HTML returned to client
5. **UI Update** → SweetAlert notification + DataTable refresh

---

## Key Benefits

### For Trading & Import-Export Companies
1. **End-to-End Trade Management** - Complete lifecycle from lead to payment
2. **Multi-Currency Operations** - Handle international transactions seamlessly
3. **Compliance Ready** - Incoterms, COO, COA, and other trade documents
4. **Real-Time Tracking** - Monitor shipments, payments, and inventory

### For Business Operations
1. **Centralized Platform** - Eliminate multiple software systems
2. **Role-Based Access** - Secure, controlled data access
3. **Multi-Branch Support** - Scale across multiple locations
4. **Financial Transparency** - Real-time receivables and payables tracking

### For Sales & CRM
1. **Lead Management** - Capture, track, and convert leads systematically
2. **Follow-Up System** - Never miss a customer interaction
3. **Client Profiles** - Complete customer history and requirements
4. **Performance Analytics** - Track sales team performance

### For Procurement
1. **Supplier Management** - Maintain vendor relationships
2. **Purchase Workflows** - Approval-based purchase orders
3. **Payment Tracking** - Monitor supplier payments and refunds
4. **Cost Analysis** - Track purchase expenses by currency

### For Logistics
1. **Shipment Tracking** - Real-time container and vessel tracking
2. **Document Management** - Digital document repository
3. **Port Management** - Loading and destination tracking
4. **ETD/ETA Monitoring** - Estimated vs. actual arrival tracking

---

## Installation & Setup Instructions

### Prerequisites
- **Web Server** - Apache 2.4+ or Nginx
- **PHP** - Version 7.3 or higher
- **Database** - MySQL 5.7+ or MariaDB 10.4+
- **Composer** - For PHP dependency management
- **Node.js & npm** - For frontend build tools (optional for development)

### Step 1: Clone/Extract Project
```bash
# Extract project files to web server directory
cd /path/to/webserver/htdocs
# Extract ELTEE_DMCC.zip or clone from repository
```

### Step 2: Database Setup
```bash
# Import database schema
mysql -u root -p < eltee_dmcc_new1.sql

# Or using phpMyAdmin:
# 1. Create database: eltee_dmcc_new1
# 2. Import SQL file via phpMyAdmin interface
```

### Step 3: Configuration
Edit `configuration.php` and update database credentials:
```php
$dbHost = "localhost";
$dbUsername = "your_db_username";
$dbPassword = "your_db_password";
$dbName = "eltee_dmcc_new1";
$config_url = "http://your-domain.com/ELTEE_DMCC/";
```

### Step 4: Install PHP Dependencies
```bash
composer install
# This installs mPDF and PHPMailer
```

### Step 5: Set Permissions
```bash
# Ensure write permissions for upload directories
chmod -R 755 production/images/
chmod -R 755 production/uploads/
```

### Step 6: Development Build (Optional)
```bash
# Install Node.js dependencies
npm install

# Install Bower dependencies (if needed)
bower install

# Run Gulp tasks for SASS compilation
gulp
```

### Step 7: Access the Application
```
Open browser and navigate to:
http://localhost/ELTEE_DMCC/index.php

Default Login Credentials:
- Check database user_master table for existing users
- Financial Year: Select from dropdown
```

### Step 8: Email Configuration (Optional)
Edit `sendmail/mail.php` to configure SMTP settings:
```php
$mail->Host = 'smtp.yourserver.com';
$mail->Username = 'your-email@domain.com';
$mail->Password = 'your-password';
```

---

## Usage Guide

### Initial Setup Workflow
1. **Login** - Use credentials and select financial year
2. **Master Data** - Configure masters (products, clients, suppliers, banks, countries)
3. **User Management** - Create users and assign roles
4. **Branch Setup** - Configure branches if multi-location

### Sales Workflow
1. **Lead Entry** - Capture new leads in Lead Master
2. **Follow-up** - Schedule and track follow-ups
3. **Lead Conversion** - Convert to customer
4. **Pro-Forma Creation** - Generate invoice with products
5. **Pro-Forma Approval** - Submit for approval (if required)
6. **Customer Payment** - Record payment receipts
7. **Document Generation** - Print/email documents

### Purchase Workflow
1. **Supplier Entry** - Add supplier to Supplier Master
2. **Purchase Order** - Create PO (direct or from Pro-Forma)
3. **PO Approval** - Submit for approval
4. **Purchase Payment** - Record payments to supplier
5. **Shipment Entry** - Add container and vessel details
6. **Shipment Tracking** - Monitor logistics
7. **Document Management** - Upload BL, PKL, COO, COA

### Shipment Tracking
1. Navigate to **Reports → Shipment Tracking**
2. Enter PO or PI number
3. View real-time shipment status
4. Check vessel, container, ETD/ETA
5. View uploaded documents

### Reporting
1. **Dashboard** - Real-time business metrics
2. **Country-wise Reports** - Geographic analysis
3. **Payment Reports** - Receivables and payables
4. **Performance Reports** - Sales and purchase trends

---

## Limitations / Considerations

### Technical Limitations
1. **PHP Version** - Requires PHP 7.3+; may need updates for PHP 8.x
2. **Single Database** - No built-in database replication or clustering
3. **No API** - RESTful API not included; primarily web-based UI
4. **Session-Based Auth** - No JWT or OAuth2 support
5. **File Storage** - Local file system; no cloud storage integration

### Operational Considerations
1. **Manual Backups** - Database backup must be scheduled separately
2. **Email SMTP** - Requires SMTP server configuration
3. **Timezone** - Hardcoded to 'Asia/Kolkata' in configuration.php
4. **Browser Compatibility** - Optimized for modern browsers (Chrome, Firefox, Edge)
5. **Mobile Experience** - Responsive but optimized for desktop use

### Security Considerations
1. **Input Sanitization** - Basic security functions implemented
2. **SQL Injection Protection** - Uses mysqli_real_escape_string and prepared statements
3. **XSS Protection** - Uses htmlentities for output
4. **File Upload Validation** - Implement additional file type checks if needed
5. **Password Storage** - Ensure password hashing is implemented (check user_master table)
6. **HTTPS Recommended** - Use SSL/TLS for production deployment

### Scalability Considerations
1. **Concurrent Users** - Tested for small to medium user base
2. **Data Volume** - Optimize queries for large transaction volumes
3. **File Storage** - Consider CDN or object storage for large file volumes
4. **Caching** - No built-in caching layer; consider Redis/Memcached for high traffic

### Customization Notes
1. **Company Logo** - Replace `production/images/logo.png`
2. **Company Name** - Update in database table `company_master`
3. **Currency Support** - Add currencies in `country_master`
4. **Payment Terms** - Configure in `payment_description` and `payment_mode`
5. **Reports** - Custom reports can be added in `/production/operation/` directory

---

## Conclusion

The **ELTEE DMCC ERP System** represents a mature, full-featured enterprise solution for businesses engaged in international trade and import-export operations. With **24,457 lines of production code** across **63 database tables**, this system demonstrates enterprise-grade development practices and comprehensive business logic.

### Key Strengths
- **Comprehensive Feature Set** - Covers entire trade lifecycle
- **Well-Structured Codebase** - Organized, maintainable architecture
- **Rich UI/UX** - Modern, responsive interface with real-time updates
- **Multi-Currency & Multi-Branch** - True enterprise capabilities
- **Document Management** - Integrated document handling
- **Approval Workflows** - Business process automation

### Ideal Use Cases
1. **Import-Export Companies** - Primary target market
2. **Trading Houses** - Multi-product trading operations
3. **Distribution Companies** - Wholesale and distribution
4. **Manufacturing-Traders** - Combined manufacturing and trading
5. **Freight Forwarders** - Logistics service providers

### Beyond ELTEE DMCC
While built for ELTEE DMCC, this system can be easily adapted for:
- **White-Label Solutions** - Rebrand for other trading companies
- **SaaS Platform** - Multi-tenant version with subscription model
- **Industry Variants** - Adapt for specific commodities (electronics, textiles, food products)
- **Regional Customization** - Adapt for different trade regulations and currencies
- **Integration Platform** - Connect with e-commerce, accounting, or logistics systems

### Value Proposition
This system eliminates the need for multiple disconnected tools by providing an **all-in-one platform** that handles CRM, Sales, Purchase, Inventory, Payments, Logistics, and Reporting. For trading companies, this translates to:
- **Operational Efficiency** - Reduced manual work and errors
- **Cost Savings** - No need for multiple software licenses
- **Better Decision Making** - Real-time analytics and reporting
- **Improved Customer Service** - Centralized customer information
- **Compliance** - Built-in document management for trade compliance

---

## Support & Contact

For implementation support, customization, or licensing inquiries, contact the development team or visit the project repository.

**System Version:** 2.0-beta2
**Last Updated:** August 2025
**License:** MIT (As per package.json)

---

*This documentation was generated through comprehensive code analysis and reflects the actual implementation as of the analysis date.*
