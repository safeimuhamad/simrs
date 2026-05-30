<?php

function asset($path)
{
    return baseUrl() . '/assets/' . ltrim($path, '/');
}

function uploadAsset($path)
{
    return baseUrl() . '/uploads/' . ltrim($path, '/');
}

function url($page, $params = [])
{
    $page = (string) $page;

    if (str_starts_with($page, '?')) {
        parse_str(ltrim($page, '?'), $legacyParams);
        $page = (string) ($legacyParams['page'] ?? '');
        unset($legacyParams['page']);
        $params = array_merge($legacyParams, $params);
    }

    $url = baseUrl() . '/' . trim($page, '/');

    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    return $url;
}

function baseUrl()
{
    $config = require __DIR__ . '/../../config/app.php';
    $configuredUrl = rtrim($config['app_url'] ?? '', '/');
    $host = strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? ''));

    if ($host !== '' && in_array($host, $config['allowed_hosts'] ?? [], true)) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            ? 'https'
            : 'http';

        $path = $config['host_paths'][$host] ?? '';

        return $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? $host) . $path;
    }

    return $configuredUrl;
}

function fullUrl($page, $params = [])
{
    return url($page, $params);
}

function currentPage()
{
    return $_GET['page'] ?? 'simrs-dashboard';
}

function isCurrentPage($page)
{
    $current = $_GET['page'] ?? 'home';

    return $current === $page ? 'current' : '';
}

function isActiveMenu($pages = [])
{
    return in_array(currentPage(), (array) $pages, true) ? 'active' : '';
}

function isOpenMenu($pages = [])
{
    return in_array(currentPage(), (array) $pages, true) ? 'open' : '';
}

function user_role()
{
    return $_SESSION['role_name'] ?? $_SESSION['user_role'] ?? null;
}

function role_name()
{
    return $_SESSION['role_name'] ?? $_SESSION['user_role'] ?? null;
}

function roleDashboardRoute($role = null, ?array $permissions = null)
{
    $role = $role ?: role_name();
    $permissions = $permissions ?? ($_SESSION['permissions'] ?? []);

    $routes = [
        'super_admin' => 'simrs-dashboard',
        'admin_rs' => 'simrs-dashboard',
        'pendaftaran' => 'dashboard-pendaftaran',
        'dokter' => 'dashboard-dokter',
        'perawat' => 'dashboard-perawat',
        'farmasi' => 'dashboard-farmasi',
        'kasir' => 'dashboard-kasir',
        'finance' => 'dashboard-finance',
        'manajemen' => 'dashboard-manajemen',
        'parking_admin' => 'parking-dashboard',
        'parking_operator' => 'parking-dashboard',
    ];

    if ($role && isset($routes[$role])) {
        return $routes[$role];
    }

    if (in_array('simrs_dashboard.view', $permissions, true)) {
        return 'simrs-dashboard';
    }

    if (in_array('parking_dashboard.view', $permissions, true)) {
        return 'parking-dashboard';
    }

    if (in_array('simrs_queue.manage', $permissions, true)) {
        return 'dashboard-perawat';
    }

    if (in_array('simrs_pharmacy.manage', $permissions, true)) {
        return 'dashboard-farmasi';
    }

    if (in_array('simrs_cashier.manage', $permissions, true)) {
        return 'dashboard-kasir';
    }

    return 'login';
}

function is_super_admin()
{
    return role_name() === 'super_admin';
}

function can_access($roles = [])
{
    return in_array(role_name(), (array) $roles, true);
}

function can($permissionKey)
{
    if (is_super_admin()) {
        return true;
    }

    if (empty($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
        return false;
    }

    return in_array($permissionKey, $_SESSION['permissions'], true);
}

function cannot($permissionKey)
{
    return !can($permissionKey);
}

function canAny(array $permissions)
{
    if (is_super_admin()) {
        return true;
    }

    foreach ($permissions as $permission) {
        if (can($permission)) {
            return true;
        }
    }

    return false;
}

function sanitizeRichHtml($html)
{
    $html = (string) $html;
    $allowedTags = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'blockquote', 'a', 'img'];

    if (!class_exists('DOMDocument')) {
        $html = preg_replace('#<(script|iframe|object|embed|style|svg|form)\b[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('/\son\w+\s*=\s*([\'"]).*?\1/is', '', $html);
        $html = preg_replace('/\s(href|src)\s*=\s*([\'"])\s*(javascript|data):.*?\2/is', '', $html);
        return strip_tags($html, '<' . implode('><', $allowedTags) . '>');
    }

    $document = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $document->loadHTML(
        '<?xml encoding="UTF-8"><div id="rich-content-root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    $root = $document->getElementById('rich-content-root');

    if (!$root) {
        return '';
    }

    sanitizeRichNode($root, $allowedTags);

    $safeHtml = '';
    foreach ($root->childNodes as $child) {
        $safeHtml .= $document->saveHTML($child);
    }

    return $safeHtml;
}

function safeLinkUrl($url, $fallback = '#')
{
    $url = trim((string) $url);

    if ($url !== '' && preg_match('~^(https?://|mailto:|tel:|/|#)~i', $url)) {
        return $url;
    }

    return $fallback;
}

function consumeRateLimit($key, $maximumAttempts, $windowSeconds)
{
    $now = time();
    $attempts = $_SESSION['_rate_limits'][$key] ?? [];
    $attempts = array_values(array_filter($attempts, function ($attempt) use ($now, $windowSeconds) {
        return (int) $attempt > $now - $windowSeconds;
    }));

    if (count($attempts) >= $maximumAttempts) {
        $_SESSION['_rate_limits'][$key] = $attempts;
        return false;
    }

    $attempts[] = $now;
    $_SESSION['_rate_limits'][$key] = $attempts;
    return true;
}

function clearRateLimit($key)
{
    unset($_SESSION['_rate_limits'][$key]);
}

function sanitizeRichNode($parent, array $allowedTags)
{
    $blockedTags = ['script', 'iframe', 'object', 'embed', 'style', 'svg', 'math', 'form', 'input', 'button', 'link', 'meta'];

    foreach (iterator_to_array($parent->childNodes) as $node) {
        if ($node->nodeType !== XML_ELEMENT_NODE) {
            continue;
        }

        $tag = strtolower($node->nodeName);

        if (in_array($tag, $blockedTags, true)) {
            $parent->removeChild($node);
            continue;
        }

        if (!in_array($tag, $allowedTags, true)) {
            while ($node->firstChild) {
                $parent->insertBefore($node->firstChild, $node);
            }
            $parent->removeChild($node);
            sanitizeRichNode($parent, $allowedTags);
            continue;
        }

        $attributes = [];
        foreach ($node->attributes as $attribute) {
            $attributes[] = $attribute->nodeName;
        }

        foreach ($attributes as $attribute) {
            $allowedAttribute = ($tag === 'a' && in_array($attribute, ['href', 'title', 'target'], true))
                || ($tag === 'img' && in_array($attribute, ['src', 'alt', 'title'], true));

            if (!$allowedAttribute) {
                $node->removeAttribute($attribute);
            }
        }

        foreach (['href', 'src'] as $urlAttribute) {
            if (!$node->hasAttribute($urlAttribute)) {
                continue;
            }

            $value = trim($node->getAttribute($urlAttribute));
            if (!preg_match('~^(https?://|mailto:|/|#)~i', $value)) {
                $node->removeAttribute($urlAttribute);
            }
        }

        if ($tag === 'a' && strtolower($node->getAttribute('target')) === '_blank') {
            $node->setAttribute('rel', 'noopener noreferrer');
        }

        sanitizeRichNode($node, $allowedTags);
    }
}

function sentenceCaseText($text)
{
    $text = trim((string) $text);

    if ($text === '') {
        return $text;
    }

    $lower = function_exists('mb_strtolower')
        ? mb_strtolower($text, 'UTF-8')
        : strtolower($text);

    $first = function_exists('mb_substr')
        ? mb_strtoupper(mb_substr($lower, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($lower, 1, null, 'UTF-8')
        : ucfirst($lower);

    $preserve = [
        'whatsapp' => 'WhatsApp',
        'qr code' => 'QR Code',
        'q&a' => 'Q&A',
        'faq' => 'FAQ',
        'cms' => 'CMS',
    ];

    return str_ireplace(array_keys($preserve), array_values($preserve), $first);
}

function requirePermission($permissionKey)
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . url('login'));
        exit;
    }

    if (!can($permissionKey)) {
        $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman tersebut.';
        http_response_code(403);
        echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>Akses Ditolak</title><style>body{font-family:Arial,sans-serif;background:#f5f8fb;color:#102a43;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}.box{background:#fff;border:1px solid #e5edf5;border-radius:14px;padding:32px;max-width:480px;text-align:center;box-shadow:0 18px 45px rgba(16,42,67,.08)}a{display:inline-block;margin-top:16px;background:#0d6efd;color:#fff;text-decoration:none;padding:10px 18px;border-radius:8px}</style></head><body>';
        echo '<div class="box"><h1>Akses Ditolak</h1><p>Akun Anda belum memiliki permission untuk membuka halaman ini. Silakan login dengan role yang sesuai.</p><a href="' . htmlspecialchars(url('logout')) . '">Login ulang</a></div>';
        echo '</body></html>';
        exit;
    }
}

function csrfToken()
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf_token'];
}

function csrfField()
{
    return '<input type="hidden" name="_csrf" value="' .
        htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function protectPostForms($html)
{
    return preg_replace_callback('/<form\b[^>]*>/i', function ($match) {
        if (!preg_match('/\bmethod\s*=\s*([\'"]?)post\1/i', $match[0])) {
            return $match[0];
        }

        return $match[0] . csrfField();
    }, $html);
}

function verifyCsrfRequest()
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }

    $route = trim((string) ($_GET['route'] ?? $_GET['page'] ?? ''), '/');
    if (str_starts_with($route, 'api/mobile') || str_starts_with($route, 'parking-api/')) {
        return;
    }

    $submitted = $_POST['_csrf'] ?? '';
    $expected = $_SESSION['_csrf_token'] ?? '';

    if ($expected === '' || $submitted === '' || !hash_equals($expected, $submitted)) {
        http_response_code(403);
        echo 'Permintaan tidak valid atau sesi telah berakhir. Silakan muat ulang halaman.';
        exit;
    }
}

function requirePost()
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        echo 'Metode request tidak diizinkan.';
        exit;
    }
}

function validatedImageExtension($file)
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK ||
        empty($file['tmp_name']) ||
        !is_uploaded_file($file['tmp_name'])) {
        return null;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $types = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif'
    ];

    return $types[$mime] ?? null;
}

function validatedDocumentUpload($file)
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK ||
        empty($file['tmp_name']) ||
        !is_uploaded_file($file['tmp_name'])) {
        return null;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $types = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($types[$mime])) {
        return null;
    }

    return ['extension' => $types[$mime], 'mime' => $mime];
}

function activity_log($module, $action, $description, $referenceId = null, $referenceNumber = null)
{
    if (empty($_SESSION['user_id'])) {
        return;
    }

    if (!class_exists('ActivityLog')) {
        return;
    }

    $model = new ActivityLog();

    $model->create([
        'user_id' => $_SESSION['user_id'],
        'module' => $module,
        'action' => $action,
        'reference_id' => $referenceId,
        'reference_number' => $referenceNumber,
        'description' => $description
    ]);
}

function simrsStatusBadge($status, $context = 'general')
{
    $status = trim((string) $status);
    if ($status === '') {
        $status = '-';
    }

    $key = strtolower($status);
    $labels = [
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
        'pending' => 'Pending',
        'verified' => 'Terverifikasi',
        'prepared' => 'Disiapkan',
        'dispensed' => 'Diserahkan',
        'cancelled' => 'Dibatalkan',
        'registered' => 'Terdaftar',
        'waiting' => 'Menunggu',
        'called' => 'Dipanggil',
        'serving' => 'Dilayani',
        'in_service' => 'Dilayani',
        'in_consultation' => 'Pemeriksaan',
        'pharmacy' => 'Farmasi',
        'billing' => 'Billing',
        'paid' => 'Lunas',
        'completed' => 'Selesai',
        'done' => 'Selesai',
        'draft' => 'Draft',
        'unpaid' => 'Belum Bayar',
        'partial' => 'Sebagian',
        'lost_ticket' => 'Tiket Hilang',
        'waived' => 'Digratiskan',
        'refunded' => 'Refund',
        'result_ready' => 'Hasil Siap',
        'ordered' => 'Dipesan',
        'in_progress' => 'Diproses',
        'maintenance' => 'Maintenance',
        'expired' => 'Expired',
    ];

    $classes = [
        'active' => 'bg-success bg-opacity-10 text-success',
        'paid' => 'bg-success bg-opacity-10 text-success',
        'completed' => 'bg-success bg-opacity-10 text-success',
        'done' => 'bg-success bg-opacity-10 text-success',
        'verified' => 'bg-success bg-opacity-10 text-success',
        'dispensed' => 'bg-success bg-opacity-10 text-success',
        'result_ready' => 'bg-success bg-opacity-10 text-success',
        'prepared' => 'bg-info bg-opacity-10 text-info',
        'called' => 'bg-info bg-opacity-10 text-info',
        'serving' => 'bg-info bg-opacity-10 text-info',
        'in_service' => 'bg-info bg-opacity-10 text-info',
        'in_consultation' => 'bg-info bg-opacity-10 text-info',
        'billing' => 'bg-warning bg-opacity-10 text-warning',
        'partial' => 'bg-warning bg-opacity-10 text-warning',
        'waiting' => 'bg-warning bg-opacity-10 text-warning',
        'pending' => 'bg-warning bg-opacity-10 text-warning',
        'ordered' => 'bg-warning bg-opacity-10 text-warning',
        'unpaid' => 'bg-warning bg-opacity-10 text-warning',
        'registered' => 'bg-primary bg-opacity-10 text-primary',
        'pharmacy' => 'bg-primary bg-opacity-10 text-primary',
        'draft' => 'bg-secondary bg-opacity-10 text-secondary',
        'inactive' => 'bg-danger bg-opacity-10 text-danger',
        'cancelled' => 'bg-danger bg-opacity-10 text-danger',
        'lost_ticket' => 'bg-danger bg-opacity-10 text-danger',
        'expired' => 'bg-danger bg-opacity-10 text-danger',
        'refunded' => 'bg-danger bg-opacity-10 text-danger',
        'waived' => 'bg-secondary bg-opacity-10 text-secondary',
        'maintenance' => 'bg-warning bg-opacity-10 text-warning',
    ];

    if (($context === 'payment' || $context === 'parking') && $key === 'active') {
        $classes[$key] = 'bg-primary bg-opacity-10 text-primary';
    }

    $label = $labels[$key] ?? ucwords(str_replace('_', ' ', $status));
    $class = $classes[$key] ?? 'bg-light text-body';

    return '<span class="default-badge ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '">' .
        htmlspecialchars($label, ENT_QUOTES, 'UTF-8') .
        '</span>';
}

function simrsStatusBadgeGroup(array $statuses)
{
    $badges = [];
    $seen = [];

    foreach ($statuses as $status) {
        $value = trim((string) ($status['value'] ?? ''));
        if ($value === '') {
            continue;
        }

        $key = strtolower($value);
        if (isset($seen[$key])) {
            continue;
        }

        $seen[$key] = true;
        $badges[] = simrsStatusBadge($value, $status['context'] ?? 'general');
    }

    if (empty($badges)) {
        return simrsStatusBadge('-', 'general');
    }

    return '<div class="d-flex flex-wrap gap-1">' . implode('', $badges) . '</div>';
}

function adminListFooter($route, $search, $currentPage, $totalPages, $totalData, $limit)
{
    $currentPage = max(1, (int) $currentPage);
    $totalPages = max(1, (int) $totalPages);
    $totalData = (int) $totalData;
    $limit = max(1, (int) $limit);
    $params = $_GET;
    unset($params['page'], $params['route'], $params['p'], $params['partial']);
    if ((string) $search !== '') {
        $params['search'] = (string) $search;
    } else {
        unset($params['search']);
    }
    $queryString = '?' . http_build_query($params);
    if ($queryString === '?') {
        $queryString = '';
    }
    $separator = $queryString === '' ? '?' : '&';
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);

    ob_start();
    ?>
    <div class="d-flex justify-content-center justify-content-sm-between align-items-center text-center flex-wrap gap-2 showing-wrap pt-15 p-20 border-top">
        <span class="fs-15">
            Showing
            <?= $totalData > 0 ? (($currentPage - 1) * $limit + 1) : 0 ?>
            to
            <?= min($currentPage * $limit, $totalData) ?>
            of
            <?= $totalData ?> entries
        </span>

        <?php if ($totalPages > 1): ?>
            <nav class="custom-pagination">
                <ul class="pagination mb-0 justify-content-center">
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <?php if ($currentPage <= 1): ?>
                            <span class="page-link icon"><i class="material-symbols-outlined">west</i></span>
                        <?php else: ?>
                            <a class="page-link icon" href="<?= url($route) . $queryString . $separator ?>p=<?= $currentPage - 1 ?>">
                                <i class="material-symbols-outlined">west</i>
                            </a>
                        <?php endif; ?>
                    </li>

                    <?php if ($startPage > 1): ?>
                        <li class="page-item"><a class="page-link" href="<?= url($route) . $queryString . $separator ?>p=1">1</a></li>
                        <?php if ($startPage > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item">
                            <a class="page-link <?= $currentPage === $i ? 'active' : '' ?>" href="<?= url($route) . $queryString . $separator ?>p=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                        <li class="page-item"><a class="page-link" href="<?= url($route) . $queryString . $separator ?>p=<?= $totalPages ?>"><?= $totalPages ?></a></li>
                    <?php endif; ?>

                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <?php if ($currentPage >= $totalPages): ?>
                            <span class="page-link icon"><i class="material-symbols-outlined">east</i></span>
                        <?php else: ?>
                            <a class="page-link icon" href="<?= url($route) . $queryString . $separator ?>p=<?= $currentPage + 1 ?>">
                                <i class="material-symbols-outlined">east</i>
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

function paginateRows(array $rows, $route, $search = '', $limit = 10)
{
    $search = trim((string) $search);
    if ($search !== '') {
        $needle = strtolower($search);
        $rows = array_values(array_filter($rows, function ($row) use ($needle) {
            foreach ((array) $row as $value) {
                if (is_scalar($value) && str_contains(strtolower((string) $value), $needle)) {
                    return true;
                }
            }

            return false;
        }));
    }

    $status = trim((string) ($_GET['status'] ?? ''));
    if ($status !== '') {
        $statusFields = ['status', 'queue_status', 'visit_status', 'payment_status', 'billing_status'];
        $rows = array_values(array_filter($rows, function ($row) use ($status, $statusFields) {
            foreach ($statusFields as $field) {
                if (array_key_exists($field, (array) $row) && (string) $row[$field] === $status) {
                    return true;
                }
            }

            return false;
        }));
    }

    $limit = max(1, (int) $limit);
    $currentPage = max(1, (int) ($_GET['p'] ?? 1));
    $totalData = count($rows);
    $totalPages = max(1, (int) ceil($totalData / $limit));
    $currentPage = min($currentPage, $totalPages);
    $offset = ($currentPage - 1) * $limit;

    return [
        'rows' => array_slice($rows, $offset, $limit),
        'search' => $search,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'totalData' => $totalData,
        'limit' => $limit,
        'baseRoute' => $route,
    ];
}
