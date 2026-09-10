<?php

$baseUrl = Yii::$app->request->baseUrl;
?>
<style>
	html {
		scroll-behavior: smooth;
	}
</style>

<!-- Main Content -->
<div class="container my-0">
  <div class="row">

	<!-- Top Navigation -->
	<nav class="navbar navbar-expand-lg navbar-dark bg-warning w-100">
	  <ul class="navbar-nav">

	    <!-- DASHBOARD -->
	    <li class="nav-item">
	          <a class="nav-link text-white fw-bold" href="javascript:void(0)">Dashboard</a>
	        </li>

	    <!-- MODULES -->
	    <li class="nav-item">
	          <a class="nav-link text-white fw-bold" href="#modules">Modules</a>
	        </li>

	    <!-- USER FLOW -->
	    <li class="nav-item">
	          <a class="nav-link text-white fw-bold" href="#userflow">User Flow</a>
	        </li>

	    <!-- PRODUCT & PRICING -->
	    <li class="nav-item">
	          <a class="nav-link text-white fw-bold" href="#productpricing">Product &amp; Pricing</a>
	        </li>

	    <!-- BUSINESS SCENARIOS -->
	    <li class="nav-item">
	          <a class="nav-link text-white fw-bold" href="#businessscenarios">Business Scenarios</a>
	        </li>

	  </ul>
	</nav>



	<!-- Modules Documentation -->
	<section class="col-md-9">
	  <h2 id="modules">Modules</h2>
	  <p class="text-muted">
	    This section lists the modules that make up the application and what each one is responsible for.
	  </p>

	  <!-- Auth -->
	  <h3 id="module-auth">1. Auth</h3>
	  <p>Handles authentication and authorization: backend users, roles &amp; permissions (RBAC), and members.</p>
	  <ul>
	    <li><strong>Users Access:</strong> Manage backend users with system access.</li>
	    <li><strong>Assignments:</strong> Assign backend accounts to members/groups.</li>
	    <li><strong>Role:</strong> Define collections of permissions grouped into roles.</li>
	    <li><strong>Permission:</strong> Specify individual actions a user/role is allowed to perform.</li>
	    <li><strong>Members Access:</strong> Manage frontend/client member accounts.</li>
	    <li><strong>Change Password:</strong> Self-service password update for users and members.</li>
	  </ul>

	  <!-- Master -->
	  <h3 id="module-master">2. Master</h3>
	  <p>Shared reference/master data used across the other modules.</p>
	  <ul>
	    <li><strong>Company:</strong> Registered companies used throughout the system.</li>
	    <li><strong>Team:</strong> Sales team assignment used by Accounts, Leads, and Opportunities.</li>
	    <li><strong>Application Setting:</strong> Application-wide configuration parameters.</li>
	    <li><strong>Country / Province / City / Postal Code:</strong> Address reference data used by Sales module records.</li>
	  </ul>

	  <!-- Product & Pricing -->
	  <h3 id="module-productprice">3. Product &amp; Pricing</h3>
	  <p>Manages the product catalog and its pricing.</p>
	  <ul>
	    <li><strong>Product / Product Category / Product UOM:</strong> Product catalog and classification.</li>
	    <li><strong>Price List / Product Price / Product Discount:</strong> Pricing rules applied to products.</li>
	    <li><strong>Product Bundle Item:</strong> Bundled product configurations.</li>
	  </ul>

	  <!-- Sales -->
	  <h3 id="module-sales">4. Sales (CRM)</h3>
	  <p>The core CRM pipeline, from lead capture through to invoicing.</p>
	  <ul>
	    <li><strong>Lead:</strong> Prospective customers not yet qualified as accounts.</li>
	    <li><strong>Account / Contact / Account Address:</strong> Customer/company records and their contacts and addresses.</li>
	    <li><strong>Opportunity / Opportunity Product / Opportunity Stage History:</strong> Sales pipeline and deal tracking.</li>
	    <li><strong>Quotation / Quotation Item:</strong> Price quotes sent to customers.</li>
	    <li><strong>Sales Order / Sales Order Item:</strong> Confirmed customer orders.</li>
	    <li><strong>Invoice / Invoice Item:</strong> Billing documents issued to customers.</li>
	    <li><strong>Activity:</strong> Logged interactions (calls, meetings, notes) tied to sales records.</li>
	  </ul>

	  <!-- Log Data -->
	  <h3 id="module-logdata">5. Log Data</h3>
	  <p>Application-wide audit log. Records create, update, and delete activity performed across every other module for monitoring, compliance, and troubleshooting.</p>
	</section>

	<!-- User Flow Documentation -->
	<section class="col-md-9">
	  <h2 id="userflow">User Flow</h2>
	  <p class="text-muted">
	    This section describes the typical end-to-end flow a user follows when using the application.
	  </p>

	  <!-- Overall flow diagram -->
	  <div style="overflow-x:auto; margin: 8px 0 24px;">
	    <svg viewBox="0 0 1000 150" width="100%" style="min-width:760px; max-width:1000px; font-family: inherit;">
	      <defs>
	        <marker id="uf-arrow" markerWidth="10" markerHeight="10" refX="8" refY="3" orient="auto" markerUnits="strokeWidth">
	          <path d="M0,0 L8,3 L0,6 Z" fill="#f0ad4e"/>
	        </marker>
	      </defs>

	      <!-- connectors -->
	      <line x1="176" y1="60" x2="190" y2="60" stroke="#f0ad4e" stroke-width="2" marker-end="url(#uf-arrow)"/>
	      <line x1="356" y1="60" x2="370" y2="60" stroke="#f0ad4e" stroke-width="2" marker-end="url(#uf-arrow)"/>
	      <line x1="536" y1="60" x2="550" y2="60" stroke="#f0ad4e" stroke-width="2" marker-end="url(#uf-arrow)"/>
	      <line x1="716" y1="60" x2="730" y2="60" stroke="#f0ad4e" stroke-width="2" marker-end="url(#uf-arrow)"/>

	      <!-- 1. Login -->
	      <rect x="16" y="20" width="160" height="80" rx="10" fill="#fff8e6" stroke="#f0ad4e" stroke-width="1.5"/>
	      <text x="96" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#5a4300">1. Login</text>
	      <text x="96" y="75" text-anchor="middle" font-size="10" fill="#7a6120">Auth module</text>

	      <!-- 2. Setup Master Data -->
	      <rect x="196" y="20" width="160" height="80" rx="10" fill="#fff8e6" stroke="#f0ad4e" stroke-width="1.5"/>
	      <text x="276" y="48" text-anchor="middle" font-size="13" font-weight="700" fill="#5a4300">2. Setup</text>
	      <text x="276" y="64" text-anchor="middle" font-size="13" font-weight="700" fill="#5a4300">Master Data</text>
	      <text x="276" y="84" text-anchor="middle" font-size="10" fill="#7a6120">Master module</text>

	      <!-- 3. Setup Product & Pricing -->
	      <rect x="376" y="20" width="160" height="80" rx="10" fill="#fff8e6" stroke="#f0ad4e" stroke-width="1.5"/>
	      <text x="456" y="48" text-anchor="middle" font-size="13" font-weight="700" fill="#5a4300">3. Setup Product</text>
	      <text x="456" y="64" text-anchor="middle" font-size="13" font-weight="700" fill="#5a4300">&amp; Pricing</text>
	      <text x="456" y="84" text-anchor="middle" font-size="10" fill="#7a6120">Product &amp; Pricing module</text>

	      <!-- 4. Sales Process -->
	      <rect x="556" y="20" width="160" height="80" rx="10" fill="#fdece0" stroke="#e8730f" stroke-width="1.5"/>
	      <text x="636" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#7a3400">4. Sales Process</text>
	      <text x="636" y="75" text-anchor="middle" font-size="10" fill="#8a4c1c">Sales module</text>

	      <!-- 5. Audit Trail -->
	      <rect x="736" y="20" width="160" height="80" rx="10" fill="#fff8e6" stroke="#f0ad4e" stroke-width="1.5"/>
	      <text x="816" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#5a4300">5. Audit Trail</text>
	      <text x="816" y="75" text-anchor="middle" font-size="10" fill="#7a6120">Log Data module</text>
	    </svg>
	  </div>

	  <!-- Login -->
	  <h3 id="flow-login">1. Login</h3>
	  <p>User signs in with a backend account. Access to each menu depends on the roles/permissions assigned via the <strong>Auth</strong> module.</p>

	  <!-- Setup Master Data -->
	  <h3 id="flow-setup-master">2. Setup Master Data</h3>
	  <p>Before running the business process, reference data is prepared in the <strong>Master</strong> module: Company, Team, Application Setting, and address data (Country, Province, City, Postal Code).</p>

	  <!-- Setup Product & Pricing -->
	  <h3 id="flow-setup-product">3. Setup Product &amp; Pricing</h3>
	  <p>Products to be sold are registered in the <strong>Product &amp; Pricing</strong> module, along with their categories, unit of measure, and price lists/discounts.</p>

	  <!-- Sales Process -->
	  <h3 id="flow-sales-process">4. Sales Process</h3>
	  <p>The core CRM pipeline in the <strong>Sales</strong> module, followed in order:</p>

	  <div style="overflow-x:auto; margin: 8px 0 24px;">
	    <svg viewBox="0 0 990 180" width="100%" style="min-width:760px; max-width:990px; font-family: inherit;">
	      <defs>
	        <marker id="sf-arrow" markerWidth="10" markerHeight="10" refX="8" refY="3" orient="auto" markerUnits="strokeWidth">
	          <path d="M0,0 L8,3 L0,6 Z" fill="#e8730f"/>
	        </marker>
	      </defs>

	      <!-- connectors -->
	      <line x1="146" y1="55" x2="170" y2="55" stroke="#e8730f" stroke-width="2" marker-end="url(#sf-arrow)"/>
	      <line x1="316" y1="55" x2="340" y2="55" stroke="#e8730f" stroke-width="2" marker-end="url(#sf-arrow)"/>
	      <line x1="486" y1="55" x2="510" y2="55" stroke="#e8730f" stroke-width="2" marker-end="url(#sf-arrow)"/>
	      <line x1="656" y1="55" x2="680" y2="55" stroke="#e8730f" stroke-width="2" marker-end="url(#sf-arrow)"/>
	      <line x1="826" y1="55" x2="850" y2="55" stroke="#e8730f" stroke-width="2" marker-end="url(#sf-arrow)"/>

	      <!-- 1. Lead -->
	      <rect x="6" y="15" width="140" height="80" rx="10" fill="#fdece0" stroke="#e8730f" stroke-width="1.5"/>
	      <text x="76" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#7a3400">Lead</text>
	      <text x="76" y="75" text-anchor="middle" font-size="10" fill="#8a4c1c">Prospect captured</text>

	      <!-- 2. Account/Contact -->
	      <rect x="176" y="15" width="140" height="80" rx="10" fill="#fdece0" stroke="#e8730f" stroke-width="1.5"/>
	      <text x="246" y="48" text-anchor="middle" font-size="13" font-weight="700" fill="#7a3400">Account /</text>
	      <text x="246" y="64" text-anchor="middle" font-size="13" font-weight="700" fill="#7a3400">Contact</text>
	      <text x="246" y="82" text-anchor="middle" font-size="10" fill="#8a4c1c">Qualified customer</text>

	      <!-- 3. Opportunity -->
	      <rect x="346" y="15" width="140" height="80" rx="10" fill="#fdece0" stroke="#e8730f" stroke-width="1.5"/>
	      <text x="416" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#7a3400">Opportunity</text>
	      <text x="416" y="75" text-anchor="middle" font-size="10" fill="#8a4c1c">Deal in pipeline</text>

	      <!-- 4. Quotation -->
	      <rect x="516" y="15" width="140" height="80" rx="10" fill="#fdece0" stroke="#e8730f" stroke-width="1.5"/>
	      <text x="586" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#7a3400">Quotation</text>
	      <text x="586" y="75" text-anchor="middle" font-size="10" fill="#8a4c1c">Price offered</text>

	      <!-- 5. Sales Order -->
	      <rect x="686" y="15" width="140" height="80" rx="10" fill="#fdece0" stroke="#e8730f" stroke-width="1.5"/>
	      <text x="756" y="48" text-anchor="middle" font-size="13" font-weight="700" fill="#7a3400">Sales</text>
	      <text x="756" y="64" text-anchor="middle" font-size="13" font-weight="700" fill="#7a3400">Order</text>
	      <text x="756" y="82" text-anchor="middle" font-size="10" fill="#8a4c1c">Order confirmed</text>

	      <!-- 6. Invoice -->
	      <rect x="856" y="15" width="128" height="80" rx="10" fill="#fdece0" stroke="#e8730f" stroke-width="1.5"/>
	      <text x="920" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#7a3400">Invoice</text>
	      <text x="920" y="75" text-anchor="middle" font-size="10" fill="#8a4c1c">Customer billed</text>

	      <!-- Activity annotation spanning the pipeline -->
	      <line x1="76" y1="150" x2="920" y2="150" stroke="#adb5bd" stroke-width="1.5" stroke-dasharray="4 4"/>
	      <line x1="76" y1="150" x2="76" y2="140" stroke="#adb5bd" stroke-width="1.5"/>
	      <line x1="920" y1="150" x2="920" y2="140" stroke="#adb5bd" stroke-width="1.5"/>
	      <text x="498" y="172" text-anchor="middle" font-size="11" fill="#6c757d">Activity — every interaction along the way is logged</text>
	    </svg>
	  </div>

	  <ul>
	    <li><strong>Lead</strong> &rarr; A prospect comes in and is recorded as a Lead.</li>
	    <li><strong>Account / Contact</strong> &rarr; A qualified Lead is converted into an Account with its Contacts and Addresses.</li>
	    <li><strong>Opportunity</strong> &rarr; A potential deal is opened against the Account and tracked through pipeline stages.</li>
	    <li><strong>Quotation</strong> &rarr; A price quote is sent to the customer based on the Opportunity.</li>
	    <li><strong>Sales Order</strong> &rarr; The customer confirms, and the Quotation is converted into a Sales Order.</li>
	    <li><strong>Invoice</strong> &rarr; The Sales Order is billed to the customer.</li>
	    <li><strong>Activity</strong> &rarr; Every interaction along the way (calls, meetings, notes) is logged against the related record.</li>
	  </ul>

	  <!-- Audit -->
	  <h3 id="flow-audit">5. Audit Trail</h3>
	  <p>Every create/update/delete action performed above is automatically recorded in the <strong>Log Data</strong> module for monitoring and compliance review.</p>
	</section>

	<!-- Product & Pricing Documentation -->
	<section class="col-md-9">
	  <h2 id="productpricing">Product &amp; Pricing</h2>
	  <p class="text-muted">
	    This module manages the product catalog and how each product is priced. It is used to set up what can be sold before it is quoted or ordered in the Sales module.
	  </p>

	  <!-- Relationship diagram -->
	  <div style="overflow-x:auto; margin: 8px 0 24px;">
	    <svg viewBox="0 0 900 280" width="100%" style="min-width:700px; max-width:900px; font-family: inherit;">
	      <defs>
	        <marker id="pp-arrow" markerWidth="10" markerHeight="10" refX="8" refY="3" orient="auto" markerUnits="strokeWidth">
	          <path d="M0,0 L8,3 L0,6 Z" fill="#f0ad4e"/>
	        </marker>
	        <marker id="pp-arrow-gray" markerWidth="10" markerHeight="10" refX="8" refY="3" orient="auto" markerUnits="strokeWidth">
	          <path d="M0,0 L8,3 L0,6 Z" fill="#adb5bd"/>
	        </marker>
	      </defs>

	      <!-- connectors -->
	      <line x1="170" y1="55" x2="214" y2="97" stroke="#f0ad4e" stroke-width="2" marker-end="url(#pp-arrow)"/>
	      <line x1="170" y1="145" x2="214" y2="103" stroke="#f0ad4e" stroke-width="2" marker-end="url(#pp-arrow)"/>
	      <line x1="370" y1="100" x2="414" y2="100" stroke="#f0ad4e" stroke-width="2" marker-end="url(#pp-arrow)"/>
	      <line x1="570" y1="90" x2="614" y2="52" stroke="#f0ad4e" stroke-width="2" marker-end="url(#pp-arrow)"/>
	      <line x1="570" y1="110" x2="614" y2="148" stroke="#f0ad4e" stroke-width="2" marker-end="url(#pp-arrow)"/>
	      <line x1="295" y1="135" x2="295" y2="179" stroke="#adb5bd" stroke-width="2" stroke-dasharray="4 4" marker-end="url(#pp-arrow-gray)"/>

	      <!-- Product Category -->
	      <rect x="20" y="20" width="150" height="70" rx="10" fill="#fff8e6" stroke="#f0ad4e" stroke-width="1.5"/>
	      <text x="95" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#5a4300">Product Category</text>
	      <text x="95" y="75" text-anchor="middle" font-size="10" fill="#7a6120">Classification</text>

	      <!-- Product UOM -->
	      <rect x="20" y="110" width="150" height="70" rx="10" fill="#fff8e6" stroke="#f0ad4e" stroke-width="1.5"/>
	      <text x="95" y="145" text-anchor="middle" font-size="13" font-weight="700" fill="#5a4300">Product UOM</text>
	      <text x="95" y="165" text-anchor="middle" font-size="10" fill="#7a6120">Unit of measure</text>

	      <!-- Product -->
	      <rect x="220" y="65" width="150" height="70" rx="10" fill="#fdece0" stroke="#e8730f" stroke-width="1.5"/>
	      <text x="295" y="100" text-anchor="middle" font-size="13" font-weight="700" fill="#7a3400">Product</text>
	      <text x="295" y="120" text-anchor="middle" font-size="10" fill="#8a4c1c">Catalog item</text>

	      <!-- Price List -->
	      <rect x="420" y="65" width="150" height="70" rx="10" fill="#fff8e6" stroke="#f0ad4e" stroke-width="1.5"/>
	      <text x="495" y="100" text-anchor="middle" font-size="13" font-weight="700" fill="#5a4300">Price List</text>
	      <text x="495" y="120" text-anchor="middle" font-size="10" fill="#7a6120">Named price list</text>

	      <!-- Product Price -->
	      <rect x="620" y="10" width="150" height="70" rx="10" fill="#fff8e6" stroke="#f0ad4e" stroke-width="1.5"/>
	      <text x="695" y="45" text-anchor="middle" font-size="13" font-weight="700" fill="#5a4300">Product Price</text>
	      <text x="695" y="65" text-anchor="middle" font-size="10" fill="#7a6120">Price per list</text>

	      <!-- Product Discount -->
	      <rect x="620" y="120" width="150" height="70" rx="10" fill="#fff8e6" stroke="#f0ad4e" stroke-width="1.5"/>
	      <text x="695" y="155" text-anchor="middle" font-size="13" font-weight="700" fill="#5a4300">Product Discount</text>
	      <text x="695" y="175" text-anchor="middle" font-size="10" fill="#7a6120">Discount rules</text>

	      <!-- Product Bundle Item -->
	      <rect x="220" y="185" width="150" height="80" rx="10" fill="#fdece0" stroke="#e8730f" stroke-width="1.5" stroke-dasharray="5 3"/>
	      <text x="295" y="213" text-anchor="middle" font-size="13" font-weight="700" fill="#7a3400">Product Bundle</text>
	      <text x="295" y="229" text-anchor="middle" font-size="13" font-weight="700" fill="#7a3400">Item</text>
	      <text x="295" y="247" text-anchor="middle" font-size="10" fill="#8a4c1c">Combo of products</text>
	    </svg>
	  </div>

	  <!-- Product Category -->
	  <h3 id="pp-category">1. Product Category</h3>
	  <p>Groups products into categories for classification, navigation, and reporting (e.g. Hardware, Software, Services).</p>

	  <!-- Product UOM -->
	  <h3 id="pp-uom">2. Product UOM</h3>
	  <p>Defines the unit of measure a product is sold in (e.g. pcs, box, kg, hour).</p>

	  <!-- Product -->
	  <h3 id="pp-product">3. Product</h3>
	  <p>The core catalog entry: what is being sold. Each Product belongs to a Product Category, is measured in a Product UOM, and is what gets referenced by Quotation, Sales Order, and Invoice items in the Sales module.</p>

	  <!-- Price List -->
	  <h3 id="pp-pricelist">4. Price List</h3>
	  <p>A named collection of prices — for example a different Price List per customer segment, region, or currency. A Product can have different prices across different Price Lists.</p>

	  <!-- Product Price -->
	  <h3 id="pp-price">5. Product Price</h3>
	  <p>The actual price of a specific Product within a specific Price List. This is the value used when a Product is added to a Quotation or Sales Order.</p>

	  <!-- Product Discount -->
	  <h3 id="pp-discount">6. Product Discount</h3>
	  <p>Discount rules that can be applied on top of a Product's price (e.g. percentage off, fixed amount off), optionally scoped to a Price List or time period.</p>

	  <!-- Product Bundle Item -->
	  <h3 id="pp-bundle">7. Product Bundle Item</h3>
	  <p>Combines multiple Products into a single sellable bundle (e.g. a starter package), so the bundle can be quoted and sold as one line item instead of adding each Product separately.</p>
	</section>

	<!-- Business Scenarios Documentation -->
	<section class="col-md-9">
	  <h2 id="businessscenarios">Business Scenarios</h2>
	  <p class="text-muted">
	    Real-world walkthroughs showing how Product, Price List, Product Price, Product Discount, and Product Bundle Item work together.
	  </p>

	  <!-- Scenario 1 -->
	  <p><strong>Scenario 1 &mdash; Same product, different price per customer segment</strong></p>
	  <p><strong>Setup:</strong></p>
	  <ul>
	    <li>Product: <strong>Laptop ProBook 14</strong> (LPT-PB14)</li>
	    <li>Price List: <strong>Retail</strong> and <strong>Corporate</strong></li>
	    <li>Product Price: Retail = Rp 12.500.000, Corporate = Rp 11.200.000</li>
	    <li>Product Discount: 5% on the Corporate price list, valid 1&ndash;31 Dec</li>
	  </ul>
	  <p><strong>Flow:</strong> Account "PT Maju Jaya" (Corporate) orders 20 units in December. The Quotation uses the Corporate price list &rarr; Rp 11.200.000/unit, and the December discount applies &rarr; <strong>Rp 10.640.000/unit</strong>. This price carries through unchanged to the Sales Order and Invoice.</p>

	  <!-- Scenario 2 -->
	  <p><strong>Scenario 2 &mdash; Bundling multiple products into one package</strong></p>
	  <p><strong>Setup:</strong></p>
	  <ul>
	    <li>Products: Laptop ProBook 14 (Rp 12.500.000), Mouse Wireless MX (Rp 350.000), Tas Laptop 14&Prime; (Rp 250.000)</li>
	    <li>Product Bundle Item: <strong>Paket Kerja Starter</strong> (BDL-STARTER01) &mdash; sum of parts Rp 13.100.000, bundle price Rp 12.750.000</li>
	  </ul>
	  <p><strong>Flow:</strong> A Lead converted to Account wants a WFH work laptop set. The sales rep adds a single line item, <strong>Paket Kerja Starter</strong>, to the Quotation instead of three separate lines. The bundle price of <strong>Rp 12.750.000</strong> is used directly, and the same single line carries through to Sales Order and Invoice &mdash; while the underlying components remain traceable for stock/reporting.</p>

	  <!-- Scenario 3 -->
	  <p><strong>Scenario 3 &mdash; Combining a Discount with a Bundle</strong></p>
	  <p><strong>Setup:</strong></p>
	  <ul>
	    <li>Product Bundle Item: <strong>Paket Kerja Lengkap</strong> (BDL-COMPLETE01) &mdash; Laptop + Mouse + Tas + Printer, sum of parts Rp 14.000.000, bundle price Rp 13.300.000</li>
	    <li>Price List: <strong>Corporate</strong></li>
	    <li>Product Discount: 10% on Corporate price list, applies to bundle items, promo "Back to Office" 1&ndash;28 Feb</li>
	  </ul>
	  <p><strong>Flow:</strong> Account "PT Sinar Abadi" (Corporate) orders 15 bundles in February. Quotation starts from the bundle price Rp 13.300.000, then the Feb promo discount of 10% applies on top &rarr; <strong>Rp 11.970.000/bundle</strong>. Total for 15 bundles = <strong>Rp 179.550.000</strong>, with both layers of savings (bundle price + promo discount) calculated automatically.</p>

	  <!-- Scenario 4 -->
	  <p><strong>Scenario 4 &mdash; Stackable Discount (multiple discounts combined)</strong></p>
	  <p><strong>Setup:</strong> Laptop ProBook 14 on the Corporate price list (Rp 11.200.000/unit), with three discounts active together (all <code>stackable = true</code>):</p>
	  <ul>
	    <li>Corporate Loyalty Discount &mdash; 5%</li>
	    <li>Promo Back to Office &mdash; 10%</li>
	    <li>Volume Discount &mdash; 3% (triggered when quantity &ge; 10)</li>
	  </ul>
	  <p><strong>Flow:</strong> Account "PT Nusantara Teknologi" (Corporate) orders 12 units in February, so all three discounts apply, in sequence, each one calculated from the price left over by the previous step:</p>
	  <div class="table-responsive">
	    <table class="table table-sm table-bordered" style="max-width:640px;">
	      <thead class="thead-light">
	        <tr><th>Step</th><th>Discount</th><th>Calculation</th><th>Price / unit after</th></tr>
	      </thead>
	      <tbody>
	        <tr><td>Base</td><td>&mdash;</td><td>&mdash;</td><td>Rp 11.200.000</td></tr>
	        <tr><td>1</td><td>Corporate Loyalty 5%</td><td>11.200.000 &times; (1 &minus; 0,05)</td><td>Rp 10.640.000</td></tr>
	        <tr><td>2</td><td>Promo Back to Office 10%</td><td>10.640.000 &times; (1 &minus; 0,10)</td><td>Rp 9.576.000</td></tr>
	        <tr><td>3</td><td>Volume Discount 3%</td><td>9.576.000 &times; (1 &minus; 0,03)</td><td><strong>Rp 9.288.720</strong></td></tr>
	      </tbody>
	    </table>
	  </div>
	  <p>Total for 12 units = <strong>Rp 111.464.640</strong>. Note this is <em>sequential</em> (multiplicative) stacking, not additive: summing the percentages flat (5+10+3=18%) would instead give Rp 9.184.000/unit (Rp 110.208.000 total) &mdash; a different, lower number. The effective sequential discount is only ~17.06%, not 18%, because each discount is taken from the price left over by the one before it.</p>

	  <!-- Scenario 5 -->
	  <p><strong>Scenario 5 &mdash; Stackable Discount with a Cap (maximum limit)</strong></p>
	  <p><strong>Setup:</strong> Same as Scenario 4, plus a business rule: <code>max_discount_cap = 15%</code>, set at the Price List or Application Setting level to protect margin regardless of how many discounts qualify.</p>
	  <div class="table-responsive">
	    <table class="table table-sm table-bordered" style="max-width:720px;">
	      <thead class="thead-light">
	        <tr><th>Method</th><th>Price / unit</th><th>Total (12 units)</th></tr>
	      </thead>
	      <tbody>
	        <tr><td>Additive (18% flat &mdash; incorrect approach)</td><td>Rp 9.184.000</td><td>Rp 110.208.000</td></tr>
	        <tr><td>Sequential, no cap</td><td>Rp 9.288.720</td><td>Rp 111.464.640</td></tr>
	        <tr><td><strong>Sequential + 15% cap (used)</strong></td><td><strong>Rp 9.520.000</strong></td><td><strong>Rp 114.240.000</strong></td></tr>
	      </tbody>
	    </table>
	  </div>
	  <p>Sequential stacking alone works out to ~17.06% off, which exceeds the 15% cap &mdash; so the system falls back to the capped price (Rp 11.200.000 &times; 0,85 = Rp 9.520.000/unit) instead of the fully-stacked result.</p>
	  <p><strong>Two common ways to enforce a cap:</strong></p>
	  <ul>
	    <li><strong>Cap the final result</strong> &mdash; calculate the full stack first, then override with the capped price if it exceeds the limit. Simple and easy to audit ("your discount is capped at 15%").</li>
	    <li><strong>Stop applying once the cap is reached</strong> &mdash; apply discounts one by one in priority order, and skip any further discount once the cumulative total would exceed the cap. More complex, but lets the system explain exactly which discount was skipped and why.</li>
	  </ul>
	  <p>To support this, <code>Product Discount</code> would need at minimum: an <code>is_stackable</code> flag, a <code>sequence</code>/priority for the order discounts are applied in, and a place to store the cap (per Price List, per Product, or global). Without a cap, combining too many promotions at once can silently erode margin as more promotions are introduced over time.</p>
	</section>

  </div>
</div>
