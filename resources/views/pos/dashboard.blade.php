@extends('pos.layout.layout')
@section('content')
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card p-3 text-center">
                <i class="bi bi-person fs-3 text-primary"></i>
                <h6 class="mt-2">{{ $customers }} Customers</h6>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center">
                <i class="bi bi-folder-check fs-3 text-info"></i>
                <h6 class="mt-2">5+ Active Projects</h6>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center">
                <i class="bi bi-people fs-3 text-danger"></i>
                <h6 class="mt-2">4+ Staffs</h6>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center">
                <i class="bi bi-lock fs-3 text-warning"></i>
                <h6 class="mt-2">5+ Total Projects</h6>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card p-3">
                <h6>Projects</h6>
                <canvas id="projectsChart"></canvas>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-3">
                <h6>Admin Revenue In this year</h6>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
    
    @once
        @push('scripts')
            <script>
                // Projects Pie Chart
                new Chart(document.getElementById("projectsChart"), {
                    type: 'doughnut',
                    data: {
                        labels: ["Completed", "In Progress", "Not Started"],
                        datasets: [{
                            data: [40, 35, 25],
                            backgroundColor: ["#0d6efd", "#20c997", "#ffc107"]
                        }]
                    }
                });

                // Revenue Bar Chart
                new Chart(document.getElementById("revenueChart"), {
                    type: 'bar',
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                        datasets: [{
                            label: "Revenue",
                            data: [0, 33, 250, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                            backgroundColor: "#0d6efd"
                        }]
                    }
                });
            </script>
        @endpush
    @endonce
@endsection
