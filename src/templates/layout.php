<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= config('title') ?: 'My Awesome App' ?></title>
    <?= block('styles') ?>
</head>
<body>
    <div id="content">
        <?= $content ?>
    </div>
    <?= block('scripts') ?>
</body>
</html>