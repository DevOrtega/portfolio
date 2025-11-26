<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Personal Info ===\n";
$info = App\Models\PersonalInfo::first();
if ($info) {
    echo "headline (ES): " . ($info->headline ?? 'NULL') . "\n";
    echo "headline_en: " . ($info->headline_en ?? 'NULL') . "\n";
    echo "\n";
}

echo "=== Experience ===\n";
$exp = App\Models\Experience::first();
if ($exp) {
    echo "role (ES): " . ($exp->role ?? 'NULL') . "\n";
    echo "role_en: " . ($exp->role_en ?? 'NULL') . "\n";
    echo "\n";
}

echo "=== Project ===\n";
$project = App\Models\Project::first();
if ($project) {
    echo "title (ES): " . ($project->title ?? 'NULL') . "\n";
    echo "title_en: " . ($project->title_en ?? 'NULL') . "\n";
    echo "description (ES): " . substr($project->description ?? 'NULL', 0, 50) . "...\n";
    echo "description_en: " . substr($project->description_en ?? 'NULL', 0, 50) . "...\n";
    echo "\n";
}

echo "=== Education ===\n";
$edu = App\Models\Education::first();
if ($edu) {
    echo "degree (ES): " . ($edu->degree ?? 'NULL') . "\n";
    echo "degree_en: " . ($edu->degree_en ?? 'NULL') . "\n";
    echo "\n";
}

echo "=== Testing locale switch ===\n";
app()->setLocale('es');
echo "Locale: es\n";
$info = App\Models\PersonalInfo::first();
echo "headline: " . $info->toArray()['headline'] . "\n";
$project = App\Models\Project::first();
echo "project title: " . $project->toArray()['title'] . "\n\n";

app()->setLocale('en');
echo "Locale: en\n";
$info = App\Models\PersonalInfo::first();
echo "headline: " . $info->toArray()['headline'] . "\n";
$project = App\Models\Project::first();
echo "project title: " . $project->toArray()['title'] . "\n";
