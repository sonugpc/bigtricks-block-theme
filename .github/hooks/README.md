# Pre-Commit Hook & Release Workflow for Bigtricks Theme

Automated validation that runs before file modifications to enforce code quality, security, and theme-specific patterns, plus a one-command release script.

---

## Release Workflow

### One-command release

```bash
# Patch bump  1.0.4 → 1.0.5 (default)
./release.sh
npm run release

# Minor bump  1.0.4 → 1.1.0
./release.sh minor
npm run release:minor

# Major bump  1.0.4 → 2.0.0
./release.sh major
npm run release:major

# Explicit version
./release.sh 1.2.3
```

### What release.sh does (in order)

| Step | Action                                                                    |
| ---- | ------------------------------------------------------------------------- |
| 0    | Resolve target version from `style.css`                                   |
| 1    | Pre-flight: branch guard (`main` only), git/node/PHP check                |
| 2    | **Quality checks** — re-runs `pre-commit-check.sh` (all 6 checks)         |
| 3    | **Tailwind CSS** production build → `assets/css/bigtricks-tailwind.css`   |
| 4    | **Lucide icon bundle** → `assets/js/lucide-custom.js`                     |
| 5    | Bump `Version:` in `style.css` and `BIGTRICKS_VERSION` in `functions.php` |
| 6    | `git commit` with message `chore: release vX.Y.Z`                         |
| 7    | `git push origin main` → triggers GitHub Actions                          |

GitHub Actions ([`.github/workflows/release.yml`](../workflows/release.yml)) then:

1. Detects the new `Version:` in `style.css`
2. Runs `npm run build` again (clean CI environment)
3. Packages a production ZIP (no dev files)
4. Publishes a GitHub Release tagged `vX.Y.Z`

WordPress sites with `BIGTRICKS_GITHUB_OWNER` / `BIGTRICKS_GITHUB_REPO` defined will see the standard **"Update Available"** notice in **Appearance → Themes** within 12 hours (or immediately after flushing transients).

---

## Configuration

**Finalized Settings:**

- ✅ Meta field check: Simple regex (some false positives, catches most issues)
- ✅ Strictness: Only errors block commits (warnings inform)
- ✅ Dark mode check: Enabled (informational reminders)
- ✅ Scope: All theme files (per-file filtering not supported by hook system)

## What It Checks

### 1. **PHP Syntax Validation** (blocking)

- Runs `php -l` on all PHP files
- **Fails commit if:** Any PHP syntax errors found
- **Why:** Prevents broken code from entering codebase

### 2. **Meta Field Type Safety** (warning)

- Detects raw `get_post_meta()` calls without type casting
- **Warns if:** Meta values retrieved without `floatval()`, `absint()`, `sanitize_text_field()`, etc.
- **Why:** Prevents type errors and improves security
- **Note:** May have false positives for code like `(float) get_post_meta()` - review warnings manually

### 3. **AJAX Nonce Verification** (blocking)

- Scans for AJAX handlers missing `check_ajax_referer()`
- **Fails commit if:** AJAX action registered without nonce check
- **Why:** Critical security vulnerability if missing

### 4. **Lucide Icons Re-initialization** (warning)

- Checks if AJAX HTML injection is followed by `lucide.createIcons()`
- **Warns if:** `insertAdjacentHTML()` without icon re-init
- **Why:** Icons won't render in dynamically loaded content

### 5. **Dark Mode Utilities** (warning)

- Detects `bg-white` without corresponding `dark:bg-*` variant
- **Warns if:** New UI components missing dark mode support
- **Why:** Maintains UI consistency across themes

### 6. **Output Escaping** (warning)

- Flags `echo $var` without `esc_html()`, `esc_attr()`, `esc_url()`
- **Warns if:** Potential unescaped output detected
- **Why:** XSS vulnerability prevention

## How It Works

### Automatic Triggering

The hook automatically runs when AI agents use these tools:

- `replace_string_in_file`
- `multi_replace_string_in_file`
- `create_file`

For files matching:

- `**/*.php`
- `**/assets/js/**/*.js`

### Manual Testing

You can run the validation script manually anytime:

```bash
cd /Users/sonugpc/Local\ Sites/tst/app/public/wp-content/themes/bigtricks-block
.github/hooks/scripts/pre-commit-check.sh
```

### Exit Codes

- `0` = All checks passed (or only warnings)
- `1` = Critical errors found (blocks commit)

## Configuration

### Hook Definition

Location: `.github/hooks/pre-commit.json`

```json
{
  "event": "PreToolUse",
  "conditions": {
    "tools": [
      "replace_string_in_file",
      "multi_replace_string_in_file",
      "create_file"
    ],
    "filePatterns": ["**/*.php", "**/assets/js/**/*.js"]
  },
  "actions": [
    {
      "type": "runScript",
      "script": ".github/hooks/scripts/pre-commit-check.sh",
      "blocking": true
    },
    {
      "type": "injectContext",
      "context": "CRITICAL THEME PATTERNS TO ENFORCE..."
    }
  ]
}
```

### Script Location

Location: `.github/hooks/scripts/pre-commit-check.sh`

## Customization

### Adjusting Strictness

**Current setting:** Only errors block commits (warnings inform but allow)

**To make warnings blocking:**

```bash
# In pre-commit-check.sh, change final summary section:
if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    # ... existing success code
elif [ $ERRORS -eq 0 ]; then
    echo -e "${RED}✗ Found $WARNINGS warnings - blocking commit${NC}"
    exit 1  # Change this to block on warnings
```

**Disable specific checks:**

```bash
# Comment out unwanted check sections (e.g., CHECK 4)
```

**Add new checks:**

```bash
# Add new section after CHECK 6
echo "🔍 [7/6] Checking custom pattern..."
```

###

### File Scope Limitation

**Current behavior:** Checks all PHP/JS files in theme directory

**Why:** VS Code Copilot's hook system doesn't currently support passing modified file paths to scripts. The PreToolUse event triggers before modifications but doesn't expose file targets to the script.

**Workaround for manual use:**

```bash
# Check specific files only:
PHP_FILES="file1.php file2.php" .github/hooks/scripts/pre-commit-check.sh
```

**Future improvement:** If Copilot adds file path injection support, update hook JSON:

````json
{
  "type": "runScript",
  "args": ["${modifiedFiles}"]
}
``` Environment-Specific Paths

If you deploy to different environments, update these variables:
```bash
THEME_DIR="/path/to/your/theme"
PHP_BIN="/path/to/php"
````

## Testing the Hook

### Test 1: PHP Syntax Error

Create a file with syntax error:

```php
<?php
function test( {  // Missing parameter name
```

**Expected:** ✗ PHP syntax error detected, commit blocked

### Test 2: Unsafe Meta Field

Add unsafe meta retrieval:

```php
$price = get_post_meta($id, '_btdeals_offer_sale_price', true);
```

**Expected:** ⚠ Warning about missing type cast

### Test 3: Missing AJAX Nonce

Add AJAX handler without nonce:

```php
add_action('wp_ajax_test', 'test_handler');
function test_handler() {
    // Missing: check_ajax_referer('test_nonce', 'nonce');
    wp_send_json(['status' => 'ok']);
}
```

**Expected:** ✗ AJAX handler without nonce check, commit blocked

### Test 4: Missing Lucide Re-init

Add AJAX HTML injection without icon refresh:

```javascript
container.insertAdjacentHTML("beforeend", html);
// Missing: lucide.createIcons();
```

**Expected:** ⚠ Warning about missing Lucide re-initialization

### Test 5: Clean Commit

Make a change that passes all checks:

```php
$price = floatval(get_post_meta($id, '_btdeals_offer_sale_price', true));
echo '<div class="price">' . esc_html($price) . '</div>';
```

**Expected:** ✓ All checks passed

## Integration with AI Agents

### Context Injection

The hook automatically injects critical patterns into agent context before modifications:

- Meta field type safety rules
- AJAX security requirements
- Output escaping guidelines
- Lucide icon re-initialization
- Dark mode utilities

### Blocking Behavior

If the script exits with code 1:

- AI agent receives error message
- File modification is blocked
- Agent must fix issues before retrying

### Warning Behavior

If the script exits with code 0 but has warnings:

- Agent receives warning messages
- File modification proceeds
- Agent should review warnings and fix proactively

## Troubleshooting

### Hook Not Running

1. Check VS Code Copilot settings for hook enablement
2. Verify JSON syntax in `pre-commit.json`
3. Check file pattern matches your target files

### Script Execution Errors

```bash
# Make executable if needed:
chmod +x .github/hooks/scripts/pre-commit-check.sh

# Test script manually:
bash -x .github/hooks/scripts/pre-commit-check.sh
```

### False Positives

Some checks use heuristics and may flag valid code:

- Review warnings manually
- Adjust regex patterns in script if needed
- Add exclusion patterns for known safe code

## Related Documentation

- [Workspace Instructions](../.github/copilot-instructions.md) - Theme patterns and conventions
- [PLAN.md](../../PLAN.md) - Complete architecture reference
- [Testing Checklist](../.github/copilot-instructions.md#testing-checklist) - Manual verification steps

## Next Steps

After confirming this hook works well, consider creating:

- **Post-commit hook** - Update documentation, run tests
- **Session start hook** - Load project context, check dependencies
- **Pre-deploy hook** - Production readiness checks (minification, CDN validation)

---

**Version:** 1.0.0  
**Last Updated:** April 17, 2026  
**Maintained By:** Bigtricks Theme Team
