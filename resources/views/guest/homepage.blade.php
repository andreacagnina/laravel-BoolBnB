@extends('layouts.app')

@section('content')    
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="content">
                @guest
                <!-- Hero Section -->
                <section class="hero-section text-center p-5 rounded shadow-sm" style="background: linear-gradient(135deg, #1c1c2d, #282846); color: white;">
                    <h1 class="display-4 fw-bold mb-3">Welcome to BoolBnB</h1>
                    <p class="lead mb-4">Your all-in-one platform to simplify property management, boost visibility, and track performance.</p>
                    <a href="/register" class="btn btn-primary btn-lg">Get Started Now</a>
                </section>

                <!-- Features Section -->
                <section class="mt-5">
                    <h2 class="mb-4 text-center" style="color: #f8f9fa;">What BoolBnB Does</h2>
                    <div class="row gy-4">
                        <div class="col-md-4 col-12 text-center">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <i class="bi bi-house-door-fill display-3 text-primary mb-3"></i>
                                    <h5 class="card-title">Easy Property Management</h5>
                                    <p class="card-text">Register and manage your properties quickly and efficiently.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 text-center">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <i class="bi bi-megaphone-fill display-3 text-primary mb-3"></i>
                                    <h5 class="card-title">Boost Visibility</h5>
                                    <p class="card-text">Sponsor your listings to stand out and reach more users.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 text-center">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <i class="bi bi-bar-chart-fill display-3 text-primary mb-3"></i>
                                    <h5 class="card-title">Detailed Analytics</h5>
                                    <p class="card-text">Monitor your property's performance with in-depth statistics.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                @else
                <!-- Welcome Section -->
                <section class="hero-section text-center p-5 rounded shadow-sm" style="background: linear-gradient(135deg, #1c1c2d, #282846); color: white;">
                    @if(Auth::user()->name != null)
                    <h1 class="display-4 fw-bold mb-3">Welcome, {{ Auth::user()->name }}!</h1>
                    @else
                    <h1 class="display-4 fw-bold mb-3">Welcome, {{ Auth::user()->email }}!</h1>
                    @endif
                    <p class="lead mb-4">Manage your properties with ease and maximize your success.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="/properties/create" class="btn btn-success btn-lg">Add a New Property</a>
                        <a href="/statistics" class="btn btn-warning btn-lg">View Statistics</a>
                    </div>
                </section>

                <!-- Features Section -->
                <section class="mt-5">
                    <h2 class="mb-4 text-center" style="color: #f8f9fa;">What BoolBnB Offers</h2>
                    <div class="row gy-4">
                        <div class="col-md-4 col-12 text-center">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <i class="bi bi-house-door-fill display-3 text-primary mb-3"></i>
                                    <h5 class="card-title">Comprehensive Management</h5>
                                    <p class="card-text">Efficiently manage all your properties in one place.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 text-center">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <i class="bi bi-megaphone-fill display-3 text-primary mb-3"></i>
                                    <h5 class="card-title">Maximized Visibility</h5>
                                    <p class="card-text">Sponsor your properties to attract more potential clients.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 text-center">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <i class="bi bi-bar-chart-fill display-3 text-primary mb-3"></i>
                                    <h5 class="card-title">In-Depth Insights</h5>
                                    <p class="card-text">Get detailed analytics to optimize your performance.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                @endguest
            </div>
        </div>
    </div>
</div>
@endsection
