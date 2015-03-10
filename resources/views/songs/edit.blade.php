@extends('layouts.dashboard')

@section('content')
    <h1>Edit Song: {{ $song->title }}</h1>

    {!! Form::model($song, ['method' => 'PATCH', 'action' => ['SongsController@update', $song->id]]) !!}
        @include('songs.form', ['submitButtonText' => 'Update Song'])
    {!! Form::close() !!}

    @include('errors.list')

@endsection