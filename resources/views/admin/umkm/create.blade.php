@extends('layouts.admin')
@section('title','Tambah UMKM')

@section('content')
<h1 class="text-2xl font-bold mb-6">Tambah UMKM</h1>

<form method="POST" action="{{ route('admin.umkm.store') }}">
    @include('admin.umkm.form')
</form>

@endsection
