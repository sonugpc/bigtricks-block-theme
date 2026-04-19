#!/bin/bash
#
# Bigtricks Pre-Commit Validation Script
# Runs automated checks before code modifications
#

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

ERRORS=0
WARNINGS=0

echo "🔍 Running Bigtricks pre-commit checks..."
echo ""

# Get the theme directory
THEME_DIR="/Users/sonugpc/Local Sites/tst/app/public/wp-content/themes/bigtricks-block"
PHP_BIN="/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php"

# Function to check if file exists before testing
check_file_exists() {
    if [ ! -f "$1" ]; then
        return 1
    fi
    return 0
}

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# CHECK 1: PHP Syntax Validation
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo "📝 [1/6] Checking PHP syntax..."

PHP_FILES=$(find "$THEME_DIR" -name "*.php" -not -path "*/vendor/*" -not -path "*/node_modules/*" 2>/dev/null || true)

if [ -n "$PHP_FILES" ]; then
    PHP_ERRORS=0
    while IFS= read -r file; do
        if ! "$PHP_BIN" -l "$file" > /dev/null 2>&1; then
            echo -e "${RED}   ✗ PHP syntax error in: $(basename "$file")${NC}"
            "$PHP_BIN" -l "$file" 2>&1 | tail -n 3
            PHP_ERRORS=$((PHP_ERRORS + 1))
        fi
    done <<< "$PHP_FILES"
    
    if [ $PHP_ERRORS -eq 0 ]; then
        echo -e "${GREEN}   ✓ All PHP files valid${NC}"
    else
        echo -e "${RED}   ✗ Found $PHP_ERRORS PHP syntax errors${NC}"
        ERRORS=$((ERRORS + PHP_ERRORS))
    fi
else
    echo -e "${YELLOW}   ⚠ No PHP files found${NC}"
fi

echo ""

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# CHECK 2: Meta Field Type Safety
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo "🔒 [2/6] Checking meta field type safety..."

META_WARNINGS=0

if [ -n "$PHP_FILES" ]; then
    while IFS= read -r file; do
        # Look for get_post_meta/get_term_meta not wrapped with type casts
        # Using basic grep for macOS compatibility
        if grep -E "get_(post|term)_meta[^;]+;" "$file" | grep -v -E "(floatval|absint|intval|sanitize_|esc_)" > /dev/null 2>&1; then
            MATCHES=$(grep -n -E "get_(post|term)_meta[^;]+;" "$file" | grep -v -E "(floatval|absint|intval|sanitize_|esc_)" | head -n 3)
            if [ -n "$MATCHES" ]; then
                echo -e "${YELLOW}   ⚠ Potential unsafe meta in: $(basename "$file")${NC}"
                echo "$MATCHES" | while read -r line; do
                    echo "      $line"
                done
                META_WARNINGS=$((META_WARNINGS + 1))
            fi
        fi
    done <<< "$PHP_FILES"
    
    if [ $META_WARNINGS -eq 0 ]; then
        echo -e "${GREEN}   ✓ No obvious unsafe meta field usage${NC}"
    else
        echo -e "${YELLOW}   ⚠ Found $META_WARNINGS potential issues (review manually)${NC}"
        echo -e "${YELLOW}      Remember: floatval(), absint(), sanitize_text_field(), esc_url()${NC}"
        WARNINGS=$((WARNINGS + META_WARNINGS))
    fi
fi

echo ""

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# CHECK 3: AJAX Nonce Verification
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo "🔐 [3/6] Checking AJAX nonce verification..."

AJAX_MISSING_NONCE=0

if [ -n "$PHP_FILES" ]; then
    while IFS= read -r file; do
        # Check if file has AJAX actions
        if grep -q "add_action.*wp_ajax" "$file" 2>/dev/null; then
            # Get function names from AJAX actions
            AJAX_FUNCS=$(grep -oP "add_action\s*\(\s*'wp_ajax[^']*',\s*'\K[^']+" "$file" 2>/dev/null || true)
            
            if [ -n "$AJAX_FUNCS" ]; then
                while IFS= read -r func; do
                    # Check if function has nonce verification
                    if ! grep -q "check_ajax_referer" "$file" 2>/dev/null; then
                        echo -e "${RED}   ✗ Missing nonce check in AJAX handler: $func ($(basename "$file"))${NC}"
                        AJAX_MISSING_NONCE=$((AJAX_MISSING_NONCE + 1))
                    fi
                done <<< "$AJAX_FUNCS"
            fi
        fi
    done <<< "$PHP_FILES"
    
    if [ $AJAX_MISSING_NONCE -eq 0 ]; then
        echo -e "${GREEN}   ✓ AJAX handlers appear to have nonce checks${NC}"
    else
        echo -e "${RED}   ✗ Found $AJAX_MISSING_NONCE AJAX handlers without nonce verification${NC}"
        ERRORS=$((ERRORS + AJAX_MISSING_NONCE))
    fi
fi

echo ""

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# CHECK 4: Lucide Icons Re-initialization
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo "🎨 [4/6] Checking Lucide icon re-initialization after AJAX..."

JS_FILES=$(find "$THEME_DIR/assets/js" -name "*.js" 2>/dev/null || true)
MISSING_LUCIDE=0

if [ -n "$JS_FILES" ]; then
    while IFS= read -r file; do
        # Check if file has AJAX HTML injection
        if grep -q "insertAdjacentHTML\|innerHTML.*+=" "$file" 2>/dev/null; then
            # Check if it re-initializes Lucide after injection
            if ! grep -A 5 "insertAdjacentHTML\|innerHTML.*+=" "$file" | grep -q "lucide.createIcons" 2>/dev/null; then
                echo -e "${YELLOW}   ⚠ Possible missing lucide.createIcons() in: $(basename "$file")${NC}"
                MISSING_LUCIDE=$((MISSING_LUCIDE + 1))
            fi
        fi
    done <<< "$JS_FILES"
    
    if [ $MISSING_LUCIDE -eq 0 ]; then
        echo -e "${GREEN}   ✓ Lucide re-initialization looks good${NC}"
    else
        echo -e "${YELLOW}   ⚠ Found $MISSING_LUCIDE potential issues (review manually)${NC}"
        WARNINGS=$((WARNINGS + MISSING_LUCIDE))
    fi
else
    echo -e "${YELLOW}   ⚠ No JavaScript files found${NC}"
fi

echo ""

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# CHECK 5: Dark Mode Utilities
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo "🌙 [5/6] Checking dark mode utilities in new components..."

MISSING_DARK_MODE=0

if [ -n "$PHP_FILES" ]; then
    while IFS= read -r file; do
        # Look for new UI sections (divs/sections with classes) without dark: variants
        # This is a heuristic check
        if grep -q 'class="[^"]*bg-white' "$file" 2>/dev/null; then
            if ! grep -q 'dark:bg-' "$file" 2>/dev/null; then
                echo -e "${YELLOW}   ⚠ Possible missing dark mode in: $(basename "$file")${NC}"
                MISSING_DARK_MODE=$((MISSING_DARK_MODE + 1))
            fi
        fi
    done <<< "$PHP_FILES"
    
    if [ $MISSING_DARK_MODE -eq 0 ]; then
        echo -e "${GREEN}   ✓ Dark mode utilities appear consistent${NC}"
    else
        echo -e "${YELLOW}   ⚠ Found $MISSING_DARK_MODE files potentially missing dark: variants${NC}"
        WARNINGS=$((WARNINGS + MISSING_DARK_MODE))
    fi
fi

echo ""

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# CHECK 6: Output Escaping
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo "🛡️  [6/6] Checking output escaping..."

ESCAPING_WARNINGS=0

if [ -n "$PHP_FILES" ]; then
    while IFS= read -r file; do
        # Look for echo/print with variables not wrapped in esc_* functions
        # Using basic grep for macOS compatibility
        if grep -E "echo +\\$[a-zA-Z_]|print +\\$[a-zA-Z_]" "$file" | grep -v -E "esc_html|esc_attr|esc_url|wp_kses" > /dev/null 2>&1; then
            UNESCAPED_LINES=$(grep -n -E "echo +\\$[a-zA-Z_]|print +\\$[a-zA-Z_]" "$file" | grep -v -E "esc_html|esc_attr|esc_url|wp_kses" | head -n 3 || true)
            if [ -n "$UNESCAPED_LINES" ]; then
                echo -e "${YELLOW}   ⚠ Potential unescaped output in: $(basename "$file")${NC}"
                echo "$UNESCAPED_LINES" | while read -r line; do
                    echo "      $line"
                done
                ESCAPING_WARNINGS=$((ESCAPING_WARNINGS + 1))
            fi
        fi
    done <<< "$PHP_FILES"
    
    if [ $ESCAPING_WARNINGS -eq 0 ]; then
        echo -e "${GREEN}   ✓ Output escaping appears consistent${NC}"
    else
        echo -e "${YELLOW}   ⚠ Review output escaping in flagged files${NC}"
        WARNINGS=$((WARNINGS + ESCAPING_WARNINGS))
    fi
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Summary
if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✓ All checks passed!${NC}"
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠ Checks passed with $WARNINGS warnings (review recommended)${NC}"
    exit 0
else
    echo -e "${RED}✗ Found $ERRORS critical errors and $WARNINGS warnings${NC}"
    echo ""
    echo "Fix the errors before committing. See .github/copilot-instructions.md for patterns."
    exit 1
fi
