#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────────────────
# Bigtricks Theme — Release Script
#
# Usage:
#   ./release.sh                  # bump patch (1.0.4 → 1.0.5)
#   ./release.sh minor            # bump minor (1.0.4 → 1.1.0)
#   ./release.sh major            # bump major (1.0.4 → 2.0.0)
#   ./release.sh 1.2.3            # explicit version
#
# What it does, in order:
#   1.  Pre-flight: clean working tree check, branch guard
#   2.  Pre-commit quality checks  (reusing pre-commit-check.sh)
#   3.  Build: Tailwind CSS (production/minified)
#   4.  Build: Lucide icon bundle
#   5.  Bump version in style.css and functions.php
#   6.  Stage & commit all changes
#   7.  Push to main  → GitHub Actions publishes the Release + ZIP
# ─────────────────────────────────────────────────────────────────────────────

set -euo pipefail

# ── Colours ──────────────────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

ok()   { echo -e "${GREEN}  ✓ $*${NC}"; }
warn() { echo -e "${YELLOW}  ⚠ $*${NC}"; }
err()  { echo -e "${RED}  ✗ $*${NC}"; exit 1; }
step() { echo -e "\n${CYAN}${BOLD}── $* ──${NC}"; }

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

PHP_BIN="/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php"
STYLE_CSS="$SCRIPT_DIR/style.css"
FUNCTIONS_PHP="$SCRIPT_DIR/functions.php"
PRE_COMMIT_SCRIPT="$SCRIPT_DIR/.github/hooks/scripts/pre-commit-check.sh"

# ─────────────────────────────────────────────────────────────────────────────
# STEP 0 — Resolve target version
# ─────────────────────────────────────────────────────────────────────────────
step "Resolving target version"

CURRENT_VERSION=$(grep -oE "Version: [0-9]+\.[0-9]+\.[0-9]+" "$STYLE_CSS" | head -1 | cut -d' ' -f2)
if [ -z "$CURRENT_VERSION" ]; then
  err "Could not read Version from style.css"
fi
echo "  Current version: $CURRENT_VERSION"

BUMP="${1:-patch}"

bump_version() {
  local ver="$1" part="$2"
  IFS='.' read -r major minor patch <<< "$ver"
  case "$part" in
    major) echo "$((major + 1)).0.0" ;;
    minor) echo "${major}.$((minor + 1)).0" ;;
    patch) echo "${major}.${minor}.$((patch + 1))" ;;
    *)
      # explicit version string
      if [[ "$part" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
        echo "$part"
      else
        err "Invalid bump argument '$part'. Use: major | minor | patch | X.Y.Z"
      fi
      ;;
  esac
}

NEW_VERSION="$(bump_version "$CURRENT_VERSION" "$BUMP")"

# Verify new version is actually greater
_ver_gt() {
  IFS='.' read -r a1 a2 a3 <<< "$1"
  IFS='.' read -r b1 b2 b3 <<< "$2"
  [[ $b1 -gt $a1 ]] || \
  { [[ $b1 -eq $a1 ]] && [[ $b2 -gt $a2 ]]; } || \
  { [[ $b1 -eq $a1 ]] && [[ $b2 -eq $a2 ]] && [[ $b3 -gt $a3 ]]; }
}

if ! _ver_gt "$CURRENT_VERSION" "$NEW_VERSION"; then
  err "New version ($NEW_VERSION) must be greater than current ($CURRENT_VERSION)"
fi

echo "  Target  version: ${BOLD}$NEW_VERSION${NC}"

# ─────────────────────────────────────────────────────────────────────────────
# STEP 1 — Pre-flight checks
# ─────────────────────────────────────────────────────────────────────────────
step "Pre-flight checks"

# Must be on main branch
CURRENT_BRANCH="$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo '')"
if [ "$CURRENT_BRANCH" != "main" ]; then
  err "You must be on the 'main' branch to release. Currently on: $CURRENT_BRANCH"
fi
ok "On branch: main"

# No uncommitted changes (except what we're about to create)
if ! git diff-index --quiet HEAD -- 2>/dev/null; then
  warn "Working tree has uncommitted changes."
  echo ""
  git status --short
  echo ""
  read -rp "  Continue anyway? Staged + unstaged changes will be included in the release commit. [y/N] " CONFIRM
  [[ "${CONFIRM,,}" == "y" ]] || { echo "Aborted."; exit 0; }
fi

# git must be available
command -v git >/dev/null 2>&1 || err "git is not installed or not in PATH"
ok "git found"

# node / npm must be available
command -v node >/dev/null 2>&1 || err "node is not installed or not in PATH"
command -v npm  >/dev/null 2>&1 || err "npm is not installed or not in PATH"
ok "node $(node --version) / npm $(npm --version) found"

# PHP must be available
[ -x "$PHP_BIN" ] || err "PHP not found at: $PHP_BIN"
ok "PHP found: $PHP_BIN"

# ─────────────────────────────────────────────────────────────────────────────
# STEP 2 — Pre-commit quality checks
# ─────────────────────────────────────────────────────────────────────────────
step "Running quality checks (.github/hooks/scripts/pre-commit-check.sh)"

if [ -f "$PRE_COMMIT_SCRIPT" ]; then
  chmod +x "$PRE_COMMIT_SCRIPT"
  if ! bash "$PRE_COMMIT_SCRIPT"; then
    err "Quality checks failed. Fix the errors above and re-run."
  fi
  ok "All quality checks passed"
else
  warn "pre-commit-check.sh not found – skipping quality gate"
fi

# ─────────────────────────────────────────────────────────────────────────────
# STEP 3 — Tailwind CSS production build
# ─────────────────────────────────────────────────────────────────────────────
step "Building Tailwind CSS (production/minified)"

npm run build:css
ok "Tailwind CSS built → assets/css/bigtricks-tailwind.css"

CSS_SIZE=$(du -sh "assets/css/bigtricks-tailwind.css" | cut -f1)
echo "  Size: $CSS_SIZE"

# ─────────────────────────────────────────────────────────────────────────────
# STEP 4 — Lucide icon bundle
# ─────────────────────────────────────────────────────────────────────────────
step "Rebuilding Lucide icon bundle"

npm run build:icons
ok "Lucide bundle rebuilt → assets/js/lucide-custom.js"

ICON_SIZE=$(du -sh "assets/js/lucide-custom.js" | cut -f1)
echo "  Size: $ICON_SIZE"

# ─────────────────────────────────────────────────────────────────────────────
# STEP 5 — Bump version in style.css and functions.php
# ─────────────────────────────────────────────────────────────────────────────
step "Bumping version: $CURRENT_VERSION → $NEW_VERSION"

# style.css — "Version: X.X.X"
sed -i.bak "s/Version: ${CURRENT_VERSION}/Version: ${NEW_VERSION}/" "$STYLE_CSS"
rm -f "${STYLE_CSS}.bak"
ok "style.css updated"

# functions.php — "define( 'BIGTRICKS_VERSION', 'X.X.X' )"
sed -i.bak "s/define( 'BIGTRICKS_VERSION', '${CURRENT_VERSION}' )/define( 'BIGTRICKS_VERSION', '${NEW_VERSION}' )/" "$FUNCTIONS_PHP"
rm -f "${FUNCTIONS_PHP}.bak"
ok "functions.php updated"

# Verify
VERIFY=$(grep -oE "Version: [0-9]+\.[0-9]+\.[0-9]+" "$STYLE_CSS" | head -1 | cut -d' ' -f2)
[ "$VERIFY" = "$NEW_VERSION" ] || err "Version bump failed! style.css still shows $VERIFY"

# ─────────────────────────────────────────────────────────────────────────────
# STEP 6 — Commit
# ─────────────────────────────────────────────────────────────────────────────
step "Committing release"

git add \
  "$STYLE_CSS" \
  "$FUNCTIONS_PHP" \
  "assets/css/bigtricks-tailwind.css" \
  "assets/js/lucide-custom.js"

# Stage anything else already modified (e.g. template edits done before release)
git add -u

COMMIT_MSG="chore: release v${NEW_VERSION}

- Tailwind CSS rebuilt (production)
- Lucide icon bundle updated
- Version bumped: ${CURRENT_VERSION} → ${NEW_VERSION}"

git commit -m "$COMMIT_MSG"
ok "Committed: chore: release v${NEW_VERSION}"

# ─────────────────────────────────────────────────────────────────────────────
# STEP 7 — Push → triggers GitHub Actions release workflow
# ─────────────────────────────────────────────────────────────────────────────
step "Pushing to main (triggers GitHub Actions release)"

git push origin main
ok "Pushed to origin/main"

# ─────────────────────────────────────────────────────────────────────────────
# Done
# ─────────────────────────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}${BOLD}  ✓  Released bigtricks-block v${NEW_VERSION}${NC}"
echo -e "${GREEN}${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo "  GitHub Actions will now:"
echo "    1. Build a clean theme ZIP"
echo "    2. Publish GitHub Release tagged v${NEW_VERSION}"
echo "    3. WordPress sites will show the update in Appearance → Themes"
echo ""
REPO_URL=$(git remote get-url origin 2>/dev/null || echo "your GitHub repo")
# Convert SSH to HTTPS for display
REPO_URL=$(echo "$REPO_URL" | sed 's|git@github.com:|https://github.com/|; s|\.git$||')
echo "  Track progress: ${REPO_URL}/actions"
echo ""
