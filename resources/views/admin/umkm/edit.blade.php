@extends('layouts.admin')
@section('title','Edit UMKM')

@section('content')

<div class="px-5 md:px-20">
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <h1 class="text-2xl font-bold mb-6">Edit UMKM</h1>

    <form method="POST" action="{{ route('admin.umkm.update',$umkm) }}">
        @method('PUT')
        @include('admin.umkm.form')
    </form>
</div>

@endsection
