# Sales Flow

The Sales CRM pipeline from first contact to paid invoice. For each step: what the
user does, which code runs, and what gets created or changed automatically.

```
Lead ──convert──▶ Account + Contact + Opportunity
                                   │
                    Opportunity Products (amount rolls up)
                                   │
                    Quotation(s), one per business line ──Sent──▶ Opportunity: Proposal (50%)
                                   │
                              Approved ──▶ Sales Order (Draft) + SO items
                                   │        (Opportunity: Closed Won once no
                                   │         quotation is still Draft/Sent)
                                   ▼
                             Sales Order ──Confirm──▶ Invoice (Draft) + invoice items
                                                        │
                                                   Mark Sent ──▶ Mark Paid
```

Activities (calls, meetings, emails, tasks, notes) can be logged against an account,
contact or opportunity at any point.

## 1. Lead

A prospect not yet in the customer base: company name, contact person, source,
industry, optional customer segment, location, owner (a team).

- **Owner change** is recorded in `record_owner_history` by trigger
  `trg_lead_owner_update`.
- **Convert** (`/sales/lead/convert`, the ⇄ button on the Lead grid) calls stored
  procedure `sp_convert_lead_to_customer`, which in one transaction creates:
  - an **Account** (type `Prospect`, same owner, customer segment and description), with the lead's
    address as its Main Address and also as **Billing, Shipping and Office** entries in
    Account Addresses (edit the ones that differ),
  - a primary **Contact**,
  - an **Opportunity** "Opportunity - {company}" carrying the lead's description, at
    `Prospecting`, 10%, closing in 30 days,

  and marks the lead `is_converted = 1`. A converted lead cannot be converted again,
  and cannot be deleted (kept as history; its delete button is disabled). Deleting a
  lead never touches the Account/Contact/Opportunity created from it.

## 2. Account, Contact, Address

- **Account**: the customer company. `parent_account_id` groups subsidiaries under a
  holding. **Customer Type** (`account_type`) moves from Prospect to
  Customer/Partner/Reseller/Vendor by hand, nothing changes it automatically.
  **Customer Segment** (`customer_segment`) is the channel/segment (B2B2C (ISP), B2C,
  Hospitality, ...). **Sales Team** (`owner_user_id` → team) and **Assigned Sales**
  (`assigned_user_id` → user): the Assigned Sales list only offers members of the
  chosen Sales Team (refreshed from `master/lookup/team-members` when the team
  changes), and the model rejects a salesperson from another team.
  `price_list_id` links a price list, but nothing uses it for pricing yet.
- **Contact**: people at the account. **Set primary** (`/sales/contact/setprimary`)
  calls `sp_set_primary_contact`, so there is at most one primary contact per account.
- **Deleting an account** (Sales Manager) first deletes its *untouched*
  opportunities (still Prospecting, no products, quotations or activities, like the
  one Convert creates; stage history goes with them), then contacts, addresses and
  documents (files too), then the account, in one transaction
  (`Account::deleteWithDependents()`). It is refused, with the reason, while the
  account still has an opportunity in progress or closed, quotations, sales
  orders, invoices or activities (also ones pointing at its contacts).
- **Account Address**: extra addresses of type Billing, Shipping or Office.
- **Documents**: files attached on the Account detail page (Kartik FileInput, several
  files at once, max 10 MB each by default; pdf, office files, images, csv/txt,
  zip/rar). The real limit is the smallest of `params['accountAttachmentMaxSize']`
  (10 MB), PHP `upload_max_filesize` / `post_max_size`, and nginx
  `client_max_body_size` (nginx default 1 MB; locally PHP allows 2 MB). A file over
  the limit is rejected with an error in the widget. Downloads go through `/sales/account/downloadattachment`, so they are
  access-checked: roles with `viewApplication` can download, only roles with write
  access to accounts can upload or delete.

## 3. Opportunity

A potential deal with an account: stage, amount, close date, probability.

- **Stages**: Prospecting → Qualification → Proposal → Negotiation → Closed Won /
  Closed Lost. Every stage change (including the initial one) is written to
  `opportunity_stage_history` by triggers, and the Stage History menu shows it.
- **Amount**: an estimate typed in while the opportunity has no products; once it
  has products the field is locked in the form ("Calculated from products") and the
  model rejects manual changes, because the triggers keep it equal to the products'
  total.
- **Opportunity Products**: products and quantities being discussed. `total` is a
  generated column (`qty*price - discount`), and triggers keep
  `opportunity.amount = SUM(total)` whenever a product line is added, changed or
  removed.
- Stage and probability also change automatically from the quotation (below).

## 4. Quotation

A formal offer to the account, linked to an opportunity. Status: Draft → Sent →
Approved / Rejected.

- **Business line** (`quotation.business_line`): every quotation belongs to one
  line — **NHS** (NextSys Hospitality), **NXG** (NextGO), **IPTV** (Vision+), or any
  later code. The line comes from the products (`product.business_line`, set in
  Product & Pricing → Products). **1 quotation = 1 line**: a quotation item must be a
  product of the quotation's line (`QuotationItem` rule — "This quotation is for NHS;
  X is IPTV and needs its own quotation"; a product without a line can't be quoted).
  An opportunity may mix lines; it then gets one quotation per line. The line is
  chosen when the quotation is created and can't be changed afterwards (it is part
  of the number).
- **Number**: `001/SPH/SLS-NHS/EXT/IX/2026` — 3-digit running number **per business
  line per year** (NHS, NXG and IPTV each count from 001 and restart in January; it
  grows past 999 by itself), then the template code with `{LINE}` replaced by the
  line (`SPH/SLS-{LINE}/EXT` in Master Data → Layout Quotation), Roman month and
  year of the quotation date (`Quotation::nextSphNumber`). `ux_quotation_number`
  keeps numbers unique. Older `QTN/...` / `QUO/...` numbers are left as they are and
  ignored by the counter; the earlier 4-digit `0001/...` numbers still count.
- **SPH PDF** (button *SPH (PDF)* on the quotation view, `actionPdf`, mPDF): the
  "Proposal Penawaran Harga" letter — letterhead logo and coloured footer on every
  page, recipient, opening text, one block per recurring item (Harga Paket, Jumlah
  Unit, Diskon, Total), a subscription total when there are several, non-recurring
  items (e.g. an installation-fee product) as their own rows, contract duration,
  payment method, terms, installation notes, closing text and signatures (Assigned
  Sales name + job title / account).
- **Template**: Master Data → *Layout Quotation* (`quotation_layout`, one row).
  New quotations copy its opening / terms / installation notes / closing texts and
  default contract length, so an issued SPH never changes when the template does;
  the copies can be edited per quotation. In these texts `**...**` prints bold
  (`QuotationLayout::inline`); everything else is escaped. Payment method defaults from the
  recurring products' revenue model (Bulanan / Tahunan (di depan)).
- **Signer** ("Diajukan Oleh"): the account's **Assigned Sales** (name + user
  *Job Title*). A name typed on the quotation overrides it; the Layout Quotation
  signer is only a fallback for accounts without Assigned Sales (it is not copied
  into quotations).
- Product *Package Info* (e.g. "101 Channel Terlampir") is printed on the SPH.

- **Create Quotation** (button on the opportunity view,
  `QuotationController::actionCreateFromOpportunity` → `Opportunity::createQuotations()`):
  makes one Draft quotation **per business line** of the opportunity's active
  products (all in one transaction), each for the opportunity's account, dated
  today, valid 30 days, numbered in its line, with one item per product of that line
  (qty, price, discount). One quotation → it opens; several → the opportunity view
  shows "N quotations created, one per business line: …". Lines that already have an
  approved quotation are skipped. Refused (`Opportunity::quotationBlocker()`, shown
  next to the disabled button) while the opportunity has no products, a product has
  no business line, or every line is already approved; the button is hidden on
  Closed Won / Closed Lost opportunities. The opportunity view lists its quotations.
- **Revise** = the same button. When a line being quoted still has a Draft/Sent
  quotation, the button reads **Revise Quotation** and its confirm dialog lists
  those quotations (`Opportunity::quotationsToReplace()`). Clicking it creates the
  new quotation(s) first and then sets the old ones to **Rejected** in the same
  transaction (flash: "Replaced (set to Rejected): 001/…"). Workflow for a
  revision: change the Opportunity Products → click Revise Quotation. Don't reject
  the old quotation by hand first: with no Draft/Sent quotation left the
  opportunity closes as Closed Lost (see Approve below) and the button disappears.
  A Draft that was never sent can also simply be edited in place (same number).
- Creating a quotation moves the opportunity to Proposal and
  raises its probability to 50% if it was lower (`Quotation::setOpportunityProposal`).

- **Quotation Items**: triggers compute each line's `total = qty*price - discount`
  and keep `quotation.total_amount = SUM(total)`.
- **Status → Sent** (trigger `trg_quotation_after_update_status`): the opportunity
  moves to stage **Proposal**, probability 50.
- **Approve** (`/sales/quotation/approve`, `QuotationController::actionApprove`):
  refuses if already approved or if the quotation has no items; otherwise it only
  sets `status = 'Approved'`. Trigger `trg_quotation_to_sales_order` then:
  - closes the opportunity **only when none of its quotations is still Draft or
    Sent**: **Closed Won** (probability 100, amount = sum of its *approved*
    quotations) if at least one was approved, otherwise **Closed Lost**
    (probability 0). The same check runs when a quotation is set to Rejected. So an
    opportunity with an NHS and an IPTV quotation is Closed Won after both are
    decided, with the total of the approved ones. (`Quotation::handleApproved` /
    `handleRejected` repeat this in PHP via `Opportunity::syncStageFromQuotations()`.)
  - if no Sales Order exists for this quotation, creates one (`SO/YYYYMMDD/NNNN`,
    status Draft, same account and total). Trigger `trg_so_copy_items` on the new SO
    copies the quotation items into it.

  The controller then redirects to the new Sales Order.

## 5. Sales Order

The confirmed order. Status: Draft → Confirmed → Completed / Cancelled.

- **SO Items**: trigger computes `total = qty*price - discount` on insert/update.
- **Confirm** (`/sales/salesorder/confirm`, `SalesorderController::actionConfirm`),
  in PHP inside a transaction:
  - locks the SO row; if an invoice already exists for it, redirects there instead;
  - sets SO `status = 'Confirmed'`;
  - creates an **Invoice** (`generateInvoiceNumber()`, invoice date today, due in 30
    days, status Draft, `total_amount` = SO total);
  - copies every SO item into `invoice_item`;
  - redirects to the invoice.

## 6. Invoice

Status: Draft → Sent → Paid. `Overdue` can only be picked by hand in the edit form;
nothing marks invoices overdue when `due_date` passes.

- **Mark Sent** (`/sales/invoice/mark-sent`) works only from Draft.
- **Mark Paid** (`/sales/invoice/mark-paid`) works only from Sent.
- **PDF** (`/sales/invoice/pdf`) renders the printable invoice.

There are no triggers on invoice tables, so editing invoice items does not update
`invoice.total_amount`.

## Where prices come from

Prices on opportunity products and quotation items are **typed in by the user**. The
Product & Pricing module (complete) stores price lists, date-ranged product prices,
discounts and bundle definitions, but no sales screen looks them up yet. See
[known-issues.md](known-issues.md#6-sales-screens-dont-use-the-pricing-data-yet).

## Who can do what

| | Sales | Sales Manager |
|---|:-:|:-:|
| Lead, Account, Contact, Address, Activity: create / edit | ✅ | ✅ |
| Convert lead | ✅ own team's leads | ✅ all teams |
| Edit an existing lead | ✅ own team's leads | ✅ all teams |
| Opportunity + products, Quotation + items (up to Sent) | ✅ | ✅ |
| Remove opportunity products / quotation items | ✅ | ✅ |
| Change Sales Team / Assigned Sales on existing records | ❌ | ✅ |
| Approve quotation (creates the Sales Order; Closed Won once all quotations are decided) | ❌ | ✅ |
| Edit Sales Order, Confirm SO (creates the Invoice) | ❌ | ✅ |
| Edit invoice, Mark Sent / Paid | ❌ (PDF only) | ✅ |
| Delete leads, accounts, contacts, opportunities, quotations | ❌ | ✅ |

Other roles see the Sales menu read-only through `viewApplication`. Details in
[rbac.md](rbac.md).
