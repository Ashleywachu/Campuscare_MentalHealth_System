<?php
session_start();

// Server-side auth gate
if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$adminName = htmlspecialchars($_SESSION['adminName'] ?? 'Admin User');
$adminEmail = htmlspecialchars($_SESSION['adminEmail'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Portal | CampusCare</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<link rel="stylesheet" href="adminportal.css">
</head>

<body>

<!--TOP NAVBAR -->

<nav class="navbar">

    <div class="brand">
        <i class="fa-solid fa-shield-halved"></i>
        <div>
            <h2>CampusCare</h2>
            <span>Admin Console</span>
        </div>
    </div>

    <ul class="nav-links">
        <li class="active" data-target="dashboard" tabindex="0"><i class="fa-solid fa-gauge-high"></i><span class="label">Dashboard</span></li>
        <li data-target="users" tabindex="0"><i class="fa-solid fa-users-gear"></i><span class="label">Users</span></li>
        <li data-target="logins" tabindex="0"><i class="fa-solid fa-right-to-bracket"></i><span class="label">Logins</span></li>
        <li data-target="assignments" tabindex="0"><i class="fa-solid fa-people-arrows"></i><span class="label">Assign</span></li>
        <li data-target="monitoring" tabindex="0"><i class="fa-solid fa-server"></i><span class="label">Monitoring</span></li>
        <li data-target="resources" tabindex="0"><i class="fa-solid fa-folder-open"></i><span class="label">Resources</span></li>
        <li data-target="reports" tabindex="0"><i class="fa-solid fa-chart-pie"></i><span class="label">Reports</span></li>
        <li data-target="backup" tabindex="0"><i class="fa-solid fa-database"></i><span class="label">Backup</span></li>
        <li data-target="announcements" tabindex="0"><i class="fa-solid fa-bullhorn"></i><span class="label">Announce</span></li>
        <li data-target="messages" tabindex="0"><i class="fa-solid fa-envelope-open-text"></i><span class="label">Messages</span></li>
        <li data-target="profile" tabindex="0"><i class="fa-solid fa-user-gear"></i><span class="label">Profile</span></li>
    </ul>

    <div class="nav-logout" onclick="toggleTheme()" tabindex="0" title="Toggle dark / light mode">
        <i class="fa-solid fa-moon theme-toggle-icon"></i><span class="label">Theme</span>
    </div>

    <div class="nav-logout" onclick="logout()" tabindex="0">
        <i class="fa-solid fa-arrow-right-from-bracket"></i><span class="label">Logout</span>
    </div>

</nav>

<!--MAIN CONTENT-->
<div class="main">

    <div class="topbar">
        <div>
            <h1 id="pageTitle">Dashboard</h1>
            <p class="subtitle" id="pageSubtitle">A live overview of CampusCare activity.</p>
        </div>
        <div class="clock" id="clock"></div>
    </div>

    <!--DASHBOARD-->
    <section id="dashboard" class="content active">

        <div class="cards">
            <div class="card">
                <div class="label">Total Students</div>
                <div class="value" id="statStudents">0</div>
            </div>
            <div class="card">
                <div class="label">Total Counselors</div>
                <div class="value" id="statCounselors">0</div>
            </div>
            <div class="card">
                <div class="label">Active Accounts</div>
                <div class="value good" id="statActive">0</div>
            </div>
            <div class="card">
                <div class="label">Deactivated Accounts</div>
                <div class="value bad" id="statDeactivated">0</div>
            </div>
            <div class="card">
                <div class="label">Pending Requests</div>
                <div class="value warn" id="statPending">0</div>
            </div>
        </div>

        <div class="panel-row">

            <div class="panel">
                <h3>Account Status</h3>
                <p class="panel-sub">Active vs. deactivated accounts across the platform.</p>
                <div class="donut-wrap">
                    <svg viewBox="0 0 160 160" class="donut-svg">
                        <circle class="donut-ring donut-track" cx="80" cy="80" r="62"></circle>
                        <circle class="donut-ring donut-active" id="donutActive" cx="80" cy="80" r="62"></circle>
                        <circle class="donut-ring donut-deactivated" id="donutDeactivated" cx="80" cy="80" r="62"></circle>
                        <text x="80" y="76" class="donut-center" id="donutTotal">0</text>
                        <text x="80" y="93" class="donut-center-label">TOTAL USERS</text>
                    </svg>
                    <div class="legend">
                        <div class="legend-item"><span class="legend-dot" style="background:var(--accent)"></span> Active <span class="count" id="legendActive">0</span></div>
                        <div class="legend-item"><span class="legend-dot" style="background:#b794f4"></span> Deactivated <span class="count" id="legendDeactivated">0</span></div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <h3>Weekly Active Users</h3>
                <p class="panel-sub">Logins recorded over the last five days.</p>
                <canvas id="systemChart"></canvas>
            </div>

        </div>

        <div class="alert">
            <h3>Security alert</h3>
            <p>3 unauthorized login attempts were detected in the last 24 hours.</p>
            <button class="btn danger small" onclick="showSection('logins')">View login activity</button>
        </div>

    </section>

    <!--USER MANAGEMENT-->
    <section id="users" class="content">

        <div class="panel">
            <p class="panel-sub" style="margin-bottom:14px;">Every student, counselor, dean, and admin account currently in the database.</p>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody"></tbody>
                </table>
            </div>
        </div>

    </section>

    <!--LOGIN ACTIVITY-->
    <section id="logins" class="content">

        <div class="cards">
            <div class="card">
                <div class="label">Logins Today</div>
                <div class="value" id="statLoginsToday">0</div>
            </div>
            <div class="card">
                <div class="label">Failed Attempts (24h)</div>
                <div class="value bad">3</div>
            </div>
        </div>

        <div class="panel">
            <h3>Recent logins</h3>
            <p class="panel-sub">Who signed in, and when.</p>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Signed in</th>
                            <th>Device</th>
                        </tr>
                    </thead>
                    <tbody id="loginTableBody"></tbody>
                </table>
            </div>
        </div>

    </section>

    <!--COUNSELOR ASSIGNMENT (live data) -->
    <section id="assignments" class="content">

        <div class="panel">
            <h3>Pending requests</h3>
            <p class="panel-sub">Students waiting to be matched with a counselor.</p>
            <div class="table-wrap" id="pendingRequestsWrap">
                <p style="font-size:13px;color:var(--text-muted);padding:8px 0;">Loading requests…</p>
            </div>
        </div>

        <div class="panel">
            <h3>All requests</h3>
            <p class="panel-sub">Full history — pending, assigned, accepted, and declined.</p>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Counselor</th>
                            <th>Requested</th>
                        </tr>
                    </thead>
                    <tbody id="allRequestsBody">
                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </section>

    <!--SYSTEM MONITORING-->
    <section id="monitoring" class="content">

        <div class="cards">
            <div class="card">
                <div class="label">Server Status</div>
                <div class="value good" style="font-size:18px">Operational</div>
            </div>
            <div class="card">
                <div class="label">Database Health</div>
                <div class="value good" style="font-size:18px">Stable</div>
            </div>
            <div class="card">
                <div class="label">Login Activity</div>
                <div class="value good" style="font-size:18px">Normal</div>
            </div>
        </div>

        <div class="panel">
            <h3>Uptime, last 5 days</h3>
            <p class="panel-sub">No incidents recorded in this window.</p>
            <canvas id="uptimeChart"></canvas>
        </div>

    </section>

    <!--RESOURCE MANAGEMENT-->
    <section id="resources" class="content">

        <div class="panel">
            <h3>Existing resources</h3>
            <p class="panel-sub">Edit or remove what's already published to students.</p>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Added</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="resourceTableBody"></tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <h3>Upload a new resource</h3>
            <p class="panel-sub">Share a guide, video, or worksheet with students.</p>

            <label>Title</label>
            <input type="text" id="resourceTitle" placeholder="e.g. Managing exam stress">

            <label>Type</label>
            <select id="resourceType">
                <option>Article</option>
                <option>Video</option>
                <option>Worksheet</option>
                <option>Audio</option>
            </select>

            <label>File</label>
            <input type="file" id="resourceFile">

            <div class="btn-row" style="margin-top:18px">
                <button class="btn primary" onclick="uploadResource()">Upload resource</button>
            </div>
        </div>

    </section>

    <!--REPORTS-->
    <section id="reports" class="content">

        <div class="panel">
            <h3>Generate a report</h3>
            <p class="panel-sub">Pull a snapshot of current platform activity.</p>

            <div class="btn-row">
                <button class="btn primary" onclick="generateReport()"><i class="fa-solid fa-chart-pie"></i>&nbsp; Generate usage report</button>
                <button class="btn ghost" onclick="exportReport()"><i class="fa-solid fa-print"></i>&nbsp; Export / print</button>
            </div>

            <div class="report-preview" id="reportPreview">
                <div class="row"><span>Total accounts</span><span id="repTotal">0</span></div>
                <div class="row"><span>Active accounts</span><span id="repActive">0</span></div>
                <div class="row"><span>Deactivated accounts</span><span id="repDeactivated">0</span></div>
                <div class="row"><span>Logins today</span><span id="repLogins">0</span></div>
                <div class="row"><span>Pending requests</span><span id="repPending">0</span></div>
                <div class="row"><span>Report generated</span><span id="repTime">—</span></div>
            </div>
        </div>

    </section>

    <!--BACKUP-->
    <section id="backup" class="content">

        <div class="panel">
            <h3>Database backup &amp; recovery</h3>
            <p class="panel-sub" id="backupStatus">Last backup: Jun 17, 2026 — 11:40 PM</p>

            <div class="btn-row">
                <button class="btn primary" onclick="createBackup()">Create backup</button>
                <button class="btn ghost" onclick="restoreBackup()">Restore database</button>
            </div>
        </div>

    </section>

    <!--ANNOUNCEMENTS-->
    <section id="announcements" class="content">

        <div class="panel">
            <h3>Send an announcement</h3>
            <p class="panel-sub">This goes to every student and counselor on the platform.</p>

            <textarea id="announcementText" rows="4" placeholder="Write your announcement..."></textarea>

            <div class="btn-row" style="margin-top:18px">
                <button class="btn primary" onclick="sendAnnouncement()">Send notification</button>
            </div>

            <div id="announcementHistory"></div>
        </div>

    </section>

    <!--CONTACT MESSAGES-->
    <section id="messages" class="content">

        <div class="panel">
            <h3>Messages from the public Contact page</h3>
            <p class="panel-sub">Also visible to the Dean of Students.</p>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Received</th>
                        </tr>
                    </thead>
                    <tbody id="contactMessagesBody">
                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </section>

    <!--PROFILE-->
    <section id="profile" class="content">

        <div class="panel" style="max-width:480px">
            <h3>Admin profile</h3>

            <label>Full name</label>
            <input type="text" value="<?= $adminName ?>">

            <label>Email address</label>
            <input type="email" value="<?= $adminEmail ?>">

            <label>New password</label>
            <input type="password" placeholder="Leave blank to keep current password">

            <div class="btn-row" style="margin-top:18px">
                <button class="btn primary" onclick="alert('Profile updated.')">Update profile</button>
            </div>
        </div>

    </section>

</div>

<!-- All behavior now lives in its own file -->
<script src="script.js"></script>
<script src="theme.js"></script>

</body>
</html>