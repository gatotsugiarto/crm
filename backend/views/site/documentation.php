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

	    <!-- LEAD -->
	    <li class="nav-item">
	          <a class="nav-link text-white fw-bold" href="#lead">Lead</a>
	        </li>

	    <!-- SALES PATH -->
	    <li class="nav-item">
	          <a class="nav-link text-white fw-bold" href="#salespath">Sales Path</a>
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
	  <p>Handles authentication and authorization: backend users and roles &amp; permissions (RBAC).</p>
	  <ul>
	    <li><strong>Users Access:</strong> Manage backend users with system access.</li>
	    <li><strong>Assignments:</strong> Assign roles to backend users.</li>
	    <li><strong>Role:</strong> Define collections of permissions grouped into roles.</li>
	    <li><strong>Permission:</strong> Specify individual actions a user/role is allowed to perform.</li>
	    <li><strong>Change Password:</strong> Self-service password update for users.</li>
	  </ul>

	  <!-- Master -->
	  <h3 id="module-master">2. Master</h3>
	  <p>Shared reference/master data used across the other modules.</p>
	  <ul>
	    <li><strong>Team:</strong> Sales teams (Team Leader and members) used by Leads, Accounts and Opportunities.</li>
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
	  <p>Before running the business process, reference data is prepared in the <strong>Master</strong> module: Team, Application Setting, and address data (Country, Province, City, Postal Code).</p>

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
	    <li><strong>Quotation</strong> &rarr; A price quote (SPH) is sent to the customer based on the Opportunity. One quotation covers <strong>one business line</strong>
	        (<strong>NHS</strong> NextSys Hospitality, <strong>NXG</strong> NextGO, <strong>IPTV</strong> Vision+), taken from the products. <em>Create Quotation</em> on an
	        Opportunity with products from several lines makes one quotation per line. Numbers run per line per year, e.g.
	        <code>001/SPH/SLS-NHS/EXT/IX/2026</code>, <code>001/SPH/SLS-IPTV/EXT/IX/2026</code>. To revise, change the Opportunity Products and click <em>Revise Quotation</em> (the same button): it creates the new quotation and sets the old Draft/Sent one to <em>Rejected</em>. Don't reject the old one by hand first &mdash; that closes the Opportunity as Closed Lost.</li>
	    <li><strong>Closed Won / Lost</strong> &rarr; The Opportunity closes once none of its quotations is still Draft or Sent: <strong>Closed Won</strong> with the total of the
	        approved quotations, or <strong>Closed Lost</strong> if all were rejected.</li>
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
	  <p><strong>Business Line</strong> (NHS = NextSys Hospitality, NXG = NextGO, IPTV = Vision+) says which sales line quotes the product. It becomes the line code
	    in the quotation number (<code>001/SPH/SLS-NHS/EXT/IX/2026</code>), and a quotation can only hold products of one line. Set it on every product that is sold;
	    a product without a Business Line can't be put on a quotation.</p>
	  <p><strong>Service Type</strong> (FTTHD / Metro-E / Other) and <strong>Bandwidth</strong> (e.g. 100 Mbps) fill the Service Information box of the Sales Order form:
	    FTTHD and Metro-E tick their own box, anything else ticks <em>Lain-lain</em>. Non-internet products (NextSys, Vision+, NextGO, STB) are <em>Other</em> without bandwidth.</p>

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

	  <!-- Business Scenarios -->
	  <h3 id="pp-scenarios">8. Business Scenarios</h3>
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

	<!-- Lead Documentation -->
	<section class="col-md-9">
	  <h2 id="lead">Lead</h2>
	  <p class="text-muted">
	    How to record a new prospect as a Lead, what each field means, what happens when the Lead is converted, and how to complete the Account afterwards.
	    Menu: <strong>Sales &rarr; Lead Management</strong>. Available to the <strong>Sales</strong> and <strong>Sales Manager</strong> roles (and root); other roles can view only.
	  </p>

	  <!-- Add a Lead -->
	  <h3 id="lead-add">1. Add a New Lead</h3>
	  <ol>
	    <li>Open <strong>Sales</strong> in the sidebar and click the <strong>Lead Management</strong> card.</li>
	    <li>Click <strong>+ New Data</strong> (top right). The form opens in a pop-up.</li>
	    <li>Fill in the fields below. Fields marked <span class="text-danger">*</span> are required.</li>
	    <li>Click <strong>Save</strong>. The pop-up closes, a green message appears, and the Lead shows up in the list.
	        If a required field is empty, a red message appears under it &mdash; complete it and save again.</li>
	  </ol>

	  <div class="table-responsive">
	    <table class="table table-sm table-bordered" style="max-width:820px;">
	      <thead class="thead-light">
	        <tr><th>Field</th><th>What to enter</th><th>Example</th></tr>
	      </thead>
	      <tbody>
	        <tr><td>Company Name <span class="text-danger">*</span></td><td>Prospect company name</td><td>PT Contoh Media</td></tr>
	        <tr><td>Contact Name <span class="text-danger">*</span></td><td>Person you are talking to</td><td>Rina Wijaya</td></tr>
	        <tr><td>Email <span class="text-danger">*</span></td><td>Contact email</td><td>rina@contoh.co.id</td></tr>
	        <tr><td>Phone <span class="text-danger">*</span></td><td>Contact phone</td><td>0812-3456-7890</td></tr>
	        <tr><td>Lead Source <span class="text-danger">*</span></td><td>Where the lead came from (free text)</td><td>Website, Referral, Cold Call, Exhibition, Event</td></tr>
	        <tr><td>Industry <span class="text-danger">*</span></td><td>Line of business (free text)</td><td>Hospitality, Retail, Technology, Property</td></tr>
	        <tr><td>Customer Segment</td><td>Optional. Segment/channel, same list as Product (pick or type). Copied to the Account on Convert</td><td>Hospitality, B2B2C (ISP), B2B, B2C</td></tr>
	        <tr><td>Address <span class="text-danger">*</span></td><td>Full street address</td><td>Jl. Sudirman No. 10</td></tr>
	        <tr><td>Country / Province / City / Postal Code <span class="text-danger">*</span></td><td>Pick from the lists (Master Data)</td><td>INDONESIA / DKI JAKARTA / JAKARTA SELATAN / 12190</td></tr>
	        <tr><td>Owner User <span class="text-danger">*</span></td><td>The <strong>sales team</strong> handling this lead (a team, not a person)</td><td>Enterprise Sales, SMB Sales</td></tr>
	        <tr><td>Description <span class="text-danger">*</span></td><td>What the prospect needs. Copied to the Account's and the Opportunity's description on Convert</td><td>Apartemen 3 tower, butuh layanan TV berlangganan untuk penghuni</td></tr>
	      </tbody>
	    </table>
	  </div>

	  <!-- After saving -->
	  <h3 id="lead-convert">2. After Saving: Edit and Convert</h3>
	  <ul>
	    <li><strong>Edit</strong> &mdash; pencil icon on the Lead's row.</li>
	    <li><strong>Delete</strong> &mdash; only for Leads that are not converted yet. A converted Lead is kept as the history of where its Account came from
	        (the delete button is greyed out); deleting a Lead never removes its Account, Contact or Opportunity.</li>
	    <li><strong>Convert</strong> &mdash; the <i class="fa fa-exchange-alt"></i> icon, once the Lead is qualified. After confirming, the system creates in one step:
	      <ul>
	        <li>an <strong>Account</strong> (the company, Customer Type <em>Prospect</em>, with the Lead's Customer Segment and description),
	            whose address is also listed under <strong>Account Addresses</strong> as <em>Billing</em>, <em>Shipping</em> and <em>Office</em> addresses
	            (edit any that differ),</li>
	        <li>a <strong>Contact</strong> (the person, set as primary contact),</li>
	        <li>an <strong>Opportunity</strong> &ldquo;Opportunity - {company}&rdquo; with the Lead's description, at stage <em>Prospecting</em>, 10% probability, closing in 30 days.
	            Rename it to describe the deal (e.g. &ldquo;TV Berlangganan 3 Tower&rdquo;), since one company can have several opportunities.</li>
	      </ul>
	      A Lead can only be converted once, and only by <strong>members of the Lead's Sales Team</strong> or the <strong>Sales Manager</strong>
	      (others see &ldquo;This lead belongs to &hellip;&rdquo;). The same applies to editing a Lead. Sales users need a team
	      (User Management &rarr; Edit User Access &rarr; Sales Team) to work on existing Leads.
	      Continue the work in <strong>Accounts</strong> and <strong>Opportunities</strong>;
	      the individual salesperson can be set on the Account as <strong>Assigned Sales</strong>.
	    </li>
	  </ul>

	  <!-- Complete the Account -->
	  <h3 id="lead-account">3. After Converting: Complete the Account</h3>
	  <p>Open <strong>Sales &rarr; Accounts</strong>, click the company name, then use the pencil icon on the Accounts list to edit it.</p>
	  <div class="table-responsive">
	    <table class="table table-sm table-bordered" style="max-width:820px;">
	      <thead class="thead-light">
	        <tr><th>Field</th><th>What to do</th></tr>
	      </thead>
	      <tbody>
	        <tr><td>Customer Type</td><td>Stays <em>Prospect</em> after Convert. Change it to <em>Customer</em> once the deal is won (or Partner / Reseller / Vendor if that fits better).</td></tr>
	        <tr><td>Customer Segment</td><td>Filled from the Lead. Check it, or set it if the Lead didn't have one (e.g. <em>Hospitality</em>).</td></tr>
	        <tr><td>Sales Team</td><td>Filled from the Lead's Owner User. Only a <strong>Sales Manager</strong> can change it afterwards (the field is locked for the Sales role).</td></tr>
	        <tr><td>Assigned Sales</td><td>The individual salesperson responsible for this account, set by the <strong>Sales Manager</strong> (locked for the Sales role). The list only shows members of the chosen Sales Team, and changes when the Sales Team changes.</td></tr>
	        <tr><td>Price List</td><td>The price tier for this customer: <em>Corporate Price</em> for companies (hotels, apartments, ISPs), <em>Retail Price</em> for individuals or small shops.
	            Don't use <em>Promotional Price</em> as a default. For now this is informational only &mdash; prices on Opportunities and Quotations are still typed in by hand.</td></tr>
	      </tbody>
	    </table>
	  </div>

	  <p><strong>Account Addresses.</strong> Convert copies the Lead's address into three addresses. Scroll to <strong>Other Address</strong> on the Account detail page and edit any that differ:</p>
	  <div class="table-responsive">
	    <table class="table table-sm table-bordered" style="max-width:820px;">
	      <thead class="thead-light">
	        <tr><th>Address Type</th><th>Used for</th><th>Example of a different address</th></tr>
	      </thead>
	      <tbody>
	        <tr><td>Billing Address</td><td>Where invoices are sent (finance department)</td><td>Tower A Lt. 2, Finance &ndash; Building Management</td></tr>
	        <tr><td>Shipping Address</td><td>Where goods are delivered (e.g. STB to the installation site)</td><td>Loading Dock Tower C, attn. Engineering</td></tr>
	        <tr><td>Office Address</td><td>The customer's office or another branch</td><td>Marketing Gallery, Jl. Pluit Raya No. 25</td></tr>
	      </tbody>
	    </table>
	  </div>
	  <p>Use <strong>Add Address</strong> for extra branches or delivery sites. The Account's own <em>Main Address</em> stays as entered on the Lead.</p>

	  <ul>
	    <li><strong>Contacts</strong> &mdash; the Lead's contact is already there as primary. Use <strong>Add Contact</strong> for other people (finance, engineering) and click <em>Yes/No</em> in the <em>Is Primary</em> column to change the main contact.</li>
	    <li><strong>Documents</strong> &mdash; upload contracts, NPWP, company profile, etc. (several files at once; pdf, Office files, images, zip). Everyone with read access can download them.</li>
	    <li>Then continue with the <strong>Opportunity</strong> created by Convert: add products, move it through the stages, and create a Quotation.</li>
	    <li><strong>Deleting an Account</strong> (Sales Manager) also deletes its contacts, addresses, documents and any opportunity that was never worked on
	        (still Prospecting, no products, quotations or activities). If it has an opportunity in progress or closed, quotations, sales orders,
	        invoices or activities, the delete is refused and the message says what is left.</li>
	  </ul>

	  <!-- Notes -->
	  <h3 id="lead-notes">4. Things to Watch</h3>
	  <ul>
	    <li><strong>Location data currently covers Jakarta only</strong> (DKI Jakarta, 5 cities, 10 postal codes). All four location fields are required,
	        so a Lead from another city can only be saved after that city and postal code are added in <strong>Master Data</strong> (currently root only).</li>
	    <li><strong>The location lists don't filter each other</strong> &mdash; choosing a city does not narrow the postal codes. Make sure the postal code belongs to the chosen city.</li>
	    <li><strong>Email format is not checked</strong> &mdash; double-check it before saving.</li>
	    <li><strong>Lead Source and Industry are free text</strong> &mdash; use consistent spelling (e.g. <em>Website, Referral, Cold Call</em>) so reports group them correctly.</li>
	    <li><strong>There is no duplicate check</strong> &mdash; entering and converting the same company twice creates two Accounts, two Contacts and two Opportunities.
	        Search <strong>Lead Management</strong> and <strong>Accounts</strong> for the company name first; if it's already there, edit that record instead.</li>
	  </ul>

	  <!-- Sample data -->
	  <h3 id="lead-samples">5. Sample Data</h3>
	  <p>Ready-made dummy Leads for testing or training. Cities and postal codes match the existing master data. Click a value to copy it, then paste it into the form
	     (<span class="badge badge-info">type</span> = paste into the text field, <span class="badge badge-secondary">select</span> = pick that option in the dropdown).</p>

	  <ul class="nav nav-tabs" role="tablist">
	    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#lead-sample-1" role="tab">Lead 1 &mdash; Hotel</a></li>
	    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#lead-sample-2" role="tab">Lead 2 &mdash; ISP</a></li>
	    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#lead-sample-3" role="tab">Lead 3 &mdash; Cafe</a></li>
	    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#lead-sample-4" role="tab">Lead 4 &mdash; Retail</a></li>
	    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#lead-sample-5" role="tab">Lead 5 &mdash; Apartment</a></li>
	  </ul>
	  <div class="tab-content">
	    <div class="tab-pane fade show active" id="lead-sample-1" role="tabpanel">
	      <table class="table table-sm table-bordered mt-2" style="max-width:760px;">
	        <thead class="thead-light"><tr><th style="width:150px;">Field</th><th style="width:70px;">Input</th><th>Value <small class="text-muted">(click to copy)</small></th></tr></thead>
	        <tbody>
	          <tr><td>Company Name</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">PT Grand Sentosa Hotel</td></tr>
	          <tr><td>Contact Name</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Andi Pratama</td></tr>
	          <tr><td>Email</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">andi.pratama@grandsentosa.co.id</td></tr>
	          <tr><td>Phone</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">0812-1111-2233</td></tr>
	          <tr><td>Lead Source</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Exhibition</td></tr>
	          <tr><td>Industry</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Hospitality</td></tr>
	          <tr><td>Customer Segment</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">Hospitality</td></tr>
	          <tr><td>Address</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Jl. Jend. Sudirman Kav. 52, Senayan</td></tr>
	          <tr><td>Country</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">INDONESIA</td></tr>
	          <tr><td>Province</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">DKI JAKARTA</td></tr>
	          <tr><td>City</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">JAKARTA SELATAN</td></tr>
	          <tr><td>Postal Code</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">12190</td></tr>
	          <tr><td>Owner User</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">Enterprise Sales</td></tr>
	          <tr><td>Description</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Butuh IPTV untuk 250 kamar, minta demo bulan depan</td></tr>
	        </tbody>
	      </table>
	    </div>
	    <div class="tab-pane fade" id="lead-sample-2" role="tabpanel">
	      <table class="table table-sm table-bordered mt-2" style="max-width:760px;">
	        <thead class="thead-light"><tr><th style="width:150px;">Field</th><th style="width:70px;">Input</th><th>Value <small class="text-muted">(click to copy)</small></th></tr></thead>
	        <tbody>
	          <tr><td>Company Name</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">PT Jaringan Nusa Link</td></tr>
	          <tr><td>Contact Name</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Siti Rahmawati</td></tr>
	          <tr><td>Email</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">siti.rahma@nusalink.net.id</td></tr>
	          <tr><td>Phone</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">0813-2222-3344</td></tr>
	          <tr><td>Lead Source</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Referral</td></tr>
	          <tr><td>Industry</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Telecommunication</td></tr>
	          <tr><td>Customer Segment</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">B2B2C (ISP)</td></tr>
	          <tr><td>Address</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Jl. Pemuda No. 88, Rawamangun</td></tr>
	          <tr><td>Country</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">INDONESIA</td></tr>
	          <tr><td>Province</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">DKI JAKARTA</td></tr>
	          <tr><td>City</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">JAKARTA TIMUR</td></tr>
	          <tr><td>Postal Code</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">13410</td></tr>
	          <tr><td>Owner User</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">Enterprise Sales</td></tr>
	          <tr><td>Description</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">ISP lokal, tertarik bundling Vision+ untuk pelanggan broadband</td></tr>
	        </tbody>
	      </table>
	    </div>
	    <div class="tab-pane fade" id="lead-sample-3" role="tabpanel">
	      <table class="table table-sm table-bordered mt-2" style="max-width:760px;">
	        <thead class="thead-light"><tr><th style="width:150px;">Field</th><th style="width:70px;">Input</th><th>Value <small class="text-muted">(click to copy)</small></th></tr></thead>
	        <tbody>
	          <tr><td>Company Name</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">CV Kopi Kita Bersama</td></tr>
	          <tr><td>Contact Name</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Budi Hartono</td></tr>
	          <tr><td>Email</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">budi@kopikita.id</td></tr>
	          <tr><td>Phone</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">0857-3333-4455</td></tr>
	          <tr><td>Lead Source</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Website</td></tr>
	          <tr><td>Industry</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Food &amp; Beverage</td></tr>
	          <tr><td>Customer Segment</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">B2B</td></tr>
	          <tr><td>Address</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Jl. Kebon Sirih No. 12, Menteng</td></tr>
	          <tr><td>Country</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">INDONESIA</td></tr>
	          <tr><td>Province</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">DKI JAKARTA</td></tr>
	          <tr><td>City</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">JAKARTA PUSAT</td></tr>
	          <tr><td>Postal Code</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">10110</td></tr>
	          <tr><td>Owner User</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">SMB Sales</td></tr>
	          <tr><td>Description</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">5 cabang kafe, butuh konten TV untuk area pelanggan</td></tr>
	        </tbody>
	      </table>
	    </div>
	    <div class="tab-pane fade" id="lead-sample-4" role="tabpanel">
	      <table class="table table-sm table-bordered mt-2" style="max-width:760px;">
	        <thead class="thead-light"><tr><th style="width:150px;">Field</th><th style="width:70px;">Input</th><th>Value <small class="text-muted">(click to copy)</small></th></tr></thead>
	        <tbody>
	          <tr><td>Company Name</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">PT Maju Elektronik Sejahtera</td></tr>
	          <tr><td>Contact Name</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Dewi Lestari</td></tr>
	          <tr><td>Email</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">dewi.lestari@majuelektronik.com</td></tr>
	          <tr><td>Phone</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">0821-4444-5566</td></tr>
	          <tr><td>Lead Source</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Cold Call</td></tr>
	          <tr><td>Industry</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Retail</td></tr>
	          <tr><td>Customer Segment</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">B2C</td></tr>
	          <tr><td>Address</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Jl. Daan Mogot Km. 11, Cengkareng</td></tr>
	          <tr><td>Country</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">INDONESIA</td></tr>
	          <tr><td>Province</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">DKI JAKARTA</td></tr>
	          <tr><td>City</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">JAKARTA BARAT</td></tr>
	          <tr><td>Postal Code</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">11220</td></tr>
	          <tr><td>Owner User</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">SMB Sales</td></tr>
	          <tr><td>Description</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Toko elektronik, minat jual STB retail (beli putus)</td></tr>
	        </tbody>
	      </table>
	    </div>
	    <div class="tab-pane fade" id="lead-sample-5" role="tabpanel">
	      <table class="table table-sm table-bordered mt-2" style="max-width:760px;">
	        <thead class="thead-light"><tr><th style="width:150px;">Field</th><th style="width:70px;">Input</th><th>Value <small class="text-muted">(click to copy)</small></th></tr></thead>
	        <tbody>
	          <tr><td>Company Name</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">PT Pantai Indah Residence</td></tr>
	          <tr><td>Contact Name</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Hendra Wijaya</td></tr>
	          <tr><td>Email</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">hendra.w@pantaiindahresidence.co.id</td></tr>
	          <tr><td>Phone</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">0811-5555-6677</td></tr>
	          <tr><td>Lead Source</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Event</td></tr>
	          <tr><td>Industry</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Property</td></tr>
	          <tr><td>Customer Segment</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">Hospitality</td></tr>
	          <tr><td>Address</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Jl. Pantai Indah Kapuk Boulevard No. 1</td></tr>
	          <tr><td>Country</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">INDONESIA</td></tr>
	          <tr><td>Province</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">DKI JAKARTA</td></tr>
	          <tr><td>City</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">JAKARTA UTARA</td></tr>
	          <tr><td>Postal Code</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">14240</td></tr>
	          <tr><td>Owner User</td><td><span class="badge badge-secondary">select</span></td><td class="lead-copy" title="Click to copy">Enterprise Sales</td></tr>
	          <tr><td>Description</td><td><span class="badge badge-info">type</span></td><td class="lead-copy" title="Click to copy">Apartemen 3 tower, butuh layanan TV berlangganan untuk penghuni</td></tr>
	        </tbody>
	      </table>
	    </div>
	  </div>
	  <p class="text-muted small">After adding them, try <strong>Convert</strong> on one (e.g. Lead 1) to see its Account, Contact and Opportunity being created.</p>
	</section>

	<!-- Sales Path Documentation -->
	<section class="col-md-9">
	  <h2 id="salespath">Sales Path: from Lead to Paid Invoice</h2>
	  <p class="text-muted">
	    The whole road of one deal, step by step: who does it, which menu or button, and what the system does by itself.
	    Roles: <strong>Sales</strong> (e.g. Iqbal, Bagus) and <strong>Sales Manager</strong> (e.g. Fikri). Root can do everything.
	  </p>

	  <div class="d-flex flex-wrap align-items-center mb-3 small" style="gap:6px;">
	    <span class="badge badge-secondary p-2">Lead</span><i class="fa fa-arrow-right text-muted"></i>
	    <span class="badge badge-secondary p-2">Account + Contact + Opportunity</span><i class="fa fa-arrow-right text-muted"></i>
	    <span class="badge badge-info p-2">Opportunity Products</span><i class="fa fa-arrow-right text-muted"></i>
	    <span class="badge badge-info p-2">Quotation (SPH)</span><i class="fa fa-arrow-right text-muted"></i>
	    <span class="badge badge-success p-2">Approved &rarr; Sales Order</span><i class="fa fa-arrow-right text-muted"></i>
	    <span class="badge badge-primary p-2">Confirm SO &rarr; Invoice</span><i class="fa fa-arrow-right text-muted"></i>
	    <span class="badge badge-dark p-2">Sent &rarr; Paid</span>
	  </div>

	  <h3 id="sp-steps">1. Steps</h3>
	  <div class="table-responsive">
	    <table class="table table-sm table-bordered" style="max-width:980px;">
	      <thead class="thead-light">
	        <tr><th>#</th><th>Step</th><th>Who</th><th>Where / button</th><th>What happens automatically</th><th>Opportunity stage</th></tr>
	      </thead>
	      <tbody>
	        <tr><td>1</td><td><strong>Record the prospect</strong></td><td>Sales</td><td>Sales &rarr; Lead Management &rarr; <em>+ New Data</em></td>
	            <td>Lead saved, owned by a Sales Team (see the <a href="#lead">Lead</a> menu).</td><td>&ndash;</td></tr>
	        <tr><td>2</td><td><strong>Convert</strong> the qualified Lead</td><td>Sales of the Lead's team, or Sales Manager</td><td>Lead list &rarr; <i class="fa fa-exchange-alt"></i> Convert</td>
	            <td>Account (Prospect) + its Billing/Shipping/Office addresses, primary Contact, and an Opportunity.</td><td>Prospecting 10%</td></tr>
	        <tr><td>3</td><td><strong>Complete the Account</strong></td><td>Sales; Sales Manager for Sales Team / Assigned Sales</td><td>Sales &rarr; Accounts &rarr; edit</td>
	            <td>Assigned Sales becomes the signer of the SPH. Set the Price List (Corporate / Retail).</td><td>&ndash;</td></tr>
	        <tr><td>4</td><td><strong>Work the Opportunity</strong>: rename it, add products</td><td>Sales</td><td>Sales &rarr; Opportunities &rarr; open &rarr; <em>Add Opportunity Product</em></td>
	            <td>Amount = sum of the products (qty &times; price &minus; discount). Every product needs a <strong>Business Line</strong> (NHS / NXG / IPTV).</td><td>Qualification (set by hand)</td></tr>
	        <tr><td>5</td><td><strong>Create Quotation</strong></td><td>Sales</td><td>Opportunity page &rarr; <em>Create Quotation</em></td>
	            <td>One Draft quotation <strong>per business line</strong>, numbered e.g. <code>001/SPH/SLS-NHS/EXT/X/2026</code>, items and SPH texts copied in.</td><td>Proposal 50%</td></tr>
	        <tr><td>6</td><td><strong>Check and send the SPH</strong></td><td>Sales (the Assigned Sales)</td><td>Quotation page &rarr; <em>SPH (PDF)</em>, then edit &rarr; Status <em>Sent</em></td>
	            <td>&ndash;</td><td>Proposal 50%</td></tr>
	        <tr class="table-light"><td>6b</td><td><em>Customer asks for changes</em></td><td>Sales</td><td>Change the Opportunity Products &rarr; <em>Revise Quotation</em> (same button)</td>
	            <td>New quotation (next number); the old Draft/Sent one becomes <strong>Rejected</strong>. Don't reject it by hand first.</td><td>Proposal</td></tr>
	        <tr><td>7</td><td><strong>Approve</strong> (customer agreed)</td><td><strong>Sales Manager</strong></td><td>Quotation page &rarr; <em>Approve &amp; Generate SO</em></td>
	            <td>Sales Order (Draft) with the same items. The quotation is locked. When <em>all</em> quotations of the opportunity are decided, the opportunity closes.</td>
	            <td><span class="badge badge-primary">Closed Won</span> 100%, amount = approved total<br><small>(all rejected &rarr; <span class="badge badge-warning">Closed Lost</span>)</small></td></tr>
	        <tr><td>8</td><td>Account becomes a customer</td><td>Sales</td><td>Accounts &rarr; edit &rarr; Customer Type <em>Customer</em></td><td>Not automatic yet.</td><td>&ndash;</td></tr>
	        <tr><td>8b</td><td><strong>Complete and print the SO form</strong></td><td><strong>Sales Manager</strong> (print: Sales too)</td>
	            <td>Sales Order page &rarr; <em>Edit SO Details</em>, then <em>SO (PDF)</em></td>
	            <td>Fill trial / contract period, RFS date, No. PKS, installation and billing address + contact (empty = the account's Shipping / Billing address and primary contact).
	                The PDF is the 2-page SALES ORDER form for the customer's signature: service type ticked from the products, items, totals, Internal Use names
	                (Account Manager = Assigned Sales, Sec. Head = team leader, Dept. Head = who confirms).</td><td>&ndash;</td></tr>
	        <tr><td>9</td><td><strong>Confirm the Sales Order</strong></td><td><strong>Sales Manager</strong></td><td>Sales &rarr; Sales Order &rarr; open &rarr; <em>Confirm SO</em></td>
	            <td>SO &rarr; Confirmed and an <strong>Invoice</strong> (Draft, due in 30 days) with the same items. Only once.
                The confirm date and the confirming manager are printed under Internal Use on the SO form.</td><td>&ndash;</td></tr>
	        <tr><td>10</td><td><strong>Send the invoice</strong></td><td><strong>Sales Manager</strong></td><td>Invoice page &rarr; <em>Invoice PDF</em>, <em>Mark as Sent</em></td><td>&ndash;</td><td>&ndash;</td></tr>
	        <tr><td>11</td><td><strong>Payment received</strong></td><td><strong>Sales Manager</strong></td><td>Invoice page &rarr; <em>Mark as Paid</em></td><td>&ndash;</td><td>&ndash;</td></tr>
	        <tr><td>12</td><td>Delivery / installation done</td><td><strong>Sales Manager</strong></td><td>Sales Order &rarr; edit &rarr; Status <em>Completed</em> (or <em>Cancelled</em>)</td><td>&ndash;</td><td>&ndash;</td></tr>
	      </tbody>
	    </table>
	  </div>

	  <h3 id="sp-confirm">2. What &ldquo;Confirm SO&rdquo; means</h3>
	  <p>
	    Approving the quotation records that the customer agreed; the Sales Order is created as <strong>Draft</strong>.
	    <strong>Confirm</strong> says the order is final and may be billed. Before confirming, check for example that the customer signed the SPK/contract,
	    that quantities, prices and addresses are right, and that installation is scheduled. Confirming creates the Invoice and can't be repeated.
	  </p>

	  <h3 id="sp-rules">3. Rules to remember</h3>
	  <ul>
	    <li><strong>1 quotation = 1 business line.</strong> An opportunity with NHS and IPTV products gets two quotations, each with its own number series.</li>
	    <li><strong>Approved and Rejected quotations are final</strong>: their items can't be changed and a rejected one can't be approved. Changes go through <em>Revise Quotation</em>.</li>
	    <li><strong>Closed Won</strong> waits until no quotation of the opportunity is still Draft or Sent. Closed rows are highlighted in the Opportunities list
	        (<span class="badge badge-primary">Won</span> blue, <span class="badge badge-warning">Lost</span> yellow).</li>
	    <li>Quotation status colours: <span class="badge badge-secondary">Draft</span> <span class="badge badge-info">Sent</span>
	        <span class="badge badge-success">Approved</span> <span class="badge badge-danger">Rejected</span>; rows of rejected quotations are dimmed.</li>
	    <li>Sales users can see invoices and print their PDF; editing the Sales Order and invoice statuses is for the Sales Manager.</li>
	    <li>Every step is recorded in <strong>Log Activity</strong>, except Confirm SO for now (it writes straight to the database).</li>
	  </ul>
	</section>

<style>
	.lead-copy { cursor: pointer; }
	.lead-copy:hover { background: #fff8e6; }
	.lead-copy.copied { background: #e6f4ea; }
</style>
<?php
$this->registerJs(<<<JS
function leadCopyFallback(text) {
    var ta = $('<textarea>').val(text).css({position: 'fixed', top: 0, left: 0, opacity: 0}).appendTo('body');
    ta[0].select();
    try { document.execCommand('copy'); } catch (e) {}
    ta.remove();
}
$(document).on('click', '.lead-copy', function () {
    var cell = $(this), text = cell.text().trim();
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).catch(function () { leadCopyFallback(text); });
    } else {
        leadCopyFallback(text);
    }
    cell.addClass('copied');
    setTimeout(function () { cell.removeClass('copied'); }, 800);
});
JS);
?>

  </div>
</div>
