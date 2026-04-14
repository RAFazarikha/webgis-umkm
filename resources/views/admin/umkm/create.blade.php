@extends('layouts.admin')
@section('title','Tambah UMKM')

@section('content')
<div class="px-5 md:px-20">
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <h1 class="text-2xl font-bold mb-6">Tambah UMKM</h1>

    <form method="POST" action="{{ route('admin.umkm.store') }}">
        @include('admin.umkm.form')
    </form>

    <form action="{{ route('admin.umkm.import') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <input type="file" name="file" class="my-6 w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2" required>
        <button type="submit" class="px-6 py-3 bg-[#D92D20] text-white rounded-lg">Import CSV</button>

    </form>
</div>

@endsection
