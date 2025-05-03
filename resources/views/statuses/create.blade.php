@extends('layouts.app')

@section('content')
    <h1>Создать статус</h1>
    
    <form method="POST" action="{{ route('task_statuses.store') }}">
        @csrf
        
        <div class="form-group">
            <label for="name">Название</label>
            <input type="text" class="form-control" id="name" name="name" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">Создать</button>
    </form>
@endsection