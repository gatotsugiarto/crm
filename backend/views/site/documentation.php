<?php

$baseUrl = Yii::$app->request->baseUrl;
?>
<style>
	html { 
		scroll-behavior: smooth; 
	}

	/* submenu background sangat muda & clean */
	.navbar.bg-warning .dropdown-menu {
	    background-color: #fff3cd;
	    border: 1px solid #ffe69c;
	    border-radius: 6px;
	    padding: 6px 0;
	}

	/* text normal */
	.navbar.bg-warning .dropdown-menu .dropdown-item {
	    color: #495057;
	    font-weight: 500;
	}

	/* hover */
	.navbar.bg-warning .dropdown-menu .dropdown-item:hover {
	    background-color: #ffe69c;
	    color: #212529;
	}

	/* active */
	.navbar.bg-warning .dropdown-menu .dropdown-item:active {
	    background-color: #ffc107;
	    color: #212529;
	}

</style>

<!-- Main Content -->
<div class="container my-0">
  <div class="row">
    
	<!-- Top Navigation -->
	<nav class="navbar navbar-expand-lg navbar-dark bg-warning w-100">
	  <ul class="navbar-nav">

	    <!-- DASHBOARD -->
	    <li class="nav-item dropdown">
	      <a class="nav-link dropdown-toggle text-white font-weight-bold" href="#" id="menuDashboard" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dashboard</a>

	      <div class="dropdown-menu">
	        <a class="dropdown-item" href="#employer-payroll-cost">
	          Employer Payroll Cost
	        </a>
	        <a class="dropdown-item" href="#employee-status">
	          Employee Status
	        </a>
	        <a class="dropdown-item" href="#overtime">
	          Overtime
	        </a>
	        <a class="dropdown-item" href="#take-home-pay">
	          Take Home Pay
	        </a>
	        <a class="dropdown-item" href="#attendance-deduction">
	          Attendance Deduction
	        </a>
	      </div>
	    </li>

	    <!-- USER MANAGEMENT -->
	    <li class="nav-item">
	          <a class="nav-link text-white fw-bold" href="#usermanagement">User Management</a>
	        </li>

	        <li class="nav-item">
	          <a class="nav-link text-white fw-bold" href="#payrollmanagement">Payroll Management</a>
	        </li>

	        <li class="nav-item">
	          <a class="nav-link text-white fw-bold" href="#masterdata">Master Data</a>
	        </li>

	        <li class="nav-item">
	          <a class="nav-link text-white fw-bold" href="#logactivity">Log Activity</a>
	       </li>

	  </ul>
	</nav>



	<!-- Documentation Content -->
	<section class="col-md-12">
	  <h2 id="dashboard"><strong>Payroll Dashboard Documentation</strong></h2>
	  <p class="text-muted">
	    This document provides a detailed overview of all payroll dashboard components, including their purpose, data sources, visualization, interpretation guidelines, and technical references.
	  </p>
	  <img src="<?=$baseUrl?>/img/documentation/Satu-Payroll-Dashboard.png" alt="Employer Payroll Cost Chart" class="img-fluid mb-3">

	  <!-- Employer Payroll Cost -->
	  <h3 id="employer-payroll-cost"><strong>1. Employer Payroll Cost</strong></h3>
	  <h5><strong>Description</strong> The Employer Payroll Cost chart displays the total payroll expenses incurred by the employer for each payroll period. This includes all compensation and employer-paid contributions associated with employee salaries.</h5>
	  <img src="<?=$baseUrl?>/img/documentation/Satu-Payroll-Employer-Payroll-Cost.png" alt="Employer Payroll Cost Chart" class="img-fluid mb-3" width="50%">
	  <ul>
	    <li><strong>Purpose:</strong> Displays the total payroll expenses borne by the company each month.</li>
	    <li><strong>Data Source:</strong> Aggregated from base salary, allowances, deductions, and employer contributions (BPJS, tax, etc.).</li>
	  </ul>

	  <!-- Employee Status -->
	  <h3 id="employee-status"><strong>2. Employee Status</strong></h3>
	  <h5><strong>Description</strong> The Employee Status chart displays the distribution of employees based on their employment type for each payroll period. It provides visibility into workforce composition by showing how many employees are in Probation, PKWT (Fixed-Term Contract), and Permanent status.</h5>
	  <img src="<?=$baseUrl?>/img/documentation/Satu-Payroll-Employee-Status.png" alt="Employer Payroll Cost Chart" class="img-fluid mb-3" width="50%">
	  <ul>
	    <li><strong>Purpose:</strong> Shows the distribution of employees by employment status.</li>
	    <li><strong>Categories:</strong> Probation, PKWT (fixed-term contract), Permanent.</li>
	  </ul>

	  <!-- Overtime -->
	  <h3 id="overtime"><strong>3. Overtime</strong></h3>
	  <h5><strong>Description</strong> The Overtime chart displays the total employee overtime recorded during each payroll period. It provides insight into overtime trends and helps evaluate additional labor costs incurred beyond regular working hours.</h5>
	  
	  <img src="<?=$baseUrl?>/img/documentation/Satu-Payroll-Overtime.png" alt="Employer Payroll Cost Chart" class="img-fluid mb-3" width="50%">
	  <ul>
	    <li><strong>Purpose:</strong> Displays overtime hours and compensation.</li>
	    <li><strong>Data Source:</strong> Overtime timesheets combined with payroll overtime formula.</li>
	  </ul>

	  <!-- Take Home Pay -->
	  <h3 id="take-home-pay"><strong>4. Take Home Pay</strong></h3>
	  <h5><strong>Description</strong> The Take Home Pay chart shows the total net salary paid to employees after all earnings and deductions have been applied.</h5>
	  <img src="<?=$baseUrl?>/img/documentation/Satu-Payroll-THP.png" alt="Employer Payroll Cost Chart" class="img-fluid mb-3" width="50%">
	  <ul>
	    <li><strong>Purpose:</strong> Displays the net salary employees receive after deductions.</li>
	    <li><strong>Data Source:</strong> Final payroll (base salary + allowances – tax/BPJS deductions).</li>
	  </ul>

	  <!-- Attendance Deduction -->
	  <h3 id="attendance-deduction"><strong>5. Attendance Deduction</strong></h3>
	  <h5><strong>Description</strong> The Attendance Deduction chart displays total salary deductions caused by attendance violations, such as late arrivals or other attendance-related penalties.</h5>
	  
	  <img src="<?=$baseUrl?>/img/documentation/Satu-Payroll-Attendance-Deduction.png" alt="Employer Payroll Cost Chart" class="img-fluid mb-3" width="50%">
	  <ul>
	    <li><strong>Purpose:</strong> Displays deductions applied due to late employee attendance.</li>
	    <li><strong>Data Source:</strong> Attendance logs combined with payroll deduction rules.</li>
	  </ul>
	</section>


	<!-- User Management Documentation -->
	<section class="col-md-9">
	  <h2 id="usermanagement">User Management</h2>
	  <p class="text-muted">
	    This section documents the User Management dashboard, which provides administrators with tools to manage users, roles, and permissions within the system.
	  </p>
	  <img src="<?=$baseUrl?>/img/documentation/user-management.png" alt="Employer Payroll Cost Chart" class="img-fluid mb-3">

	  <!-- Users Access -->
	  <h3 id="users-access">1. Users Access</h3>
	  <img src="<?=$baseUrl?>/img/documentation/user-management.png" alt="Employer Payroll Cost Chart" class="img-fluid mb-3">
	  <ul>
	    <li><strong>Purpose:</strong> Displays the list of backend users with system access.</li>
	    <li><strong>Data Source:</strong> User accounts stored in the backend authentication database.</li>
	    <li><strong>Interpretation:</strong> Allows administrators to review and manage system-level users.</li>
	    <li><strong>Technical Notes:</strong> Ensure proper role assignment and password policies are enforced.</li>
	  </ul>

	  <!-- Assignments -->
	  <h3 id="assignments">2. Assignments</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Provides navigation to assign users to members or groups.</li>
	    <li><strong>Data Source:</strong> User-to-member mapping tables.</li>
	    <li><strong>Interpretation:</strong> Facilitates linking backend accounts to frontend members.</li>
	    <li><strong>Technical Notes:</strong> Validate assignment consistency to avoid orphaned accounts.</li>
	  </ul>

	  <!-- Role -->
	  <h3 id="role">3. Role</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Defines collections of permissions grouped into roles.</li>
	    <li><strong>Data Source:</strong> Role definitions stored in the authorization schema.</li>
	    <li><strong>Interpretation:</strong> Roles simplify permission management by grouping actions.</li>
	    <li><strong>Technical Notes:</strong> Use role-based access control (RBAC) for scalability.</li>
	  </ul>

	  <!-- Permission -->
	  <h3 id="permission">4. Permission</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Specifies individual actions a user is allowed to perform.</li>
	    <li><strong>Data Source:</strong> Permission records linked to roles and users.</li>
	    <li><strong>Interpretation:</strong> Fine-grained control over system functionality.</li>
	    <li><strong>Technical Notes:</strong> Permissions should be audited regularly for compliance.</li>
	  </ul>

	  <!-- Members Access -->
	  <h3 id="members-access">5. Members Access</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Displays the list of frontend/client users.</li>
	    <li><strong>Data Source:</strong> Member accounts stored in the client-facing database.</li>
	    <li><strong>Interpretation:</strong> Enables monitoring and management of client-side access.</li>
	    <li><strong>Technical Notes:</strong> Ensure synchronization between backend and frontend accounts.</li>
	  </ul>

	  <!-- Change Password -->
	  <h3 id="change-password">6. Change Password</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Allows users to update their account password securely.</li>
	    <li><strong>Data Source:</strong> Authentication system with password hashing.</li>
	    <li><strong>Interpretation:</strong> Provides a self-service option for account security.</li>
	    <li><strong>Technical Notes:</strong> Enforce strong password policies and hashing algorithms (e.g., bcrypt).</li>
	  </ul>
	</section>

	<!-- Payroll Management Documentation -->
	<section class="col-md-9">
	  <h2 id="payrollmanagement">Payroll Management</h2>
	  <p class="text-muted">
	    This section documents the Payroll Management dashboard, which provides HR and finance teams with tools to manage employee compensation, compliance, and reporting.
	  </p>

	  <!-- Upload Data -->
	  <h3 id="upload-data">1. Upload Data</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Upload payroll data integrated from HRIS systems.</li>
	    <li><strong>Data Source:</strong> HRIS export files (CSV/XML).</li>
	    <li><strong>Interpretation:</strong> Ensures payroll records are synchronized with HR data.</li>
	    <li><strong>Technical Notes:</strong> Validate file structure before upload to avoid errors.</li>
	  </ul>

	  <!-- Join & Resignation -->
	  <h3 id="join-resignation">2. Join &amp; Resignation</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Overview of employees joining or resigning during the payroll period.</li>
	    <li><strong>Data Source:</strong> Employee lifecycle records.</li>
	    <li><strong>Interpretation:</strong> Provides visibility into workforce changes impacting payroll.</li>
	    <li><strong>Technical Notes:</strong> Ensure resignation dates align with payroll cut-off.</li>
	  </ul>

	  <!-- Employee Salary -->
	  <h3 id="employee-salary">3. Employee Salary</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Configure and oversee salary structures and compensation records.</li>
	    <li><strong>Data Source:</strong> Salary tables and compensation policies.</li>
	    <li><strong>Interpretation:</strong> Centralized management of salary components.</li>
	    <li><strong>Technical Notes:</strong> Use <code>DECIMAL(15,2)</code> for monetary fields.</li>
	  </ul>

	  <!-- Employee Management -->
	  <h3 id="employee-management">4. Employee Management</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Manage and monitor employee data within the system.</li>
	    <li><strong>Data Source:</strong> Employee master records.</li>
	    <li><strong>Interpretation:</strong> Ensures accurate employee information for payroll processing.</li>
	    <li><strong>Technical Notes:</strong> Regularly audit employee records for completeness.</li>
	  </ul>

	  <!-- Employee Profile -->
	  <h3 id="employee-profile">5. Employee Profile</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Displays employee information and payroll details.</li>
	    <li><strong>Data Source:</strong> Integrated HR and payroll database.</li>
	    <li><strong>Interpretation:</strong> Provides a single view of employee compensation history.</li>
	    <li><strong>Technical Notes:</strong> Ensure secure access controls for sensitive data.</li>
	  </ul>

	  <!-- Payroll -->
	  <h3 id="payroll">6. Payroll</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Manages employee salary and payroll processing.</li>
	    <li><strong>Data Source:</strong> Payroll transaction records.</li>
	    <li><strong>Interpretation:</strong> Executes monthly payroll runs and generates payslips.</li>
	    <li><strong>Technical Notes:</strong> Align payroll cycle with company financial calendar.</li>
	  </ul>

	  <!-- L3 Summary -->
	  <h3 id="l3-summary">7. L3 Summary</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Provides a consolidated payroll overview.</li>
	    <li><strong>Data Source:</strong> Aggregated payroll reports.</li>
	    <li><strong>Interpretation:</strong> High-level summary for management review.</li>
	    <li><strong>Technical Notes:</strong> Ensure report accuracy before distribution.</li>
	  </ul>

	  <!-- Bukti Potong PPh21 -->
	  <h3 id="bukti-potong">8. Bukti Potong PPh21</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Generates official tax withholding forms (1721-A1/A2).</li>
	    <li><strong>Data Source:</strong> Payroll tax calculations.</li>
	    <li><strong>Interpretation:</strong> Provides compliance documentation for employees.</li>
	    <li><strong>Technical Notes:</strong> Ensure alignment with DJP e-Bupot standards.</li>
	  </ul>

	  <!-- Formulir 1721-A1 -->
	  <h3 id="formulir-1721-a1">9. Formulir 1721-A1</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Official tax withholding form for permanent employees.</li>
	    <li><strong>Data Source:</strong> Payroll tax records.</li>
	    <li><strong>Interpretation:</strong> Used for employee annual tax reporting.</li>
	    <li><strong>Technical Notes:</strong> Validate NPWP/NIK fields before submission.</li>
	  </ul>

	  <!-- BPJS Records -->
	  <h3 id="bpjs-records">10. BPJS Records</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Displays employee BPJS records and contribution details.</li>
	    <li><strong>Data Source:</strong> BPJS contribution tables.</li>
	    <li><strong>Interpretation:</strong> Ensures compliance with social security regulations.</li>
	    <li><strong>Technical Notes:</strong> Synchronize with BPJS online system for accuracy.</li>
	  </ul>

	  <!-- THR -->
	  <h3 id="thr">11. THR</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Summarizes employee THR (holiday allowance) contributions.</li>
	    <li><strong>Data Source:</strong> Payroll THR records.</li>
	    <li><strong>Interpretation:</strong> Provides visibility into annual THR disbursement.</li>
	    <li><strong>Technical Notes:</strong> Ensure THR calculation follows labor regulations.</li>
	  </ul>
	</section>

	<!-- Master Data Documentation -->
	<section class="col-md-9">
	  <h2 id="masterdata">Master Data</h2>
	  <p class="text-muted">
	    This section documents the Master Data dashboard, which provides foundational configurations for payroll structures, employee statuses, and company information.
	  </p>

	  <!-- Payroll Profiles -->
	  <h3 id="payroll-profiles">1. Payroll Profiles</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Defines payroll structures and employee salary configurations.</li>
	    <li><strong>Data Source:</strong> Payroll profile tables linked to employee records.</li>
	    <li><strong>Interpretation:</strong> Ensures consistent salary structures across the organization.</li>
	    <li><strong>Technical Notes:</strong> Profiles should be version-controlled to track changes over time.</li>
	  </ul>

	  <!-- Payroll Components -->
	  <h3 id="payroll-components">2. Payroll Components</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Configures salary components used in payroll calculations (e.g., base salary, allowances, deductions).</li>
	    <li><strong>Data Source:</strong> Component definitions stored in payroll schema.</li>
	    <li><strong>Interpretation:</strong> Provides modular building blocks for payroll formulas.</li>
	    <li><strong>Technical Notes:</strong> Use standardized naming conventions for components to avoid duplication.</li>
	  </ul>

	  <!-- Payroll Categories -->
	  <h3 id="payroll-categories">3. Payroll Categories</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Organizes payroll components into structured categories.</li>
	    <li><strong>Data Source:</strong> Category mappings in payroll configuration tables.</li>
	    <li><strong>Interpretation:</strong> Simplifies reporting and analysis by grouping related components.</li>
	    <li><strong>Technical Notes:</strong> Categories should align with financial reporting standards.</li>
	  </ul>

	  <!-- Pending Status -->
	  <h3 id="pending-status">4. Pending Status</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Displays employees with pending employment status.</li>
	    <li><strong>Data Source:</strong> Employee master records flagged as pending.</li>
	    <li><strong>Interpretation:</strong> Provides visibility into employees awaiting confirmation or approval.</li>
	    <li><strong>Technical Notes:</strong> Pending records should be reviewed regularly to maintain data integrity.</li>
	  </ul>

	  <!-- Master Company -->
	  <h3 id="master-company">5. Company</h3>
	  <ul>
	    <li>
	    	<strong>Overview:</strong> 
	    	The Company Master Data module is used to manage company information registered in the payroll system.
	    	<br /><br />
	    	This module stores essential company details required for payroll processing, tax reporting (Form 1721-A1), and system configuration.
	    	<br /><br />
	    	Users can:
	    	<br />
	    	<ul>
					<li>Create a new company</li>
					<li>View company details</li>
					<li>Update company information</li>
					<li>Delete a company</li>
					<li>Activate or deactivate a company</li>
				</ul>
	    </li>
	    <br />
	    <li><strong>Company List Page:</strong> The Companies page displays all registered companies in the system.</li>
	    	<ul>
					<li>
						I. Company Table Columns
						<ul>
							<li>No – Sequential number</li>
							<li>Code – Unique company code</li>
							<li>Company – Company name</li>
							<li>Status – Active / Inactive</li>
							<li>Action – Edit and Delete buttons</li>
						</ul>
					</li>
					<br />
					<li>
						II. Search & Filter
						<ul>
							<li>Search by company code</li>
							<li>Search by company name</li>
							<li>Filter by status (All Status / Active / Inactive)</li>
						</ul>
					</li>
					<br />
					<li>
						III. Export Data: The Export button allows users to export company data into a file format (e.g., Excel/CSV).
					</li>
					<br />
					<li>
						IV. Add New Company: Click + New Company to create a new company record.
					</li>
				</ul>

	    <br />
	    <li><strong>Company Detail Page:</strong> The Company Detail page displays complete company information.</li>
	    
	    <li><strong>Technical Notes:</strong> Ensure compliance with government regulations when updating company data.</li>
	  </ul>
	</section>

	<!-- Log Activity Documentation -->
	<section class="col-md-9">
	  <h2 id="logactivity">Log Activity</h2>
	  <p class="text-muted">
	    This section documents the Log Activity dashboard, which provides an audit trail of all actions performed within the payroll system. It is essential for monitoring, compliance, and troubleshooting.
	  </p>

	  <!-- Overview -->
	  <h3 id="log-overview">1. Overview</h3>
	  <ul>
	    <li><strong>Purpose:</strong> Tracks system actions such as create, update, and delete operations.</li>
	    <li><strong>Data Source:</strong> Application logs generated by the payroll system.</li>
	    <li><strong>Interpretation:</strong> Provides visibility into user and system activity for auditing purposes.</li>
	    <li><strong>Technical Notes:</strong> Logs should be timestamped and immutable to ensure integrity.</li>
	  </ul>

	  <!-- Log Fields -->
	  <h3 id="log-fields">2. Log Fields</h3>
	  <ul>
	    <li><strong>No:</strong> Sequential identifier for each log entry.</li>
	    <li><strong>Action:</strong> Type of operation performed (create, update, delete).</li>
	    <li><strong>Model:</strong> The data model affected (e.g., EmployeePending, PayrollItem, Salary).</li>
	    <li><strong>Action By:</strong> The user or system component that performed the action.</li>
	    <li><strong>Action Date:</strong> Timestamp of when the action occurred.</li>
	    <li><strong>Record ID:</strong> Identifier of the record impacted by the action.</li>
	  </ul>

	  <!-- Interpretation -->
	  <h3 id="log-interpretation">3. Interpretation</h3>
	  <ul>
	    <li>Frequent <strong>create</strong> actions indicate new records being added (e.g., new employees).</li>
	    <li><strong>Update</strong> actions reflect modifications to existing payroll or employee data.</li>
	    <li><strong>Delete</strong> actions highlight records removed, which should be reviewed for compliance.</li>
	    <li>Consistent logging by <em>Application v1</em> shows automated system processes are active.</li>
	  </ul>

	  <!-- Technical Notes -->
	  <h3 id="log-technical">4. Technical Notes</h3>
	  <ul>
	    <li>Ensure logs are stored securely and backed up regularly.</li>
	    <li>Implement role-based access control (RBAC) to restrict log visibility to authorized users.</li>
	    <li>Use indexing for efficient retrieval of log records during audits.</li>
	    <li>Consider integrating with external monitoring tools for real-time alerts.</li>
	  </ul>
	</section>
	  
  </div>
</div>
