    @extends('default')

    @section('content')

        @include('prob-notice')

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('prizes.create') }}" class="btn btn-info">Create</a>
                    </div>
                    <h1>Prizes</h1>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Title</th>
                                <th>Probability</th>
                                @if($isSimulated)
                                    <th>Awarded</th>
                                @endif
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prizes as $prize)
                                <tr>
                                    <td>{{ $prize->id }}</td>
                                    <td>{{ $prize->title }}</td>
                                    <td>{{ $prize->probability }}%</td>
                                    @if($isSimulated)
                                        <td>{{ $prize->awarded_count }}</td>
                                    @endif
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('prizes.edit', [$prize->id]) }}" class="btn btn-primary">Edit</a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['prizes.destroy', $prize->id]]) !!}
                                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                            {!! Form::close() !!}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>                        
                        
                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3>Simulate</h3>
                        </div>
                        <div class="card-body">
                            {!! Form::open(['method' => 'POST', 'route' => ['simulate']]) !!}
                            @csrf
                            <div class="form-group">
                                {!! Form::label('number_of_prizes', 'Number of Prizes') !!}
                                {!! Form::number('number_of_prizes', 50, ['class' => 'form-control']) !!}
                            </div>
                            {!! Form::submit('Simulate', ['class' => 'btn btn-primary']) !!}
                            {!! Form::close() !!}
                        </div>

                        <br>

                        <div class="card-body">
                            {!! Form::open(['method' => 'POST', 'route' => ['reset']]) !!}
                            @csrf
                            {!! Form::submit('Reset', ['class' => 'btn btn-primary']) !!}
                            {!! Form::close() !!}
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="container mb-4">
            <div class="row">
                <div class="col-md-6">
                    <h2>Probability Settings</h2>
                    <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                        <canvas id="probabilityChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <h2>Actual Rewards</h2>
                    <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                        <canvas id="awardedChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    @stop

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctxProbability = document.getElementById('probabilityChart').getContext('2d');
                const probabilities = @json($prizes->map(function ($prize) { 
                    return $prize->title . '{' . $prize->probability .'%'. '}';
                }));
                const labels = probabilities.map(p => p);

                new Chart(ctxProbability, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Probability Distribution',
                            data: @json($prizes->pluck('probability')),
                            backgroundColor: [
                                '#FF6384', 
                                '#36A2EB', 
                                '#FFCE56',
                                '#4BC0C0', 
                                '#9966FF', 
                                '#FF9F40', 
                                '#E57373', 
                                '#64B5F6', 
                                '#81C784', 
                                '#FFD54F'  
                            ],
                            borderColor: [
                                '#FF6384', 
                                '#36A2EB', 
                                '#FFCE56', 
                                '#4BC0C0', 
                                '#9966FF', 
                                '#FF9F40', 
                                '#E57373', 
                                '#64B5F6', 
                                '#81C784', 
                                '#FFD54F'  
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            datalabels: {
                                display: true,
                                color: '#fff',
                                formatter: (value, context) => {
                                    const label = context.chart.data.labels[context.dataIndex];
                                    return `${label} (${value}%)`;
                                },
                                anchor: 'end',
                                align: 'start',
                            },
                            legend: {
                                position: 'top',
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });

                const ctxAwarded = document.getElementById('awardedChart').getContext('2d');
                const awardedData = @json($prizes->map(function ($prize) { 
                    return $prize->title . '{' . $prize->awarded_count .'%'. '}';
                }));
                const awardLabels = awardedData.map(a => a);

                new Chart(ctxAwarded, {
                    type: 'doughnut',
                    data: {
                        labels: awardLabels,
                        datasets: [{
                            label: 'Awarded Distribution',
                            data: @json($prizes->pluck('awarded_count')),
                            backgroundColor: [
                                '#FF6384', 
                                '#36A2EB', 
                                '#FFCE56', 
                                '#4BC0C0', 
                                '#9966FF', 
                                '#FF9F40', 
                                '#E57373', 
                                '#64B5F6', 
                                '#81C784',  
                                '#FFD54F'  
                            ],
                            borderColor: [
                                '#FF6384', 
                                '#36A2EB', 
                                '#FFCE56', 
                                '#4BC0C0', 
                                '#9966FF', 
                                '#FF9F40', 
                                '#E57373', 
                                '#64B5F6', 
                                '#81C784', 
                                '#FFD54F'  
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            datalabels: {
                                display: true,
                                color: '#fff',
                                formatter: (value, context) => {
                                    const label = context.chart.data.labels[context.dataIndex];
                                    return `${label} (${value})`;

                                },
                                anchor: 'end',
                                align: 'start',
                            },
                            legend: {
                                position: 'top',
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
            });
        </script>
    @endpush
