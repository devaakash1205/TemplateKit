<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('site.default_title')) ?></title>
    <meta name="description" content="<?= config('site.default_description', 'TemplateKit') ?>">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Auto-loaded CSS files -->
    <?= Asset::css() ?>
</head>

<body>

    <!-- Main content -->
    <?= $GLOBALS['compiledView'] ?>

    <!-- Optional footer -->
    <footer class="text-center text-muted mt-5">
        &copy; <?= date('Y') . ' ' . config('site.name', 'TemplateKit') ?>
    </footer>

    <!-- JS + Trackers -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= Asset::js() ?>
    <?= Tracker::load() ?>

</body>

</html>
