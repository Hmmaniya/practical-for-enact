@extends('default')

@section('content')

@include('prob-notice')

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            {{ $error }} <br>
        @endforeach
    </div>
@endif

{{ Form::model($prize, ['route' => ['prizes.update', $prize->id], 'method' => 'PUT']) }}

    <div class="mb-3">
        {{ Form::label('title', 'Title', ['class'=>'form-label']) }}
        {{ Form::text('title', null, ['class' => 'form-control']) }}
    </div>
    <div class="mb-3">
        {{ Form::label('probability', 'Probability', ['class'=>'form-label']) }}
        {{ Form::number('probability', null, ['class' => 'form-control', 'min' => '0', 'max' => '100', 'placeholder' => '0 - 100', 'step' => '0.01']) }}
    </div>

    {{ Form::submit('Edit', ['class' => 'btn btn-primary']) }}

{{ Form::close() }}

@stop
