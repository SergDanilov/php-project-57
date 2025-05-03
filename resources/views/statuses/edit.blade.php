@extends('layouts.app')

@section('content')
    <h1>Изменение статуса</h1>
    
    <form method="POST" action="{{ route('task_statuses.update', $status) }}">
        @csrf
        @method('PATCH')
        
        <div class="form-group">
            <label for="name">Название</label>
            <input type="text" class="form-control" id="name" name="name" 
                   value="{{ old('name', $status->name) }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">Обновить</button>
    </form>
@endsection