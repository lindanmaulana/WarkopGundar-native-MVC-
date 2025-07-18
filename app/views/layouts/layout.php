<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistem Antrian Rumah Sakit' ?></title>
    <!-- CSS -->
    <link href="/assets/css/output.css" rel="stylesheet">
    <style>
    </style>
</head>

<body>
    <!-- Main -->
    <main class="h-screen w-full bg-soft-blue-gray">
        <section class="w-full h-full">
            <div class="flex items-center justify-center h-full">
                <?= $content ?>
            </div>
        </section>
    </main>
</body>

</html>