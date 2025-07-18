<?php
// app/core/helpers.php

// ... other helper functions like isActive, bgHeader, route ...

function x_icon(array $attributes): string
{
    $name = $attributes['name'] ?? 'default';
    $class = $attributes['class'] ?? '';
    // In a real app, this would load SVG from a sprite or a dedicated file
    // For simplicity, we'll just return a placeholder.
    return "<img src='/assets/images/icons/{$name}.svg' alt='{$name} icon' class='{$class}'>";
}

// ... rest of your helper functions