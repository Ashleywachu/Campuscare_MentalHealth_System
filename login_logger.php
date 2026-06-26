<?php
// Shared by every *-login.php script — records every login attempt (success or failure)
// so the admin portal's Login Activity tab can show real, live data.

function detect_device_label() {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

    if (stripos($ua, 'Edg/') !== false) $browser = 'Edge';
    elseif (stripos($ua, 'Chrome') !== false) $browser = 'Chrome';
    elseif (stripos($ua, 'Firefox') !== false) $browser = 'Firefox';
    elseif (stripos($ua, 'Safari') !== false) $browser = 'Safari';
    else $browser = 'Unknown browser';

    if (stripos($ua, 'Android') !== false) $os = 'Android';
    elseif (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false) $os = 'iOS';
    elseif (stripos($ua, 'Mac OS') !== false) $os = 'macOS';
    elseif (stripos($ua, 'Windows') !== false) $os = 'Windows';
    elseif (stripos($ua, 'Linux') !== false) $os = 'Linux';
    else $os = 'Unknown OS';

    return "$browser · $os";
}

function log_login_attempt($pdo, $role, $identifier, $display_name, $success) {
    $stmt = $pdo->prepare("
        INSERT INTO login_logs (role, identifier, display_name, success, device)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$role, $identifier, $display_name, $success ? 1 : 0, detect_device_label()]);
}
