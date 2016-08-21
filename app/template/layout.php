<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= config('title') ?: 'My Awesome App' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.min.css') ?>">
    <script>
        window.onload = function() {
            var h1 = document.getElementsByTagName('h1');
            if (h1.length) { 
                h1[0].setAttribute('class', 'tada animated'); 
            }
        }
    </script>
</head>
<body>
    <div id="content">
        <?= $content ?>
    </div>
</body>
</html>