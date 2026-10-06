<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <title><?= htmlspecialchars($title ?? 'Kafinzo') ?></title>
    <!-- Font (self-hosted) -->
    <link href="/assets/vendor/inter/inter.css" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="/assets/vendor/bootstrap-icons/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

    <?= $content ?? '' ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="/assets/js/chart.umd.min.js"></script>
</body>
</html>
