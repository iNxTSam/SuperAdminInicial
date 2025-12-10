@extends('layouts.header')

@section('title', 'Iniciar Sesión')

@section('content')
    <form action="{{route('loginUsuario')}}" method="post">
        @csrf
        <div class="mb-3">
            <label for="numeroDocumento" class="form-label">Número de documento</label>
            <input type="text" class="form-control" id="numeroDocumento" aria-describedby="emailHelp"
                name="numeroDocumento" required>
        </div>
        <div class="mb-3">
            <label for="clave" class="form-label">contraseña</label>
            <input type="password" class="form-control" id="clave" name="clave" required>
        </div>
        <button type="submit" class="btn btn-primary">Iniciar sesión</button>
    </form>
    @if($errors->any())
        <div class="errores" style="color:red;">
            {{ $errors->first('error') }}
        </div>
    @endif
@endsection