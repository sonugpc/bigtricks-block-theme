# Pre-Commit Hook Configuration

**Date:** April 17, 2026  
**Version:** 1.0.0

## Finalized Settings

### 1. Meta Field Type Safety - Option A
**Decision:** Keep simple regex pattern

**Pros:**
- Catches most unsafe meta field usage
- Simple and fast pattern matching
- Low maintenance

**Cons:**
- Some false positives for code like `(float) get_post_meta()`
- Requires manual review of warnings

**Pattern used:**
```bash
grep -E "get_(post|term)_meta[^;]+;" | grep -v -E "(floatval|absint|intval|sanitize_|esc_)"
```

**False positive examples:**
```php
// These trigger warnings but are actually safe:
$rating = (float) get_post_meta($id, 'rating', true);
$img = wp_kses_post((string) get_post_meta($id, 'image', true));
```

**Recommendation:** Review warnings manually, look for untyped usage patterns

---

### 2. Warning Strictness - Only Errors Block
**Decision:** Errors block commits, warnings inform

**Blocking errors:**
- ✗ PHP syntax errors
- ✗ AJAX handlers without nonce verification

**Non-blocking warnings:**
- ⚠️ Potential untyped meta fields
- ⚠️ Missing Lucide re-initialization
- ⚠️ Missing dark mode utilities
- ⚠️ Potential unescaped output

**Exit codes:**
- `0` = Success (no errors, warnings allowed)
- `1` = Failure (errors found, commit blocked)

**Rationale:** Allows development velocity while preventing critical security/functionality issues

---

### 3. Dark Mode Check - Enabled
**Decision:** Keep dark mode check active

**What it checks:**
```bash
# Looks for bg-white without dark:bg- variant
grep 'class="[^"]*bg-white' | grep -v 'dark:bg-'
```

**Known false positives:**
- Files with dark mode in different HTML sections
- Files using CSS variables instead of Tailwind utilities
- Template parts that inherit dark mode from parent

**Recommendation:** Use as informational reminder for new components

---

### 4. File Scope - All Files (Limitation)
**Decision:** Check all theme files (modified-only not supported)

**Current behavior:**
```bash
# Scans entire theme directory:
find "$THEME_DIR" -name "*.php" -not -path "*/vendor/*"
find "$THEME_DIR/assets/js" -name "*.js"
```

**Limitation:** VS Code Copilot's PreToolUse hook doesn't expose target file paths to scripts

**Performance impact:**
- ~20 PHP files: ~2-3 seconds
- ~5 JS files: <1 second
- **Total:** ~3-4 seconds per hook execution

**Future enhancement:** If Copilot adds `${modifiedFiles}` variable support:
```json
{
  "type": "runScript",
  "script": ".github/hooks/scripts/pre-commit-check.sh",
  "args": ["${modifiedFiles}"]
}
```

**Manual workaround:**
```bash
# Check specific files only:
export CHECK_FILES="single-deal.php functions.php"
.github/hooks/scripts/pre-commit-check.sh
```

---

## Performance Metrics

Based on current theme (20 PHP, 1 JS file):

| Check | Time | Blocking |
|-------|------|----------|
| PHP syntax | ~2s | Yes |
| Meta field safety | ~0.5s | No |
| AJAX nonce | ~0.3s | Yes |
| Lucide re-init | ~0.2s | No |
| Dark mode | ~0.3s | No |
| Output escaping | ~0.3s | No |
| **Total** | **~3.6s** | - |

**Optimization opportunities:**
- ✅ Parallel grep commands (not needed for current size)
- ✅ Cache PHP syntax results (minimal gain)
- ✅ Skip vendor/node_modules (already implemented)

---

## Maintenance

### When to Update

**Add new checks when:**
- New security vulnerabilities discovered
- New theme patterns established
- Plugin integration requires new validation

**Remove checks when:**
- False positive rate >50%
- Pattern no longer applicable
- Replaced by better tooling (e.g., PHPStan)

### Related Customizations

**Suggested companion hooks:**

1. **Post-commit hook** - Documentation generation
2. **Session start hook** - Environment validation
3. **Pre-deploy hook** - Production readiness

**Suggested agents:**

1. **Security agent** - Deep security analysis
2. **Performance agent** - Query optimization
3. **Template agent** - Component generation

---

## Rollback Instructions

**To disable the hook entirely:**
```bash
# Rename or delete:
mv .github/hooks/pre-commit.json .github/hooks/pre-commit.json.disabled
```

**To disable specific checks:**
```bash
# Edit pre-commit-check.sh and comment out:
# echo "🔒 [2/6] Checking meta field type safety..."
# ... (entire check section)
```

**To restore strict mode (block on warnings):**
```bash
# In pre-commit-check.sh, line ~170:
elif [ $ERRORS -eq 0 ]; then
    echo -e "${RED}✗ Found $WARNINGS warnings${NC}"
    exit 1  # Add this to block
```

---

**Approved by:** User (April 17, 2026)  
**Review date:** Quarterly (next: July 17, 2026)
