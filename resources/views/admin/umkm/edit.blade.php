@extends('layouts.admin')
@section('title','Edit UMKM')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit UMKM</h1>

<form method="POST" action="{{ route('admin.umkm.update',$umkm) }}">
    @method('PUT')
    @include('admin.umkm.form')
</form>

@endsection
