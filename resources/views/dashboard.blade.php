@include('template.header')
<!-- main-wrap -->
<div class="container-fluid">
    <div class="row">
        @include('template.sidebar')
        <div class="col-sm-9">
            <div class="wel-ban">
                <div class="wel-ban-row">
                    <div class="wel-ban-left">
                        <h2>Welcome to CRM Portal</h2>
                        <span>efficiently manage your website with easy editing, updating, and deleting of sections. Simplify your site administration today!</span>
                    </div>
                    <div class="wel-ban-right">
                        <div class="ban-img">
                            <img src="{{asset('backend/images/user-bg.webp')}}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-3">
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body badge-bd">
                                <div class="badge-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="total-count">
                                    <h4>24</h4>
                                    <span>Total</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body badge-bd">
                                <div class="badge-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="total-count">
                                    <h4>50</h4>
                                    <span>Total Contact Request</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body badge-bd">
                                <div class="badge-icon">
                                    <i class="fas fa-concierge-bell"></i>
                                </div>
                                <div class="total-count">
                                    <h4>25</h4>
                                    <span>Total <span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <canvas id="lineChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('template.footer')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Bar Chart
        // var ctxBar = document.getElementById('barChart').getContext('2d');
        // var barChart = new Chart(ctxBar, {
        //     type: 'bar',
        //     data: {
        //         labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'], // Ensure this matches the data
        //         datasets: [{
        //             label: 'Monthly Sales',
        //             data: , // Ensure this data is passed correctly from the backend
        //             backgroundColor: 'rgba(75, 192, 192, 0.2)',
        //             borderColor: 'rgba(75, 192, 192, 1)',
        //             borderWidth: 1
        //         }]
        //     },
        //     options: {
        //         scales: {
        //             y: {
        //                 beginAtZero: true
        //             }
        //         }
        //     }
        // });

        // // Line Chart
        // var ctxLine = document.getElementById('lineChart').getContext('2d');
        // var lineChart = new Chart(ctxLine, {
        //     type: 'line',
        //     data: {
        //         labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'], // Ensure this matches the data
        //         datasets: [{
        //             label: 'Contact Requests',
        //             data: , // Ensure this data is passed correctly from the backend
        //             backgroundColor: 'rgba(153, 102, 255, 0.2)',
        //             borderColor: 'rgba(153, 102, 255, 1)',
        //             borderWidth: 1
        //         }]
        //     },
        //     options: {
        //         scales: {
        //             y: {
        //                 beginAtZero: true
        //             }
        //         }
        //     }
        // });
    </script>
    <script>
        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                datasets: [{
                    label: 'Sales',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Line Chart
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                datasets: [{
                    label: 'Revenue',
                    data: [5, 10, 15, 20, 25, 30],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>