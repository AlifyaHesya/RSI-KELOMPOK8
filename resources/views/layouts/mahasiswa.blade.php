@extends('layouts.app')

@push('styles')
<style>
    :root {
        --pk-teal: #21737f;
        --pk-teal-dark: #195c66;
        --pk-yellow: #f2b705;
        --pk-yellow-hover: #dda600;
    }

    body { background-color: #f5f9f9; }

    .text-pk { color: var(--pk-teal) !important; }
    .text-pk-yellow { color: var(--pk-yellow) !important; }

    .btn-pk {
        background-color: var(--pk-teal);
        color: #fff;
        border: none;
    }
    .btn-pk:hover { background-color: var(--pk-teal-dark); color: #fff; }

    .btn-pk-outline {
        background-color: #ffffff;
        color: var(--pk-teal);
        border: 1px solid #cbd5e1;
        transition: all 0.2s ease;
    }
    .btn-pk-outline:hover {
        background-color: var(--pk-teal);
        color: #ffffff;
        border-color: var(--pk-teal);
    }

    .btn-pk-yellow {
        background-color: var(--pk-yellow);
        color: #ffffff;
        border: none;
    }
    .btn-pk-yellow:hover { background-color: var(--pk-yellow-hover); color: #ffffff; }

    .pk-stat-box {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.04);
    }

    .pk-card {
        border-radius: 1.25rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(33, 110, 117, 0.1);
    }
</style>
@endpush
