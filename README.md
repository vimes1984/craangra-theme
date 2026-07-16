# Crann Grá WordPress Theme

A bespoke WordPress block theme (Full Site Editing compatible) designed specifically for **Crann Grá**'s WooCommerce plant nursery store. This theme features custom templates, page patterns, typography, and color styling tailored to garden and horticultural WooCommerce stores.

## 🎨 Design System

The theme uses a curated, premium color palette and typography scale defined in `theme.json`:

### Color Palette

| Color | Hex Code | Slug | Name | Description |
| :--- | :--- | :--- | :--- | :--- |
| **Forest Green** | `#2d5a27` | `primary` | `Primary` | Core brand color for headings, links, and buttons. |
| **Honey Gold** | `#d4af37` | `secondary` | `Secondary` | Accent color for highlight details. |
| **Cream** | `#fcfbfa` | `background` | `Background` | Clean, soft cream background color. |
| **Charcoal** | `#222222` | `foreground` | `Foreground` | Primary text color. |
| **Terracotta** | `#c05a46` | `accent` | `Accent` | Bright accent color. |

### Typography

- **Headings**: `Cardo` (Georgia, Serif) - weights: `400`, `700`
- **Body & Controls**: `Outfit` (Sans-serif) - variable weight (`100 900`)

---

## 📁 Directory Structure

```text
crann-gra-theme/
├── assets/
│   ├── fonts/           # Custom local woff2 files (Outfit & Cardo)
│   └── js/              # Theme scripts (e.g. shop-filter.js)
├── images/              # Custom image assets for layout patterns
├── parts/               # Reusable template parts (header, footer)
├── patterns/            # Pre-built page block patterns (Home, Contact, Our Story)
├── templates/           # Page templates (archive-product, single, index, page, etc.)
├── functions.php        # Custom theme features, styles, & script enqueues
├── style.css            # Stylesheet (metadata and theme customizations)
├── theme.json           # Block theme configuration
└── screenshot.jpg       # Theme dashboard preview
```

---

## 🛠️ Custom Templates & Layouts

The theme register several page layouts accessible via the WordPress Page Editor:

1. **Home Page Layout** (`page-home`): Custom layout built for highlighting featured products, collections, and brand story.
2. **Our Story Layout** (`page-our-story`): Rich layout structure for conveying company background and values.
3. **Contact Layout** (`page-contact`): Clean layout with integrated contact and location options.
4. **WooCommerce Templates**: 
   - `archive-product.html` (Shop archives / Categories)
   - `single-product.html` (Individual WooCommerce product layouts)
   - `taxonomy-product_cat.html` (Product category pages)

---

## ⚙️ Installation

1. Compress the theme folder:
   ```bash
   zip -r crann-gra-theme.zip crann-gra-theme
   ```
2. Navigate to your WordPress Dashboard.
3. Go to **Appearance > Themes > Add New > Upload Theme**.
4. Upload `crann-gra-theme.zip` and click **Install Now**.
5. Click **Activate** once installed.
