# Bigtricks Custom Blocks

This directory contains custom Gutenberg blocks for the Bigtricks theme.

## Available Blocks

### 1. Contact Form Block (`bigtricks/contact-form`)

A customizable contact form that sends email to the WordPress admin email.

**Fields:**

- Name (required)
- Email (required)
- WhatsApp Number (optional)
- Message (required)

**Usage in Block Editor:**

1. Click the (+) button to add a block
2. Search for "Contact Form"
3. Configure settings in the right sidebar:
   - Toggle title/description visibility
   - Customize form title, description, button text, and success message

**Shortcode Usage:**

```
[bigtricks_contact_form]

<!-- With custom attributes -->
[bigtricks_contact_form title="Contact Us" description="We'd love to hear from you!" button_text="Get in Touch"]
```

**Block Customization:**

- Supports spacing (margin/padding)
- Supports color (background, text, gradients)
- Supports typography (font size, line height)
- Supports alignment (wide, full)

---

### 2. Advertise Form Block (`bigtricks/advertise-form`)

An advertising inquiry form for potential sponsors and partners.

**Fields:**

- **Contact Information:**
  - Name (required)
  - Email (required)
  - Phone Number (required)
  - WhatsApp Number (optional)

- **Business Information:**
  - Company Name (required)
  - Company Website (optional)

- **Advertising Requirements:**
  - Type of Advertisement (required dropdown): Banner Ads, Sponsored Posts, Telegram Post, Product Review, Affiliate Partnership, Newsletter Sponsorship, Other
  - Monthly Budget Range (optional dropdown)
  - Campaign Duration (optional dropdown)
  - Additional Details (required)

**Usage in Block Editor:**

1. Click the (+) button to add a block
2. Search for "Advertise Form"
3. Configure settings in the right sidebar

**Shortcode Usage:**

```
[bigtricks_advertise_form]

<!-- With custom attributes -->
[bigtricks_advertise_form title="Partner With Us" button_text="Send Inquiry"]
```

**Block Customization:**

- Same customization options as Contact Form

---

## Technical Details

### File Structure

Each block follows this structure:

```
inc/blocks/{block-name}/
├── block.json          # Block metadata and configuration
├── render.php          # Server-side rendering (PHP template)
└── editor.js           # Block editor UI (React/JSX)
```

### Email Delivery

Both forms use WordPress `wp_mail()` function to send emails to the admin email address set in **Settings → General**.

**Email Format:**

- **Subject:** `[Site Name] New {Form Type} from {Name/Company}`
- **From:** Site admin email
- **Reply-To:** User's submitted email
- **Content:** Plain text with all submitted fields

**Testing Email:**

- Install an SMTP plugin like WP Mail SMTP for reliable delivery
- Check spam folder if emails aren't arriving
- Verify admin email in WordPress settings

### AJAX Submission

Forms submit via AJAX to `/wp-admin/admin-ajax.php` with the following actions:

- `bigtricks_submit_form` (both logged-in and guest users)

**Security:**

- Nonce verification on all submissions
- Input sanitization (sanitize_text_field, sanitize_email, sanitize_textarea_field, esc_url_raw)
- Output escaping in templates

### Form Validation

**Client-side:**

- HTML5 required attributes
- Email validation (`type="email"`)
- Phone pattern validation (`pattern="[0-9+\-\s]+"`)

**Server-side:**

- Required field checks
- Email format validation (`is_email()`)
- Form type validation (whitelist)
- Nonce verification

### Styling

Forms use Tailwind CSS classes with dark mode support:

- Light mode: `bg-white`, `text-gray-900`, `border-gray-200`
- Dark mode: `dark:bg-gray-800`, `dark:text-white`, `dark:border-gray-700`

Icons powered by Lucide Icons (loaded via CDN).

### Frontend JavaScript

Form handling is in `assets/js/forms.js`:

- AJAX submission with Fetch API
- Loading state management
- Success/error message display
- Form reset on success
- Lucide icon re-initialization after dynamic content

### Dependencies

**Required:**

- WordPress 6.0+
- PHP 8.0+
- Tailwind CSS (loaded in theme)
- Lucide Icons (loaded in theme)

**No external form plugins required** - everything is self-contained in the theme.

---

## Customization Examples

### Change Email Recipient

Edit `functions.php` and modify the email handlers:

```php
// In bigtricks_handle_contact_form() or bigtricks_handle_advertise_form()
// Change this line:
$admin_email = get_option( 'admin_email' );

// To this:
$admin_email = 'custom@example.com';
```

### Add CC/BCC Recipients

```php
$headers = [
    'Content-Type: text/plain; charset=UTF-8',
    "Reply-To: {$name} <{$email}>",
    'Cc: sales@example.com',
    'Bcc: marketing@example.com',
];
```

### Add Custom Fields

1. **Update block render file** (`render.php`):

   ```php
   <input type="text" name="custom_field" class="..." placeholder="Custom Field">
   ```

2. **Update AJAX handler** in `functions.php`:

   ```php
   $custom_field = isset( $_POST['custom_field'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_field'] ) ) : '';
   $email_body .= "Custom Field: {$custom_field}\n";
   ```

3. **Update shortcode** (if using):
   Add the same field markup in the shortcode function

### Customize Email Template

Edit the `$email_body` variable in the handler functions:

```php
// HTML email (change headers first)
$headers = ['Content-Type: text/html; charset=UTF-8'];

$email_body = '<html><body>';
$email_body .= '<h2>New Contact Form Submission</h2>';
$email_body .= '<p><strong>Name:</strong> ' . esc_html( $name ) . '</p>';
// ... more fields
$email_body .= '</body></html>';
```

---

## Troubleshooting

### Emails Not Sending

1. Check WordPress admin email in **Settings → General**
2. Install WP Mail SMTP plugin
3. Check server mail logs
4. Verify PHP `mail()` function works on your server
5. Check spam/junk folders

### Form Not Submitting

1. Open browser console (F12) and check for JavaScript errors
2. Verify `bigtricksData.ajaxUrl` is defined (check page source)
3. Check Network tab for failed AJAX requests
4. Enable WordPress debug mode: `define('WP_DEBUG', true);`

### Block Not Appearing in Editor

1. Clear browser cache
2. Verify block registration in functions.php
3. Check `block.json` files are valid JSON
4. Ensure `inc/blocks/` directory permissions are correct

### Dark Mode Issues

Forms should automatically adapt to dark mode. If not:

1. Verify `dark:` classes are in templates
2. Check Tailwind dark mode config in `functions.php`
3. Ensure `<html>` has class `dark` when dark mode is active

---

## Performance Notes

- Forms use native WordPress functionality (no external dependencies)
- AJAX submission prevents page reload
- Minimal JavaScript footprint (~1KB gzipped)
- No database writes (emails only)
- Blocks are server-rendered (no client-side rendering overhead)

---

## Security Best Practices

✅ **Implemented:**

- Nonce verification on all submissions
- Input sanitization on server-side
- Output escaping in templates
- Email validation
- CSRF protection via nonces
- Rate limiting via WordPress (future: add custom rate limiting)

⚠️ **Recommended Additions:**

- Install a spam protection plugin (Akismet, reCAPTCHA)
- Add honeypot fields for bot detection
- Implement rate limiting (max X submissions per IP per hour)
- Log failed submissions for security monitoring

---

## Future Enhancements

Potential improvements:

- [ ] Add reCAPTCHA integration
- [ ] Store submissions in database (optional)
- [ ] Add export to CSV functionality
- [ ] Email notifications to user (confirmation emails)
- [ ] Conditional fields based on dropdown selections
- [ ] Multi-step form wizard
- [ ] File upload support
- [ ] Integration with CRM systems
- [ ] A/B testing variants

---

**Last Updated:** April 19, 2026  
**Maintained By:** Bigtricks Theme Development Team
