#!/usr/bin/env node
/**
 * Generates assets/js/lucide-custom.js — a slim Lucide bundle containing only
 * the icons actually used in this theme.
 *
 * Icons are auto-discovered by scanning all .php and .js files for
 * data-lucide="icon-name" attributes. No manual list to maintain.
 *
 * Run:  node generate-lucide-bundle.js
 *       npm run build:icons
 */
'use strict';

const lucide = require('lucide');
const fs = require('fs');
const path = require('path');

// ---------------------------------------------------------------------------
// 1. Auto-scan theme files for data-lucide="..." usages
// ---------------------------------------------------------------------------

/** Recursively collect all files matching an extension inside a directory. */
function walkDir(dir, exts, results) {
  results = results || [];
  var entries;
  try { entries = fs.readdirSync(dir); } catch (e) { return results; }
  entries.forEach(function(entry) {
    var full = path.join(dir, entry);
    // Skip node_modules, the generator script itself, and the output bundle
    if (entry === 'node_modules') return;
    if (entry === 'generate-lucide-bundle.js') return;
    if (full.endsWith('lucide-custom.js')) return;
    var stat;
    try { stat = fs.statSync(full); } catch (e) { return; }
    if (stat.isDirectory()) {
      walkDir(full, exts, results);
    } else if (exts.indexOf(path.extname(entry)) !== -1) {
      results.push(full);
    }
  });
  return results;
}

var THEME_ROOT = __dirname;
var files = walkDir(THEME_ROOT, ['.php', '.js']);
var iconSet = {};
var DATA_LUCIDE_RE = /data-lucide=["']([a-z0-9-]+)["']/g;

files.forEach(function(file) {
  var src = fs.readFileSync(file, 'utf8');
  var match;
  while ((match = DATA_LUCIDE_RE.exec(src)) !== null) {
    iconSet[match[1]] = true;
  }
});

var USED_ICONS = Object.keys(iconSet).sort();
console.log('Scanned ' + files.length + ' files — found ' + USED_ICONS.length + ' unique icons.');

function toPascalCase(str) {
  return str.split('-').map(function(s) {
    return s.charAt(0).toUpperCase() + s.slice(1);
  }).join('');
}

function renderNode(node) {
  if (typeof node !== 'object' || node === null) return '';
  if (!Array.isArray(node)) return '';
  const tag = node[0];
  const attrs = node[1] || {};
  const children = Array.isArray(node[2]) ? node[2] : [];
  const attrsStr = Object.entries(attrs)
    .map(function(pair) { return pair[0] + '="' + pair[1] + '"'; })
    .join(' ');
  const childStr = children.map(renderNode).join('');
  return '<' + tag + (attrsStr ? ' ' + attrsStr : '') + '>' + childStr + '</' + tag + '>';
}

// Social brand icons removed from Lucide — provide minimal inline SVG paths
const MANUAL_ICONS = {
  'facebook': '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
  'instagram': '<rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>',
  'twitter': '<path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>',
};

const icons = {};
const missing = [];

for (let i = 0; i < USED_ICONS.length; i++) {
  const name = USED_ICONS[i];

  // Use manual override if available
  if (MANUAL_ICONS[name] !== undefined) {
    icons[name] = MANUAL_ICONS[name];
    continue;
  }

  const key = toPascalCase(name);
  const icon = lucide[key];
  if (!icon) {
    missing.push(name);
    continue;
  }
  // lucide exports: array of child nodes [[tag, attrs], ...]
  icons[name] = icon.map(renderNode).join('');
}

if (missing.length > 0) {
  console.warn('WARNING — icons not found in lucide package (add to MANUAL_ICONS):', missing.join(', '));
}

const iconsJSON = JSON.stringify(icons);

const bundle = `/**
 * Bigtricks Slim Lucide Bundle
 * Auto-generated — only the ${Object.keys(icons).length} icons used in this theme.
 * Full Lucide library: ~400 KB | This bundle: ~${(Buffer.byteLength(iconsJSON, 'utf8') / 1024).toFixed(0)} KB
 * DO NOT EDIT MANUALLY — regenerate with: node generate-lucide-bundle.js
 */
(function (global) {
  'use strict';

  /* Icon SVG inner HTML, keyed by icon name */
  var ICONS = ${iconsJSON};

  var DEFAULT_ATTRS = [
    ['xmlns', 'http://www.w3.org/2000/svg'],
    ['width', '24'],
    ['height', '24'],
    ['viewBox', '0 0 24 24'],
    ['fill', 'none'],
    ['stroke', 'currentColor'],
    ['stroke-width', '2'],
    ['stroke-linecap', 'round'],
    ['stroke-linejoin', 'round']
  ];

  function createSvgForElement(name, el) {
    var innerHTML = ICONS[name];
    if (innerHTML === undefined) {
      console.warn('[lucide-custom] Unknown icon: ' + name);
      return;
    }

    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');

    for (var d = 0; d < DEFAULT_ATTRS.length; d++) {
      svg.setAttribute(DEFAULT_ATTRS[d][0], DEFAULT_ATTRS[d][1]);
    }

    // Transfer class attribute from <i> to <svg>
    var cls = el.getAttribute('class');
    if (cls) svg.setAttribute('class', cls);

    // Transfer any other attributes (aria-*, style, id, etc.) except data-lucide/class
    var attrs = el.attributes;
    for (var a = 0; a < attrs.length; a++) {
      var attrName = attrs[a].name;
      if (attrName !== 'data-lucide' && attrName !== 'class') {
        svg.setAttribute(attrName, attrs[a].value);
      }
    }

    svg.setAttribute('aria-hidden', 'true');
    svg.innerHTML = innerHTML;
    el.parentNode.replaceChild(svg, el);
  }

  function createIcons() {
    var nodes = document.querySelectorAll('[data-lucide]');
    for (var i = 0; i < nodes.length; i++) {
      createSvgForElement(nodes[i].getAttribute('data-lucide'), nodes[i]);
    }
  }

  var api = { createIcons: createIcons };

  /* UMD export */
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = api;
  } else {
    global.lucide = api;
  }

}(typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : this));
`;

const outputPath = path.join(__dirname, 'assets', 'js', 'lucide-custom.js');
fs.writeFileSync(outputPath, bundle);

const finalSize = fs.statSync(outputPath).size;
console.log('Generated: assets/js/lucide-custom.js');
console.log('Icons    :', Object.keys(icons).length, '(' + USED_ICONS.join(', ') + ')');
console.log('File size:', (finalSize / 1024).toFixed(1), 'KB', '(vs ~400 KB full library)');
console.log('Savings  :', ((1 - finalSize / (400 * 1024)) * 100).toFixed(0) + '%');
if (missing.length > 0) {
  console.warn('Add these to MANUAL_ICONS in generate-lucide-bundle.js:', missing.join(', '));
}
