<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Beyours')); ?></title>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <?php if(app()->environment('local')): ?>
        <!-- Desarrollo: Vite -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php else: ?>
        <!-- Producción: Assets compilados -->
        <link rel="stylesheet" href="<?php echo e(asset('build/assets/app-BgAgxFXk.css')); ?>">
        <script src="<?php echo e(asset('build/assets/app-DRPAYixA.js')); ?>" defer></script>
    <?php endif; ?>
</head>
<body>
    <div id="app"></div>
</body>
</html><?php /**PATH C:\xampp\htdocs\beyours\resources\views/app.blade.php ENDPATH**/ ?>