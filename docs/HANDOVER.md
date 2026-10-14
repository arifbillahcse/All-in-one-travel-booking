# TravelOrio – Owner's guide

Admin panel: **https://your-domain/admin** (sign in with the email/password you were given).

## Daily
- **Inquiries** (Sales → Inquiries): every booking request and contact message lands here, even if the customer never finished the WhatsApp message. Change the status (New → Contacted → Confirmed or Cancelled) and add notes. A new one also emails the notify address.
- **Reviews** (Sales → Reviews): new reviews wait for approval. Switch on *Approved* to publish.

## Content (Content menu)
- **Destinations / Packages / Add-ons / Blog posts**: every text field has **English** and **বাংলা** tabs. Fill both; if Bangla is empty the English text is shown.
- **Photos:** upload JPG/PNG up to 10 MB; the site makes small WebP versions automatically. Give each photo a short description (helps Google and screen readers).
- **Add a destination:** Destinations → New → name, slug (the URL part, lowercase, no spaces), photos, highlights, best time, how to get there → Save. It appears in the menu and sitemap within a minute.
- **Add a blog post:** Blog posts → New → title, category, cover photo, body → set *Published* and a date.
- Hide something without deleting: untick *Active/Published*.

## Settings (Settings menu)
- **Site settings:** WhatsApp number (digits only with country code, e.g. 8801779440297), phone, email, Facebook/Instagram/YouTube links, notification email. Changes apply immediately.
- **Team:** add editors (can edit content) or owners (can also manage users).

## Backups
- Automatic every night at 02:30; the last 14 are kept on the server and (if configured) copied off-server.
- Ask your developer to restore: `php artisan travelorio:restore`. Photos and content come back together.

## If something looks wrong
1. Hard-refresh (Ctrl+Shift+R). 2. Check the admin panel loads. 3. Send your developer the time, the page URL and a screenshot. Technical health check: `php artisan travelorio:preflight`.

## Important facts
- Bookings are *requests*, confirmed manually over WhatsApp; there is no online payment.
- Old links like `/packages.html` redirect to the new addresses, so existing Google results keep working.
- Keep the server `.env` file and the database backups safe – they contain all secrets and customer phone numbers/emails. Use strong, unique admin passwords.

## Company profile PDF (for buyers and partners)
- Preview in a browser: `/company-profile` (English, hidden from search engines). It is built from the live site: published destinations, packages, add-ons and approved five-star reviews.
- Make the PDF: `node tools/build-company-profile.mjs` (writes `docs/TravelOrio-Company-Profile.pdf`; `--url https://your-domain` builds from the live site).
- Optional corporate lines (registration number, trade licence, TIN) appear in "Corporate details" once they are set in Site settings; nothing is printed for empty fields.
- Check the figures (5,000+ travelers, 4.9 rating) are true before sending the PDF to a buyer.

## Sharing data
- Safe to share: public marketing content (destinations, prices, packages) and business facts the owner supplied for this purpose.
- Needs written permission first: client logos, photos and testimonials from third parties, and anything marked confidential.
- Never share without the person's consent: inquiry details (names, phone numbers, emails) and booking records. The website promises "We never share your details".
