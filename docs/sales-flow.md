# Sales Flow

The Sales CRM pipeline from first contact to paid invoice. For each step: what the
user does, which code runs, and what gets created or changed automatically.

```
Lead ──convert──▶ Account + Contact + Opportunity
                                   │
                    Opportunity Products (amount rolls up)
                                   │
                              Quotation ──Sent──▶ Opportunity: Proposal (50%)
                                   │
                              Approved ──▶ Opportunity: Closed Won (100%)
                                   │        + Sales Order (Draft) + SO items
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
  - an **Account** (type `Prospect`, same owner and customer segment), with the lead's
    address as its Main Address and also as an **Office** entry in Account Addresses,
  - a primary **Contact**,
  - an **Opportunity** "Opportunity - {company}" at `Prospecting`, 10%, closing in 30 days,

  and marks the lead `is_converted = 1`. A converted lead cannot be converted again.

## 2. Account, Contact, Address

- **Account**: the customer company. `parent_account_id` groups subsidiaries under a
  holding. **Customer Type** (`account_type`) moves from Prospect to
  Customer/Partner/Reseller/Vendor by hand, nothing changes it automatically.
  **Customer Segment** (`customer_segment`) is the channel/segment (B2B2C (ISP), B2C,
  Hospitality, ...). **Sales Team** (`owner_user_id` → team) and **Assigned Sales**
  (`assigned_user_id` → user) are independent: either, both or neither can be set, and
  the assigned salesperson doesn't have to belong to the chosen team.
  `price_list_id` links a price list, but nothing uses it for pricing yet.
- **Contact**: people at the account. **Set primary** (`/sales/contact/setprimary`)
  calls `sp_set_primary_contact`, so there is at most one primary contact per account.
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
- **Opportunity Products**: products and quantities being discussed. `total` is a
  generated column (`qty*price - discount`), and triggers keep
  `opportunity.amount = SUM(total)` whenever a product line is added, changed or
  removed.
- Stage and probability also change automatically from the quotation (below).

## 4. Quotation

A formal offer to the account, linked to an opportunity. Status: Draft → Sent →
Approved / Rejected.

- **Quotation Items**: triggers compute each line's `total = qty*price - discount`
  and keep `quotation.total_amount = SUM(total)`.
- **Status → Sent** (trigger `trg_quotation_after_update_status`): the opportunity
  moves to stage **Proposal**, probability 50.
- **Approve** (`/sales/quotation/approve`, `QuotationController::actionApprove`):
  refuses if already approved or if the quotation has no items; otherwise it only
  sets `status = 'Approved'`. Trigger `trg_quotation_to_sales_order` then:
  - sets the opportunity to **Closed Won**, probability 100, amount = quotation total;
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

The `sales` role can create, edit and delete everything in this flow, including the
convert, approve, confirm and mark-sent/paid actions. Other roles see it read-only
through `viewApplication`. Details in [rbac.md](rbac.md).
