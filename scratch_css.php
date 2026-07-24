<?php
$file = __DIR__ . '/resources/css/theme.css';
$content = file_get_contents($file);

// Replace font families
$content = preg_replace('/--font-display:\s*\'[^\']+\',\s*sans-serif;/', "--font-display: 'Inter', sans-serif;", $content);
$content = preg_replace('/--font-body:\s*\'[^\']+\',\s*sans-serif;/', "--font-body: 'Inter', sans-serif;", $content);

// Replace colors
$content = preg_replace('/--bg-primary:\s*#[A-Fa-f0-9]+;/', "--bg-primary: #F8FAFC;", $content);
$content = preg_replace('/--success:\s*#[A-Fa-f0-9]+;/', "--success: #10B981;", $content);
$content = preg_replace('/--warning:\s*#[A-Fa-f0-9]+;/', "--warning: #F59E0B;", $content);
$content = preg_replace('/--danger:\s*#[A-Fa-f0-9]+;/', "--danger: #EF4444;", $content);
$content = preg_replace('/--info:\s*#[A-Fa-f0-9]+;/', "--info: #3B82F6;", $content);
$content = preg_replace('/--border:\s*#[A-Fa-f0-9]+;/', "--border: #E2E8F0;", $content);
$content = preg_replace('/--text-muted:\s*#[A-Fa-f0-9]+;/', "--text-muted: #64748B;", $content);
$content = preg_replace('/--text-dim:\s*#[A-Fa-f0-9]+;/', "--text-dim: #94A3B8;", $content);

// Replace radius and shadows
$content = preg_replace('/--radius-lg:\s*[0-9]+px;/', "--radius-lg: 20px;", $content);
$content = preg_replace('/--radius-xl:\s*[0-9]+px;/', "--radius-xl: 24px;", $content);
$content = preg_replace('/--shadow-card:\s*[^;]+;/', "--shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -1px rgba(0, 0, 0, 0.02);", $content);
$content = preg_replace('/--shadow-elevated:\s*[^;]+;/', "--shadow-elevated: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -4px rgba(0, 0, 0, 0.03);", $content);
$content = preg_replace('/--transition-base:\s*[^;]+;/', "--transition-base: 200ms ease-out;", $content);
$content = preg_replace('/--transition-fast:\s*[^;]+;/', "--transition-fast: 150ms ease-out;", $content);

// Card padding
$content = preg_replace('/\.card-premium \.card-body \{\s*padding:\s*24px;\s*\}/s', ".card-premium .card-body {\n    padding: 32px;\n}", $content);

// Buttons
$content = preg_replace('/\.btn-premium\.btn-primary \{\s*background:\s*var\(--primary\);\s*color:\s*#[A-Fa-f0-9]+;\s*border:\s*none;\s*\}/s', ".btn-premium.btn-primary {\n    background: linear-gradient(180deg, #16C7B7 0%, #0EA597 100%);\n    color: #ffffff;\n    border: 1px solid rgba(0,0,0,0.1);\n    border-radius: 12px;\n    box-shadow: 0 1px 2px rgba(0,0,0,0.05), inset 0 1px 0 rgba(255,255,255,0.15);\n}", $content);

file_put_contents($file, $content);
echo "Done CSS updates.\n";
