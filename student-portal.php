<?php
session_start();

// Server-side auth gate — no JS sessionStorage check needed anymore
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header('Location: student-login.html');
    exit;
}

$studentName      = htmlspecialchars($_SESSION['userName'] ?? 'Student');
$studentAdmission = htmlspecialchars($_SESSION['admissionNo'] ?? '');
$studentAvatar    = htmlspecialchars($_SESSION['userAvatar'] ?? 'https://i.pravatar.cc/80?img=47');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Portal | CampusCare</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="studentportal.css">
</head>

<body>

<!--TOP NAV-->
<nav class="portal-nav">
    <a href="Home.html" class="nav-logo">
        <i class="fa-solid fa-heart"></i>
        <div>
            <h2>CampusCare</h2>
            <p>Student Portal</p>
        </div>
    </a>

    <ul class="portal-tabs">
        <li>
            <button class="active" onclick="switchTab('dashboard', this)">
                <i class="fa-solid fa-house"></i> Dashboard
            </button>
        </li>
        <li>
            <button onclick="switchTab('academics', this)">
                <i class="fa-solid fa-graduation-cap"></i> Academics
            </button>
        </li>
        <li>
            <button onclick="openCheckin()">
                <i class="fa-solid fa-clipboard-check"></i> Check-In
            </button>
        </li>
        <li>
            <button onclick="switchTab('journal', this)">
                <i class="fa-solid fa-pen-fancy"></i> Journal
            </button>
        </li>
        <li>
            <button onclick="switchTab('announcements', this)">
                <i class="fa-solid fa-bullhorn"></i> Announcements
            </button>
        </li>
        <li>
            <button onclick="switchTab('settings', this)">
                <i class="fa-solid fa-gear"></i> Settings
            </button>
        </li>
    </ul>

    <div class="nav-right">
        <a href="Home.html" class="home-link">
            <i class="fa-solid fa-arrow-left"></i> Main Site
        </a>
        <div class="avatar-btn" onclick="switchTab('settings', null)">
            <img id="navAvatar" src="<?= $studentAvatar ?>" alt="Profile">
        </div>
    </div>
</nav>


<!--DASHBOARD-->
<div id="dashboard" class="portal-page active">

    <!-- Greeting Banner -->
    <div class="greeting-banner">
        <div class="greeting-inner">

            <!-- Profile + Name -->
            <div class="greeting-profile">
                <img id="greetingAvatar" class="greeting-avatar" src="<?= $studentAvatar ?>" alt="Profile photo">
                <div class="greeting-text">
                    <p class="eyebrow">Welcome back</p>
                    <h1 id="greetingName"><?= $studentName ?></h1>
                    <p class="student-sub" id="studentSub">Good to see you </p>
                </div>
            </div>

            <!-- Affirmation -->
            <div class="affirmation-pill">
                <p class="pill-label">
                    <i class="fa-solid fa-sun"></i> Today's affirmation
                </p>
                <p id="affirmationText">Loading…</p>
                <button class="refresh-affirmation" onclick="loadAffirmation()">
                    <i class="fa-solid fa-rotate-right"></i> New affirmation
                </button>
            </div>

        </div>
    </div>

    <!-- Dashboard Body -->
    <div class="dashboard-body">

        <!-- Quick Actions -->
        <div class="quick-actions">
            <button class="action-card" onclick="openCheckin()">
                <div class="action-icon purple"><i class="fa-solid fa-clipboard-check"></i></div>
                <div class="action-text">
                    <h4>Start Check-In</h4>
                    <p>Log today's mood &amp; wellbeing</p>
                </div>
            </button>

            <button class="action-card" onclick="switchTab('journal', null)">
                <div class="action-icon green"><i class="fa-solid fa-pen-fancy"></i></div>
                <div class="action-text">
                    <h4>Open Journal</h4>
                    <p>Write your thoughts privately</p>
                </div>
            </button>

            <button class="action-card" onclick="switchTab('academics', null)">
                <div class="action-icon orange"><i class="fa-solid fa-graduation-cap"></i></div>
                <div class="action-text">
                    <h4>Academics</h4>
                    <p>Grades &amp; attendance overview</p>
                </div>
            </button>

            <button class="action-card" onclick="window.location.href='Contact.html'">
                <div class="action-icon blue"><i class="fa-solid fa-phone"></i></div>
                <div class="action-text">
                    <h4>Get Support</h4>
                    <p>Talk to a counsellor</p>
                </div>
            </button>
        </div>

        <!-- Substance-Free Tracker -->
        <div class="dashboard-cols" style="margin-top:0;">
            <div class="dash-card sobriety-card" style="grid-column: 1 / -1;">
                <h3><i class="fa-solid fa-seedling"></i> Substance-Free Tracker</h3>
                <p style="font-size:11px;color:#aaa;margin-top:-8px;margin-bottom:10px;">Private — only you can see this.</p>
                <div id="sobrietyArea"></div>
            </div>
        </div>

        <!-- Two-column lower -->
        <div class="dashboard-cols">

            <!-- Profile + CTA -->
            <div class="dash-card">
                <h3><i class="fa-solid fa-user"></i> Your Profile</h3>

                <div class="profile-row">
                    <img id="profilePreview" class="profile-avatar" src="<?= $studentAvatar ?>" alt="Profile">
                    <div>
                        <p class="profile-name" id="usernameDisplay"><?= $studentName ?></p>
                        <p class="profile-sub" id="admissionDisplay"><?= $studentAdmission ?> · CampusCare Member</p>
                    </div>
                </div>

                <div class="mood-today">
                    <i class="fa-solid fa-face-smile"></i>
                    How are you feeling today?
                </div>

                <button class="checkin-btn" onclick="openCheckin()">
                    <i class="fa-solid fa-clipboard-check"></i>
                    Begin Mental Health Check-In
                </button>
            </div>

            <!-- Counselor Support -->
            <div class="dash-card">
                <h3><i class="fa-solid fa-hand-holding-heart"></i> Counselor Support</h3>

                <div id="counselorRequestStatus">
                    <p style="font-size:13px;color:#aaa;">Checking your request status…</p>
                </div>
            </div>

        </div>

        <!-- Progress Chart (own row, was paired with Profile before) -->
        <div class="dashboard-cols" style="margin-top:20px;">
            <div class="dash-card" style="grid-column: 1 / -1;">
                <h3><i class="fa-solid fa-chart-line"></i> Wellbeing Progress</h3>

                <div id="chartArea">
                    <div class="no-data" id="noDataMsg">
                        <i class="fa-solid fa-chart-bar"></i>
                        <p>Complete a check-in to see your progress here.</p>
                    </div>
                    <div class="chart-wrap" id="chartWrap" style="display:none;">
                        <canvas id="historyChart"></canvas>
                    </div>
                    <p id="latestScore" style="margin-top:12px;font-size:13px;color:#8b5cf6;font-weight:600;"></p>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ══════════ ACADEMICS ══════════ -->
<div id="academics" class="portal-page">
    <div class="journal-body">
        <div class="page-header">
            <p class="eyebrow">Your Records</p>
            <h2>Academics</h2>
        </div>

        <div class="c-card" id="academicWarning" style="display:none; margin-bottom:20px; background:#fff5f5; border:1.5px solid #fecaca; border-radius:16px; padding:18px 22px;">
            <p style="font-size:14px; color:#b91c1c; font-weight:600; display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Heads up — you have failing units or high absences this semester. Consider reaching out to your counsellor.
            </p>
        </div>

        <div class="journal-card" style="margin-bottom:20px;">
            <h3><i class="fa-solid fa-book"></i> Grades</h3>
            <div style="overflow-x:auto;">
                <table class="acad-table">
                    <thead>
                        <tr>
                            <th>Unit Code</th>
                            <th>Unit Name</th>
                            <th>CAT 1</th>
                            <th>CAT 2</th>
                            <th>Assignment</th>
                            <th>Exam</th>
                            <th>Total</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody id="gradesTableBody">
                        <tr><td colspan="8" style="text-align:center; color:#aaa; padding:20px;">Loading grades…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="journal-card">
            <h3><i class="fa-solid fa-calendar-check"></i> Attendance</h3>
            <div style="overflow-x:auto;">
                <table class="acad-table">
                    <thead>
                        <tr>
                            <th>Unit Code</th>
                            <th>Unit Name</th>
                            <th>Total Classes</th>
                            <th>Attended</th>
                            <th>Absences</th>
                            <th>Absence %</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTableBody">
                        <tr><td colspan="6" style="text-align:center; color:#aaa; padding:20px;">Loading attendance…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- ══════════ JOURNAL ══════════ -->
<div id="journal" class="portal-page">
    <div class="journal-body">
        <div class="page-header">
            <p class="eyebrow">Private Space</p>
            <h2>Your Journal</h2>
        </div>

        <div class="journal-grid">

            <!-- Write -->
            <div class="journal-card">
                <h3><i class="fa-solid fa-pen"></i> Write an Entry</h3>

                <p style="font-size:13px;color:#888;margin-bottom:14px;">How are you feeling right now?</p>

                <div class="mood-chips" id="moodChips">
                    <button class="chip" onclick="selectMood(this)"> Happy</button>
                    <button class="chip" onclick="selectMood(this)"> Neutral</button>
                    <button class="chip" onclick="selectMood(this)"> Sad</button>
                    <button class="chip" onclick="selectMood(this)"> Anxious</button>
                    <button class="chip" onclick="selectMood(this)"> Frustrated</button>
                </div>

                <textarea class="journal-textarea" id="journalEntry"
                    placeholder="Write freely — this is your safe space. No one else can read this."></textarea>

                <button class="save-btn" onclick="saveJournal()">
                    <i class="fa-solid fa-floppy-disk"></i> Save Entry
                </button>
                <div class="save-confirm" id="saveConfirm">
                    <i class="fa-solid fa-circle-check"></i> Entry saved!
                </div>
            </div>

            <!-- Prompts -->
            <div class="journal-card">
                <h3><i class="fa-solid fa-lightbulb"></i> Writing Prompts</h3>
                <p style="font-size:13px;color:#888;margin-bottom:16px;">Tap a prompt to use it as a starting point.</p>

                <ul class="prompts-list">
                    <li onclick="usePrompt(this)">
                        <i class="fa-solid fa-arrow-right"></i>
                        What is one thing that went well today, no matter how small?
                    </li>
                    <li onclick="usePrompt(this)">
                        <i class="fa-solid fa-arrow-right"></i>
                        What emotion am I carrying right now, and where do I feel it?
                    </li>
                    <li onclick="usePrompt(this)">
                        <i class="fa-solid fa-arrow-right"></i>
                        What would I tell a close friend going through what I am?
                    </li>
                    <li onclick="usePrompt(this)">
                        <i class="fa-solid fa-arrow-right"></i>
                        What is one thing I can let go of today to feel lighter?
                    </li>
                    <li onclick="usePrompt(this)">
                        <i class="fa-solid fa-arrow-right"></i>
                        Three things I am grateful for right now.
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>


<!-- ══════════ ANNOUNCEMENTS ══════════ -->
<div id="announcements" class="portal-page">
    <div class="journal-body">
        <div class="page-header">
            <p class="eyebrow">Announcements</p>
            <h2>Announcements</h2>
        </div>

        <div class="journal-card">
            <h3><i class="fa-solid fa-bullhorn"></i> Latest Updates</h3>
            <div id="announcementHistory">
                <p style="font-size:13px;color:#888;">Loading announcements…</p>
            </div>
        </div>
    </div>
</div>


<!-- ══════════ SETTINGS ══════════ -->
<div id="settings" class="portal-page">
    <div class="settings-body">
        <div class="page-header">
            <p class="eyebrow">Preferences</p>
            <h2>Your Settings</h2>
        </div>

        <div class="settings-grid">

            <!-- Profile -->
            <div class="settings-card">
                <h3><i class="fa-solid fa-id-card"></i> Profile</h3>

                <div class="avatar-upload-wrap">
                    <img id="settingsAvatar" class="avatar-preview" src="<?= $studentAvatar ?>" alt="Profile">
                    <label class="upload-label" for="profileUpload">
                        <i class="fa-solid fa-camera"></i> Change photo
                    </label>
                    <input type="file" id="profileUpload" accept="image/*" onchange="handleAvatarUpload(this)">
                </div>

                <label class="field-label" for="usernameInput">Display Name</label>
                <input class="settings-input" type="text" id="usernameInput" placeholder="Your name" value="<?= $studentName ?>">

                <label class="field-label">Admission Number</label>
                <input class="settings-input" type="text" id="admissionReadonly" readonly
                    value="<?= $studentAdmission ?>"
                    style="opacity:0.6;cursor:not-allowed;">

                <button class="settings-save" onclick="saveProfile()">
                    <i class="fa-solid fa-floppy-disk"></i> Save Profile
                </button>
            </div>

            <!-- Appearance -->
            <div class="settings-card">
                <h3><i class="fa-solid fa-palette"></i> Appearance</h3>

                <p style="font-size:14px;color:#666;line-height:1.7;margin-bottom:16px;">
                    Switch between light and dark mode to suit your preference
                    and reduce eye strain.
                </p>

                <button class="theme-toggle" onclick="toggleTheme()">
                    <i class="fa-solid fa-circle-half-stroke theme-toggle-icon"></i>
                    Toggle Dark / Light Mode
                </button>

                <div style="margin-top:28px;padding-top:20px;border-top:1px solid #f0eaf8;">
                    <h3 style="margin-bottom:8px;font-size:14px;">
                        <i class="fa-solid fa-right-from-bracket" style="color:#dc2626;"></i>
                        <span style="color:#dc2626;">Sign Out</span>
                    </h3>
                    <p style="font-size:13px;color:#888;line-height:1.6;margin-bottom:12px;">
                        You will be returned to the login screen.
                    </p>
                    <button onclick="signOut()" style="
                        padding:11px 22px;
                        background:#fff;
                        border:1.5px solid #fca5a5;
                        color:#dc2626;
                        border-radius:12px;
                        font-family:'Poppins',sans-serif;
                        font-size:13px;
                        font-weight:600;
                        cursor:pointer;
                        display:flex;
                        align-items:center;
                        gap:7px;
                        transition:background 0.2s;
                    " onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='#fff'">
                        <i class="fa-solid fa-right-from-bracket"></i> Sign Out
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>


<script>
/*TAB SWITCHING*/
function switchTab(id, btn) {
    document.querySelectorAll('.portal-page').forEach(p => p.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    if (btn) {
        document.querySelectorAll('.portal-tabs button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }
    window.scrollTo(0, 0);
    if (id === 'announcements') loadAnnouncements();
}

/* ANNOUNCEMENTS — platform-wide (admin/dean) + personal reminders from the Dean, merged into one feed */
function loadAnnouncements() {
    Promise.all([
        fetch('get_announcements.php').then(res => res.json()).catch(() => ({ success: false })),
        fetch('get_student_reminders.php').then(res => res.json()).catch(() => ({ success: false }))
    ]).then(([announcementData, reminderData]) => {
        const el = document.getElementById('announcementHistory');

        const items = [];
        if (announcementData.success) {
            announcementData.announcements.forEach(a => items.push({
                label: 'Announcement',
                from: `${a.sender_name} (${a.sender_role})`,
                message: a.message,
                date: a.created_at
            }));
        }
        if (reminderData.success) {
            reminderData.reminders.forEach(r => items.push({
                label: 'Announcement',
                from: r.dean_name,
                message: r.note,
                date: r.created_at
            }));
        }

        if (!items.length) {
            el.innerHTML = '<p style="font-size:13px;color:#888;">No announcements yet.</p>';
            return;
        }

        items.sort((a, b) => new Date(b.date) - new Date(a.date));

        el.innerHTML = items.map(i => `
            <div style="border-bottom:1px solid #eee;padding:10px 0;">
                <p style="font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:2px;">${i.label}</p>
                <p style="font-size:12px;color:#8b5cf6;font-weight:600;margin-bottom:4px;">
                    ${i.from} · ${new Date(i.date).toLocaleString()}
                </p>
                <p style="font-size:14px;color:#444;">${i.message}</p>
            </div>
        `).join('');
    }).catch(() => {
        document.getElementById('announcementHistory').innerHTML =
            '<p style="font-size:13px;color:#888;">Could not load announcements.</p>';
    });
}

function openCheckin() {
    window.location.href = 'student-checkin.html';
}

function signOut() {
    // Clear the PHP session server-side, then send to login
    fetch('logout.php').finally(() => {
        window.location.href = 'student-login.html';
    });
}

/*AFFIRMATIONS*/
const affirmations = [
    'You are stronger than your struggles.',
    'Progress matters more than perfection.',
    'You deserve care and kindness — always.',
    'Every day is a new beginning.',
    'Your wellbeing comes first.',
    'It is okay to rest. Rest is part of the journey.',
    'You have survived every hard day so far.',
    'Asking for help is a sign of strength.',
    'One breath at a time. You have got this.',
    'You are more than your grades or your worries.'
];

function loadAffirmation() {
    const el = document.getElementById('affirmationText');
    el.style.opacity = 0;
    setTimeout(() => {
        el.textContent = affirmations[Math.floor(Math.random() * affirmations.length)];
        el.style.transition = 'opacity 0.4s';
        el.style.opacity = 1;
    }, 150);
}

/*PROFILE*/
function handleAvatarUpload(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const src = e.target.result;
        // Local-only preview for now — wiring this to a real avatar-upload.php
        // (saving to students.avatar_url) is a good follow-up piece.
        ['profilePreview', 'settingsAvatar', 'navAvatar', 'greetingAvatar'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.src = src;
        });
    };
    reader.readAsDataURL(file);
}

function saveProfile() {
    // Local-only display update for now — wiring this to a real
    // update-profile.php (saving to students.full_name) is a good follow-up piece.
    const name = document.getElementById('usernameInput').value.trim();
    if (name) {
        document.getElementById('usernameDisplay').textContent = name;
        document.getElementById('greetingName').textContent = name;
    }
}

/*CHART*/
function loadHistoryGraph() {
    const history = JSON.parse(localStorage.getItem('checkinHistory') || '[]');
    if (history.length === 0) return;

    document.getElementById('noDataMsg').style.display = 'none';
    document.getElementById('chartWrap').style.display = 'block';

    const dates  = history.map(h => h.date);
    const scores = history.map(h => h.score);

    document.getElementById('latestScore').textContent =
        'Latest score: ' + scores[scores.length - 1] + '%';

    new Chart(document.getElementById('historyChart'), {
        type: 'bar',
        data: {
            labels: dates,
            datasets: [{
                label: 'Wellbeing Score (%)',
                data: scores,
                backgroundColor: 'rgba(139,92,246,0.25)',
                borderColor: '#8b5cf6',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true, max: 100,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { font: { family: 'Poppins' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Poppins' } }
                }
            }
        }
    });
}

/* Counselor Support — request flow */
function loadCounselorRequestStatus() {
    const el = document.getElementById('counselorRequestStatus');
    fetch('get_my_request.php')
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                el.innerHTML = '<p style="font-size:13px;color:#aaa;">Could not load request status.</p>';
                return;
            }

            const r = data.request;

            if (!r) {
                el.innerHTML = `
                    <p style="font-size:14px;color:#666;line-height:1.7;margin-bottom:16px;">
                        Want to talk to someone? Request a counselor and our admin team will match you with one.
                    </p>
                    <button class="checkin-btn" onclick="requestCounselor()">
                        <i class="fa-solid fa-hand-holding-heart"></i> Request a Counselor
                    </button>`;
                return;
            }

            if (r.status === 'pending') {
                el.innerHTML = `
                    <div class="mood-today" style="background:#fef3c7;color:#92400e;">
                        <i class="fa-solid fa-clock"></i>
                        Your request is pending — an admin will assign you a counselor soon.
                    </div>`;
            } else if (r.status === 'assigned') {
                el.innerHTML = `
                    <div class="mood-today" style="background:#f5f0ff;color:#6d28d9;">
                        <i class="fa-solid fa-user-clock"></i>
                        ${r.counselor_title || ''} ${r.counselor_name} has been assigned and is reviewing your request.
                    </div>`;
            } else if (r.status === 'accepted') {
                el.innerHTML = `
                    <div class="profile-row" style="margin-bottom:14px;">
                        <img class="profile-avatar" src="${r.counselor_avatar || 'https://i.pravatar.cc/60?img=33'}" alt="Counselor">
                        <div>
                            <p class="profile-name">${r.counselor_title || ''} ${r.counselor_name}</p>
                            <p class="profile-sub">Your assigned counselor</p>
                        </div>
                    </div>
                    <div class="mood-today" style="background:#d1fae5;color:#065f46;">
                        <i class="fa-solid fa-circle-check"></i> You're all set — reach out any time.
                    </div>`;
            } else if (r.status === 'declined') {
                el.innerHTML = `
                    <p style="font-size:14px;color:#666;line-height:1.7;margin-bottom:16px;">
                        Your previous request couldn't be matched. The admin team will reassign you shortly, or you can submit a new request.
                    </p>
                    <button class="checkin-btn" onclick="requestCounselor()">
                        <i class="fa-solid fa-hand-holding-heart"></i> Request a Counselor
                    </button>`;
            }
        })
        .catch(() => {
            el.innerHTML = '<p style="font-size:13px;color:#aaa;">Could not load request status.</p>';
        });
}

function requestCounselor() {
    const formData = new FormData();
    fetch('request_counselor.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadCounselorRequestStatus();
        })
        .catch(() => alert('Could not submit your request. Please try again.'));
}

/* Academic data — calls the real PHP endpoint backed by the grades/attendance tables */
function loadAcademicData() {
    fetch('get_students_academic.php')
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            document.getElementById('gradesTableBody').innerHTML =
                `<tr><td colspan="8" style="text-align:center;color:#aaa;padding:20px;">${data.message || 'No academic data available.'}</td></tr>`;
            document.getElementById('attendanceTableBody').innerHTML =
                `<tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px;">${data.message || 'No academic data available.'}</td></tr>`;
            return;
        }

        // Render grades table
        if (data.grades.length) {
            document.getElementById('gradesTableBody').innerHTML = data.grades.map(g => `
                <tr>
                    <td>${g.unit_code}</td>
                    <td>${g.unit_name}</td>
                    <td>${g.cat1}/15</td>
                    <td>${g.cat2}/15</td>
                    <td>${g.assignment}/10</td>
                    <td>${g.exam}/60</td>
                    <td><strong>${g.total}/100</strong></td>
                    <td class="${g.total < 40 ? 'fail' : 'pass'}">${g.grade_letter}</td>
                </tr>
            `).join('');
        } else {
            document.getElementById('gradesTableBody').innerHTML =
                '<tr><td colspan="8" style="text-align:center;color:#aaa;padding:20px;">No grades recorded yet this semester.</td></tr>';
        }

        // Render attendance table
        if (data.attendance.length) {
            document.getElementById('attendanceTableBody').innerHTML = data.attendance.map(a => `
                <tr>
                    <td>${a.unit_code}</td>
                    <td>${a.unit_name}</td>
                    <td>${a.total_classes}</td>
                    <td>${a.attended}</td>
                    <td>${a.absences}</td>
                    <td class="${a.absence_pct >= 40 ? 'fail' : 'pass'}">${a.absence_pct}%</td>
                </tr>
            `).join('');
        } else {
            document.getElementById('attendanceTableBody').innerHTML =
                '<tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px;">No attendance recorded yet this semester.</td></tr>';
        }

        // Show warning if failing or high absence
        if (data.failing_count > 0 || data.at_risk_attendance > 0) {
            document.getElementById('academicWarning').style.display = 'block';
        }
    })
    .catch(() => {
        document.getElementById('gradesTableBody').innerHTML =
            '<tr><td colspan="8" style="text-align:center;color:#aaa;padding:20px;">Could not load grades right now.</td></tr>';
        document.getElementById('attendanceTableBody').innerHTML =
            '<tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px;">Could not load attendance right now.</td></tr>';
    });
}

/*JOURNAL*/
function selectMood(btn) {
    document.querySelectorAll('#moodChips .chip').forEach(c => c.classList.remove('selected'));
    btn.classList.add('selected');
}

function usePrompt(li) {
    const text = li.textContent.trim().replace(/^→\s*/, '');
    document.getElementById('journalEntry').value = text + '\n\n';
    document.getElementById('journalEntry').focus();
}

function saveJournal() {
    const entry = document.getElementById('journalEntry').value.trim();
    if (!entry) return;
    document.getElementById('journalEntry').value = '';
    document.querySelectorAll('#moodChips .chip').forEach(c => c.classList.remove('selected'));
    const confirm = document.getElementById('saveConfirm');
    confirm.style.display = 'flex';
    setTimeout(() => confirm.style.display = 'none', 3000);
}

/*SUBSTANCE-FREE TRACKER — private, stored only in this browser*/
const SOBRIETY_MESSAGES = [
    'Every minute clean is a minute you chose yourself. Keep going.',
    'Progress isn\'t always loud — showing up today counts.',
    'You are stronger than the urge. One moment at a time.',
    'Healing isn\'t linear, and that\'s okay. You\'re still moving forward.',
    'Be proud of how far you\'ve come, not just how far you have to go.',
    'Your future self is grateful for the choice you\'re making today.'
];

function sobrietyBreakdown(startIso) {
    const ms = Date.now() - new Date(startIso).getTime();
    const totalMinutes = Math.max(0, Math.floor(ms / 60000));
    const minutes = totalMinutes % 60;
    const totalHours = Math.floor(totalMinutes / 60);
    const hours = totalHours % 24;
    const totalDays = Math.floor(totalHours / 24);
    const months = Math.floor(totalDays / 30);
    const daysAfterMonths = totalDays % 30;
    const weeks = Math.floor(daysAfterMonths / 7);
    const days = daysAfterMonths % 7;
    return { months, weeks, days, hours, minutes };
}

function renderSobrietyCounter(overrideMessage) {
    const startIso = localStorage.getItem('sobrietyStartDate');
    const area = document.getElementById('sobrietyArea');

    if (!startIso) {
        area.innerHTML = `
            <p style="font-size:12.5px;color:#444;margin-bottom:10px;">Do you drink alcohol or use drugs?</p>
            <div style="display:flex;gap:8px;">
                <button class="sobriety-ask-btn" style="background:#10b981;" onclick="startSobrietyTracker('no')">No</button>
                <button class="sobriety-ask-btn" style="background:#8b5cf6;" onclick="startSobrietyTracker('yes')">Yes</button>
            </div>
        `;
        return;
    }

    const t = sobrietyBreakdown(startIso);
    const message = overrideMessage || SOBRIETY_MESSAGES[Math.floor(Math.random() * SOBRIETY_MESSAGES.length)];

    area.innerHTML = `
        <div class="sobriety-stats">
            <div class="sobriety-unit"><span>${t.months}</span>mo</div>
            <div class="sobriety-unit"><span>${t.weeks}</span>wk</div>
            <div class="sobriety-unit"><span>${t.days}</span>d</div>
            <div class="sobriety-unit"><span>${t.hours}</span>h</div>
            <div class="sobriety-unit"><span>${t.minutes}</span>m</div>
        </div>
        <p class="sobriety-label">substance-free</p>
        <p class="sobriety-message">${message}</p>
        <button class="redo-btn" onclick="resetSobrietyTracker()">
            <i class="fa-solid fa-rotate-left"></i> I used recently — reset
        </button>
    `;
}

function startSobrietyTracker(answer) {
    localStorage.setItem('sobrietyStartDate', new Date().toISOString());
    const message = answer === 'yes'
        ? 'It takes courage to be honest. Today can be day one — you\'ve got this.'
        : 'Great! Let\'s start tracking your substance-free journey from today.';
    renderSobrietyCounter(message);
}

function resetSobrietyTracker() {
    if (!confirm('Reset your counter back to zero? That\'s okay — every day is a new start.')) return;
    localStorage.setItem('sobrietyStartDate', new Date().toISOString());
    renderSobrietyCounter('It\'s okay. Recovery isn\'t a straight line — today is a fresh start.');
}

function loadSobrietyTracker() {
    renderSobrietyCounter();
    setInterval(() => {
        if (localStorage.getItem('sobrietyStartDate') && document.getElementById('sobrietyArea')) {
            const startIso = localStorage.getItem('sobrietyStartDate');
            const t = sobrietyBreakdown(startIso);
            const unitEls = document.querySelectorAll('.sobriety-unit span');
            if (unitEls.length === 5) {
                unitEls[0].textContent = t.months;
                unitEls[1].textContent = t.weeks;
                unitEls[2].textContent = t.days;
                unitEls[3].textContent = t.hours;
                unitEls[4].textContent = t.minutes;
            }
        }
    }, 60000);
}

/*INIT — no more sessionStorage auth check needed, PHP already gated the page*/
window.onload = function () {
    loadAffirmation();
    loadHistoryGraph();
    loadAcademicData();
    loadCounselorRequestStatus();
    loadSobrietyTracker();
};
</script>
<script src="theme.js"></script>

</body>
</html>