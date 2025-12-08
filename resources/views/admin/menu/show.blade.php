@extends('layout.template')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            @empty($menu)
                <div class="alert alert-danger alert-dismissible">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!</h5>
                    Data Menu yang Anda cari tidak ditemukan.
                </div>
            @else
                <table class="table table-bordered table-striped table-hover table-sm">
                    <tr>
                        <th>ID</th>
                        <td>{{ $menu->id }}</td>
                    </tr>
                    <tr>
                        <th>Nama Menu</th>
                        <td>{{ $menu->name }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $menu->category->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $menu->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Harga Dasar</th>
                        <td>Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Perlu Level?</th>
                        <td>
                            @if ($menu->has_level)
                                <span class="badge bg-danger">YA</span>
                            @else
                                <span class="badge bg-success">TIDAK</span>
                            @endif
                        </td>
                    </tr>
                </table>
            @endempty
            <a href="{{ url('admin/menus') }}" class="btn btn-sm btn-default mt-2">Kembali</a>
        </div>
    </div>
@endsection