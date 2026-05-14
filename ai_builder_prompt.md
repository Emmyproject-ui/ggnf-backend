# ✅ AI Builder Prompt — Liko Blog Classic Page

> **How to use:** Copy everything inside the code block below and paste it directly into your AI builder (Lovable, Bolt.new, V0.dev, Framer AI, Webflow AI, etc.)

---

```
Build a premium, dark-themed creative agency blog page from scratch using HTML, CSS, and JavaScript. The design should feel modern, minimal, and high-end — inspired by top creative portfolio agencies. Here are the full requirements:

---

## 🎨 DESIGN SYSTEM

- **Font:** Use "Inter" or "DM Sans" from Google Fonts
- **Primary color:** #0a0a0a (near black background)
- **Text color:** #f3f3f4 (off-white)
- **Accent color:** #ffffff
- **Secondary text:** #888888
- **Border color:** rgba(255,255,255,0.1)
- **Layout:** Max width container 1480px, centered
- **Style:** Glassmorphism accents, smooth animations, dark mode throughout

---

## 🔄 PAGE PRELOADER

- Full-screen black overlay on page load
- 9 vertical animated bars/lines that wave up and down in sequence (staggered CSS keyframe animation)
- Below the bars show the text: "Loading ..."
- Fade out the preloader after 1.5 seconds and reveal the page

---

## 🧭 STICKY HEADER / NAVIGATION

- Transparent header that becomes solid dark on scroll (sticky)
- Left: Logo text "LIKO" in white, bold
- Center: Navigation links — Home, Pages, Portfolio, Blog, Contact
- Right: Shopping cart icon (with count badge "0") + hamburger menu icon
- Each nav item has a hover underline animation
- On mobile: hide center nav, show hamburger only

---

## 📥 OFF-CANVAS SIDE MENU (opens on hamburger click)

- Slides in from the right
- Close button (X icon) top right
- Title: "Hello There!"
- Subtitle: "We are a creative studio focused on bold ideas and beautiful design."
- A 2x2 grid of 4 small gallery thumbnail images (use placeholder images)
- Section titled "Information":
  - Phone: + 4 20 7700 1007
  - Email: hello@liko.com
  - Address: Avenue de Roma 158b, Lisboa
- Section titled "Follow Us" with icons for: Instagram, Behance, Dribbble, YouTube
- Dark overlay behind the drawer when open

---

## 🛒 MINI CART SIDEBAR (opens on cart icon click)

- Slides in from the right
- Title: "Shopping cart"
- Message: "Free Shipping for all orders over $50" with an animated striped progress bar at 70%
- One product item:
  - Product name: "Level Bolt Smart Lock"
  - Price: $46.00 × 2
  - Thumbnail image (placeholder)
  - Remove (×) button
- Bottom section:
  - "Subtotal:" label with "$113.00" value
  - Two full-width buttons stacked: "View Cart" and "Checkout"

---

## 🎠 HERO SLIDER SECTION

- Full-viewport-height section with a dark image background (use a dark gradient or dark placeholder image)
- Swiper.js carousel with 5 slides (all same content for demo) and auto-play
- Each slide contains:
  - Small author avatar (circle image) + author name: "Mark Hopkins"
  - Category tag + date: "BRANDING · 27 JULY, 2022"
  - Large heading (animate letter by letter on slide change): "Relax while learning design and Be Connected"
  - "Read More" link with an arrow →
- Bottom-left: "Scroll to explore" text with a vertical animated scroll-down arrow SVG
- Bottom-right: "Next" / "Prev" navigation buttons styled as text buttons
- The heading text should animate character by character when the slide appears (GSAP SplitText style or CSS equivalent)

---

## 📝 BLOG POST LISTING SECTION (left column, 2/3 width)

Use a two-column layout: left blog posts (66%) + right sidebar (33%).

### Post 1 — Standard Image Post
- Full-width image at top (placeholder)
- Meta: "WORK · 01 DEC, 2022" in small caps gray
- Title: "Design To Remember" (bold, large)
- Short paragraph: "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat."
- "Read More" button with border (outline style, dark text)
- Hover: button fills dark, text turns white

### Post 2 — Video Post (image + play button overlay)
- Same structure as Post 1
- Title: "Desert Treasure Hunt"
- On image: centered circular play button overlay
- Clicking opens a YouTube popup lightbox

### Post 3 — Link / Quote Post (no image)
- Chain link SVG icon on the left
- Large centered text: "MERGE DIFFERENT TO CREATE A PERFECT PLAYLIST FOR EACH."
- Card with a subtle border, slightly different background

### Post 4 — Image Slider Post
- Swiper image carousel with 3 slides (placeholder images) and left/right arrow buttons
- Title: "Future Business Ideas."
- Same meta and excerpt format
- "Read More" button

### Post 5 — Blockquote Post
- Large quotation mark SVG icon
- Italic quote text: "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, uyam erat."
- Author attribution: "— SEM SMITH, CREATIVE DIRECTOR"
- Dark card with left accent border

### Post 6 — Standard Image Post
- Title: "Simplistic Photo Setup"
- Same format as Post 1

### Pagination
- Simple numbered pagination: 1 (active/filled), 2, → (next arrow)
- Active page has a filled dark circle

---

## 📌 SIDEBAR (right column, 1/3 width)

### Author Widget
- Circular author photo (placeholder)
- Name: "Mark Hopkins" bold
- Bio: "Lorem ipsum dolor consectetur adipiscing elit."
- Centered layout, subtle card with border

### Search Widget
- Full-width text input: placeholder "Search..."
- Search icon button on the right inside the input
- Rounded or squared minimal style

### Category Widget
- Section title: "Category"
- List of links: Branding, Lifestyle, UI/UX Design, Production
- Each link has a subtle right-arrow → that appears on hover

### Recent Posts Widget
- Section title: "Recent Post"
- 3 posts, each with:
  - Small thumbnail (50x50px placeholder)
  - Date: "30. JAN. 2024"
  - Post title link: "Design in the finest style", "Photography history being told" (×2)

### Tags Widget
- Section title: "Tags"
- Pill/chip-style tag buttons: Creative, Vision, Popular, Photography, Lifestyle
- On hover: fill dark background

### Social Follow Widget
- Section title: "Follow Us"
- Icon buttons for: Facebook, Twitter, LinkedIn
- Hover: icon color changes to accent

---

## 🦶 FOOTER

Two-row dark footer (background: #0a0a0a):

### Row 1 — 4 columns:

**Column 1 — Brand**
- Logo: "LIKO" white
- Text: "Drop us a line. We'd love to hear from you and start something great together."

**Column 2 — Sitemap**
- Title: "Sitemap"
- Links: Home, About, Contact, Blog, Portfolio

**Column 3 — Office**
- Title: "Office"
- Address: 740 New South Head Rd, Triple Bay, New York
- Phone: P: +725 214 456
- Email: E: contact@liko.com

**Column 4 — Newsletter**
- Title: "Subscribe to our newsletter"
- Email input field + arrow submit button inside the input (right-aligned)

### Row 2 — Copyright bar:
- Left: "All rights reserved — 2024 © Liko"
- Right: Social text links — Linkedin, Twitter, Instagram (spaced out)
- Thin top border line separating from row 1

---

## 🎬 ANIMATIONS & INTERACTIONS

1. **Preloader:** 9-bar wave animation, fades out on load
2. **Smooth scroll:** Implement smooth scrolling on the entire page
3. **Hero title:** Character-by-character text reveal animation on slide change (use CSS or GSAP)
4. **Scroll-triggered fade-ins:** Blog post articles fade up into view as user scrolls
5. **Sticky header:** Header becomes opaque white/dark with shadow when user scrolls past 80px
6. **Hover effects on all buttons and links:** Smooth color/border transitions (0.3s ease)
7. **Off-canvas and mini cart:** Smooth slide-in from right (CSS transform translateX)
8. **Dark overlay:** Fades in behind off-canvas drawers
9. **Back-to-top button:** Appears after scrolling 300px, smooth scroll to top on click
10. **Image hover zoom:** Blog post thumbnails subtly scale up (1.05) on hover
11. **Swiper sliders:** Auto-play with smooth fade or slide transition
12. **Tag pill hover:** Background fills on hover with smooth transition
13. **Custom cursor (optional):** Large circle cursor that follows the mouse with slight lag

---

## 📱 RESPONSIVE BREAKPOINTS

- **Desktop (≥1280px):** Full two-column blog + sidebar layout
- **Tablet (768px–1279px):** Single column blog posts, sidebar moves below
- **Mobile (<768px):** Full single column, hamburger nav only, stacked footer columns, hero text smaller

---

## 🔧 TECH STACK

- Pure HTML5 + CSS3 + Vanilla JavaScript
- Use Swiper.js (CDN) for sliders
- Use GSAP (CDN) for scroll-triggered animations and text reveal
- Google Fonts for typography
- Font Awesome (CDN) for icons
- No frameworks like React or Vue — keep it plain HTML/CSS/JS

---

## ✅ FINAL NOTES

- All images can be placeholder images (use https://picsum.photos for random beautiful photos)
- Make the page feel like a $10,000 agency website — premium spacing, clean typography, confident layout
- Dark mode only — no light mode toggle needed
- Add subtle grain/noise texture to the hero background for a premium feel
```
