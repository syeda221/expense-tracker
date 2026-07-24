<?php
$files = [
    'app/Http/Controllers/API/AdvisorController.php',
    'app/Services/AI/AICopilotService.php',
    'app/Services/AdvisorPromptBuilder.php',
    'resources/views/dashboard.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/guest.blade.php',
    'resources/views/layouts/navigation.blade.php',
    'resources/views/welcome.blade.php',
    '.env',
    'config/app.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        // Ollie to Foresight
        $content = str_replace('Ollie', 'Foresight', $content);
        $content = str_replace('ollie', 'foresight', $content);
        
        // Expense Tracker to Foresight
        $content = str_replace('Expense Tracker', 'Foresight', $content);
        
        file_put_contents($path, $content);
    }
}
echo "Rename complete\n";
