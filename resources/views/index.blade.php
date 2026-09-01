@extends('core.layouts.front')
@section('content')
    <!-- Hero Section -->
    <x-core::landing.hero />

    <!-- How It Works Section -->
    <x-core::landing.how-it-works />

    <!-- Pricing Section -->
    <x-core::landing.pricing :plans="$plans" />



    <!-- Enquiry Form Section -->
    <x-core::landing.enquiry-form />

    <!-- JavaScript -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection
