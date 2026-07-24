<?php
$file = __DIR__ . '/resources/views/dashboard.blade.php';
$content = file_get_contents($file);

// Default styles
$content = str_replace("Chart.defaults.color = '#6B7280';", "Chart.defaults.color = '#64748B'; Chart.defaults.font.family = 'Inter';", $content);
$content = str_replace("Chart.defaults.borderColor = '#E7ECF2';", "Chart.defaults.borderColor = 'rgba(0, 0, 0, 0.04)';", $content);

// Bar Chart
$content = str_replace("borderColor: '#16C7B7',", "borderColor: 'transparent',", $content);
$content = str_replace("borderWidth: 1,", "borderWidth: 0,", $content);
$content = str_replace("borderRadius: 6,", "borderRadius: 12,", $content);
$content = preg_replace('/barGradient\.addColorStop\(0, \'[^\']+\'\);/', "barGradient.addColorStop(0, '#16C7B7');", $content);
$content = preg_replace('/barGradient\.addColorStop\(1, \'[^\']+\'\);/', "barGradient.addColorStop(1, '#0EA597');", $content);

// Doughnut Chart
$content = str_replace("cutout: '70%',", "cutout: '75%',", $content);
$content = str_replace("borderWidth: 0,", "borderWidth: 3, borderColor: '#ffffff', hoverOffset: 4,", $content);
$content = str_replace("boxWidth: 8,", "boxWidth: 12,", $content);
$content = str_replace("padding: 8,", "padding: 12,", $content);

// Line Chart
$content = str_replace("borderWidth: 2.5,", "borderWidth: 3, tension: 0.4,", $content);
$content = str_replace("rgba(22, 199, 183, 0.3)", "rgba(22, 199, 183, 0.2)", $content);

file_put_contents($file, $content);
echo "Chart JS updated\n";
