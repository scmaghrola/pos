@extends('pos.layout.layout')

@section('title', 'My Profile')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-4 text-center border-end">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="https://via.placeholder.com/150" alt="Profile Photo" class="rounded-3 img-thumbnail"
                                width="150" height="150">
                            <div class="change-photo position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white small py-1 rounded-bottom"
                                style="opacity:0; transition:.3s;">
                                Change Photo
                            </div>
                        </div>

                        <div class="mt-4 text-start">
                            <h6 class="text-uppercase text-muted fw-bold">Skills</h6>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-decoration-none text-primary">Laravel</a></li>
                                <li><a href="#" class="text-decoration-none text-primary">Bootstrap</a></li>
                                <li><a href="#" class="text-decoration-none text-primary">MySQL</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="mb-0">{{ $user->name }}</h4>
                                <p class="text-primary mb-1">{{ $user->getRoleNames()->first() ?? 'User' }}</p>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editProfileModal">
                                Edit Profile
                            </button>
                        </div>

                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="about-tab" data-bs-toggle="tab" data-bs-target="#about"
                                    type="button">About</button>
                            </li>
                        </ul>

                        <div class="tab-content pt-3">
                            <div class="tab-pane fade show active" id="about">
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">User ID</div>
                                    <div class="col-sm-8 text-primary">{{ $user->id }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Name</div>
                                    <div class="col-sm-8 text-primary">{{ $user->name }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Email</div>
                                    <div class="col-sm-8 text-primary">{{ $user->email }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Created At</div>
                                    <div class="col-sm-8 text-primary">{{ $user->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Modal -->
        <div class="modal fade" id="editProfileModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('user.profile.update') }}" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('styles')
        <style>
            .change-photo:hover {
                opacity: 1 !important;
                cursor: pointer;
            }
        </style>
    @endpush
@endsection
