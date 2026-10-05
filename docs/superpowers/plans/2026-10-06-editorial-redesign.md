# Editorial Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the public news frontend for DidebanShahr with a modern editorial look, dark/light theme support, responsive social icons (Iranian & international), and increased excerpt lengths.

**Architecture:** Update Laravel Blade template views and inline/custom CSS/JS variables. Ensure complete backward compatibility and host safety without modifying database schemas or backend logic.

**Tech Stack:** Laravel Blade, Bootstrap 5.3 RTL, FontAwesome 6, Bootstrap Icons, CSS Custom Properties (Variables), JavaScript (LocalStorage theme persistence).

**Spec:** `docs/superpowers/specs/2026-10-06-editorial-redesign.md`

## Global Constraints
- Do not alter database tables or backend models/controllers unless strictly required.
- Maintain full responsiveness across 375px (Mobile), 768px (Tablet), and 1200px+ (Desktop).
- Preserve all existing social media links (Eitaa, Bale, Rubika, Telegram, Instagram).
- Ensure WCAG contrast ratio compliance in both Light and Dark modes.

---

### Task 1: Master Layout & Dark/Light CSS Variables

**Files:**
- Modify: `resources/views/home/layout/master.blade.php`

**Interfaces:**
- Consumes: Existing Bootstrap 5 RTL setup in `master.blade.php`
- Produces: Updated CSS variables for `[data-bs-theme="light"]` and `[data-bs-theme="dark"]`, font setup, theme toggling JS function.

- [ ] **Step 1: Check master layout current structure**

Read `resources/views/home/layout/master.blade.php`.

- [ ] **Step 2: Update CSS variables and styles in master.blade.php**

Ensure the following CSS variables and styles exist in `<head>` style block:
```css
:root {
    --primary-color: #0d6efd;
    --primary-hover: #0b5ed7;
    --bs-body-font-family: 'Vazirmatn', Tahoma, sans-serif;
}

[data-bs-theme="light"] {
    --bs-body-bg: #f8f9fa;
    --bs-body-color: #212529;
    --bs-card-bg: #ffffff;
    --bs-border-color: #e9ecef;
    --navbar-bg: #ffffff;
    --footer-bg: #1e293b;
}

[data-bs-theme="dark"] {
    --bs-body-bg: #0f172a;
    --bs-body-color: #f8fafc;
    --bs-card-bg: #1e293b;
    --bs-border-color: #334155;
    --navbar-bg: #1e293b;
    --footer-bg: #0f172a;
}

* { font-family: 'Vazirmatn', Tahoma, sans-serif; }

body {
    background-color: var(--bs-body-bg);
    color: var(--bs-body-color);
    transition: background-color 0.3s ease, color 0.3s ease;
}
```

- [ ] **Step 3: Update theme toggle JavaScript**

Ensure script near bottom of `master.blade.php`:
```javascript
const themeToggle = document.getElementById('themeToggle');
const themeIcon = document.getElementById('themeIcon');
const html = document.documentElement;

function setTheme(theme) {
    html.setAttribute('data-bs-theme', theme);
    localStorage.setItem('theme', theme);
    if (themeIcon) {
        themeIcon.className = theme === 'dark' ? 'fas fa-sun text-warning' : 'fas fa-moon text-dark';
    }
}

const savedTheme = localStorage.getItem('theme') || 'light';
setTheme(savedTheme);

if (themeToggle) {
    themeToggle.addEventListener('click', () => {
        const currentTheme = html.getAttribute('data-bs-theme');
        setTheme(currentTheme === 'dark' ? 'light' : 'dark');
    });
}
```

- [ ] **Step 4: Commit**

```bash
git add resources/views/home/layout/master.blade.php
git commit -m "style: update master layout CSS variables and theme toggle JS"
```

---

### Task 2: Navbar & Social Icons Polish

**Files:**
- Modify: `resources/views/home/partial/navbar.blade.php`

**Interfaces:**
- Consumes: Image assets in `public/assets/img/icons/`
- Produces: Responsive navbar with unified social media icons and theme switch button.

- [ ] **Step 1: Inspect navbar markup**

Read `resources/views/home/partial/navbar.blade.php`.

- [ ] **Step 2: Update social icons and theme switch button markup**

Update `resources/views/home/partial/navbar.blade.php` to format social icons with uniform 28x28px dimensions, hover scale, and dark-mode friendly theme toggle button:
```html
<header>
    <nav class="navbar navbar-expand-lg navbar-custom border-bottom shadow-sm">
        <div class="container">
            <div class="logo-container">
                <a class="logo-text d-flex align-items-center gap-2 text-decoration-none" href="/">
                    <img src="{{ asset('assets/img/branding/logo.png') }}" alt="لوگو" width="40" height="40">
                    <span class="fw-bold fs-5 text-body">{{ config('app.name', 'پایگاه خبری دیدبان شهر') }}</span>
                </a>
            </div>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto my-2 my-lg-0">
                    <li class="nav-item"><a class="nav-link nav-link-custom px-3 fw-medium" href="/">صفحه اصلی</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom px-3 fw-medium" href="{{ route('news') }}">اخبار</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom px-3 fw-medium" href="{{ route('notes') }}">یادداشت‌ها</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom px-3 fw-medium" href="{{ route('interviews') }}">مصاحبه‌ها</a></li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <div class="social-icons d-none d-lg-flex align-items-center gap-2">
                        <a href="https://eitaa.com/didebaneshahr_ir" target="_blank" rel="noopener noreferrer" title="ایتا">
                            <img src="{{ asset('assets/img/icons/eitaa.png') }}" alt="ایتا" width="28" height="28" class="rounded-1 hover-scale">
                        </a>
                        <a href="https://ble.ir/didebaneshahr_ir" target="_blank" rel="noopener noreferrer" title="بله">
                            <img src="{{ asset('assets/img/icons/bale.png') }}" alt="بله" width="28" height="28" class="rounded-1 hover-scale">
                        </a>
                        <a href="https://rubika.ir/didebaneshahr_ir" target="_blank" rel="noopener noreferrer" title="روبیکا">
                            <img src="{{ asset('assets/img/icons/rubika.png') }}" alt="روبیکا" width="28" height="28" class="rounded-1 hover-scale">
                        </a>
                        <a href="https://t.me/didebaneshahr_ir" target="_blank" rel="noopener noreferrer" title="تلگرام">
                            <img src="{{ asset('assets/img/icons/telegram.png') }}" alt="تلگرام" width="28" height="28" class="rounded-1 hover-scale">
                        </a>
                        <a href="https://www.instagram.com/didebaneshahr_ir" target="_blank" rel="noopener noreferrer" title="اینستاگرام">
                            <img src="{{ asset('assets/img/icons/instagram.png') }}" alt="اینستاگرام" width="28" height="28" class="rounded-1 hover-scale">
                        </a>
                    </div>

                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-flex align-items-center gap-2" id="themeToggle" type="button" aria-label="تغییر تم">
                        <i id="themeIcon" class="fas fa-moon"></i>
                        <span class="d-none d-sm-inline small">حالت شب/روز</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</header>
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/home/partial/navbar.blade.php
git commit -m "feat: redesign navbar with unified social icons and theme button"
```

---

### Task 3: Homepage Hero, News Cards Excerpt & Breaking News Ticker

**Files:**
- Modify: `resources/views/home/index.blade.php`

**Interfaces:**
- Consumes: `$slides`, `$news`, `$notes`, `$interviews` from controller
- Produces: Editorial news layout with breaking news banner, increased excerpt lengths (150 chars), and styled news cards.

- [ ] **Step 1: Inspect index.blade.php**

Read `resources/views/home/index.blade.php`.

- [ ] **Step 2: Update index.blade.php with Breaking News & Expanded Excerpts**

In `resources/views/home/index.blade.php`:
1. Add breaking news ticker at top of page before slider if slides exist.
2. Update slider caption content limit: `{!! Str::limit(strip_tags($slide->content), 120, '...') !!}`
3. Update news card title: `{{ Str::limit($item->title, 70, '...') }}`
4. Uncomment and update news card excerpt: `<p class="news-card-excerpt text-secondary small mt-2 mb-3">{!! Str::limit(strip_tags($item->content), 150, '...') !!}</p>`
5. Update sidebar note excerpts and interview excerpts if applicable.

- [ ] **Step 3: Test syntax with php -l**

Run: `php -l resources/views/home/index.blade.php`
Expected: "No syntax errors detected in resources/views/home/index.blade.php"

- [ ] **Step 4: Commit**

```bash
git add resources/views/home/index.blade.php
git commit -m "feat: add breaking news banner, expand news card excerpts to 150 chars"
```

---

### Task 4: News Show Page & List Views Polish

**Files:**
- Modify: `resources/views/home/showNews.blade.php`
- Modify: `resources/views/home/allNews.blade.php`
- Modify: `resources/views/home/allNotes.blade.php`
- Modify: `resources/views/home/allInterviews.blade.php`

**Interfaces:**
- Consumes: Individual post data `$post` and list view collections
- Produces: Polished, readable single article view and grid lists with expanded excerpts.

- [ ] **Step 1: Read single news and list view templates**

Read `resources/views/home/showNews.blade.php`, `resources/views/home/allNews.blade.php`, `resources/views/home/allNotes.blade.php`, and `resources/views/home/allInterviews.blade.php`.

- [ ] **Step 2: Polish showNews.blade.php typography and social share box**

Ensure proper padding, font-size, line-height 1.8 for article body, and social share buttons.

- [ ] **Step 3: Update excerpt limits in allNews, allNotes, allInterviews**

Change limit from 20/15 chars to 150 chars: `{!! Str::limit(strip_tags($item->content), 150, '...') !!}`.

- [ ] **Step 4: Verify syntax**

Run: `php -l resources/views/home/showNews.blade.php && php -l resources/views/home/allNews.blade.php`

- [ ] **Step 5: Commit**

```bash
git add resources/views/home/showNews.blade.php resources/views/home/allNews.blade.php resources/views/home/allNotes.blade.php resources/views/home/allInterviews.blade.php
git commit -m "feat: polish single article view and expand excerpts in all list views"
```

---

### Task 5: Final Quality Verification & Cleanup

**Files:**
- Verify: All touched Blade templates
- Remove: Temporary preview file `public/preview-proposals.html` if no longer needed or keep it if approved.

- [ ] **Step 1: Run git status and check for clean working directory**

Run: `git status`

- [ ] **Step 2: Verify site routes response**

Run: `php artisan route:list` to verify routes are healthy.

- [ ] **Step 3: Commit final plan verification**

```bash
git add .
git commit -m "chore: complete editorial redesign implementation"
```
