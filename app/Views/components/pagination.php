<?php
$currentPage = max(1, (int) ($currentPage ?? 1));
$limit = max(1, (int) ($limit ?? 10));
$totalData = max(0, (int) ($totalData ?? 0));
$totalPages = max(1, (int) ($totalPages ?? ceil($totalData / $limit)));
$routeName = $paginationRoute ?? '';
$queryParams = $_GET;
unset($queryParams['p'], $queryParams['page'], $queryParams['route'], $queryParams['partial']);

$pageUrl = function ($page) use ($routeName, $queryParams) {
    $params = array_merge($queryParams, ['p' => max(1, (int) $page)]);
    return url($routeName) . '?' . http_build_query($params);
};

$startPage = max(1, $currentPage - 2);
$endPage = min($totalPages, $currentPage + 2);
?>

<div class="d-flex justify-content-center justify-content-sm-between align-items-center text-center flex-wrap gap-2 showing-wrap pt-15 p-20 border-top">

    <span class="fs-15">
        Showing
        <?= $totalData > 0 ? (($currentPage - 1) * $limit + 1) : 0 ?>
        to
        <?= min($currentPage * $limit, $totalData) ?>
        of
        <?= $totalData ?>
        entries
    </span>

    <?php if ($totalPages > 1 && $routeName !== ''): ?>
        <nav class="custom-pagination">
            <ul class="pagination mb-0 justify-content-center">

                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link icon" href="<?= $pageUrl($currentPage - 1) ?>">
                        <i class="material-symbols-outlined">west</i>
                    </a>
                </li>

                <?php if ($startPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $pageUrl(1) ?>">1</a>
                    </li>

                    <?php if ($startPage > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item">
                        <a
                            class="page-link <?= $currentPage === $i ? 'active' : '' ?>"
                            href="<?= $pageUrl($i) ?>"
                        >
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>

                    <li class="page-item">
                        <a class="page-link" href="<?= $pageUrl($totalPages) ?>">
                            <?= $totalPages ?>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link icon" href="<?= $pageUrl($currentPage + 1) ?>">
                        <i class="material-symbols-outlined">east</i>
                    </a>
                </li>

            </ul>
        </nav>
    <?php endif; ?>

</div>
