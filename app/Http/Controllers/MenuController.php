<?php

namespace App\Http\Controllers;

use App\Models\Menu; // Import Model Menu
use App\Models\Category; // Import Model Category untuk dropdown filter/form
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables; // Untuk DataTables

class MenuController extends Controller
{
    /**
     * Menampilkan halaman index (Daftar Menu).
     */
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Menu',
            'list' => ['Home', 'Menu']
        ];

        $page = (object) [
            'title' => 'Daftar Menu Restoran'
        ];

        $categories = Category::all();

        // 👇 SOLUSI: Definisikan $activeMenu di sini
        $activeMenu = 'menu'; // Nilai ini harus sama dengan yang diperiksa di sidebar.blade.php

        return view('admin.menu.index', compact('breadcrumb', 'page', 'categories', 'activeMenu'));
        // PASTIKAN Anda menyertakan 'activeMenu' dalam fungsi compact()
    }
    /**
     * Mengambil data untuk DataTables (AJAX).
     */
    public function list(Request $request)
    {
        // Eager load relasi 'category' agar tidak terjadi N+1 problem
        $menus = Menu::select('id', 'category_id', 'name', 'price', 'has_level')->with('category');

        // Filter berdasarkan category_id dari AJAX request (sesuai logika di index.blade.php)
        if ($request->category_id) {
            $menus->where('category_id', $request->category_id);
        }

        return DataTables::of($menus)
            ->addIndexColumn() // Menambahkan kolom nomor urut (DT_RowIndex)
            ->addColumn('aksi', function ($menu) { // Menambahkan tombol aksi
                // Rute mengacu pada prefix 'admin/menus'
                $btn = '<a href="' . url('/admin/menus/' . $menu->id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/admin/menus/' . $menu->id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/admin/menus/' . $menu->id) . '">' .
                    csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Menampilkan halaman tambah Menu.
     */
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Menu',
            'list' => ['Home', 'Menu', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah Menu Baru'
        ];

        // Ambil semua kategori untuk dropdown di form
        $categories = Category::all();
        
        // 👇 SOLUSI: Tambahkan $activeMenu
        $activeMenu = 'menu';

        // Mengirimkan data categories ke view: admin/menu/create.blade.php
        return view('admin.menu.create', compact('breadcrumb', 'page', 'categories', 'activeMenu'));
    }

    /**
     * Menyimpan data Menu baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'category_id' => 'required|exists:categories,id', // Harus ada di tabel categories
            'name' => 'required|string|max:100|unique:menus,name', // Nama menu harus unik
            'description' => 'nullable|string|max:255',
            'price' => 'required|integer|min:0', // Harga harus integer non-negatif
            'has_level' => 'required|boolean', // Harus 0 atau 1
        ]);

        Menu::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'has_level' => $request->has_level,
        ]);

        return redirect('/admin/menus')->with('success', 'Data Menu berhasil disimpan.');
    }

    /**
     * Menampilkan detail Menu.
     */
    public function show(string $id)
    {
        // Eager load relasi category
        $menu = Menu::with('category')->find($id);

        if (!$menu) {
            return redirect('/admin/menus')->with('error', 'Data Menu tidak ditemukan.');
        }

        $breadcrumb = (object) [
            'title' => 'Manajemen Menu',
            'list' => ['Home', 'Menu', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail Menu'
        ];
        
        // 👇 SOLUSI: Tambahkan $activeMenu
        $activeMenu = 'menu';

        return view('admin.menu.show', compact('breadcrumb', 'page', 'menu', 'activeMenu'));
    }

    /**
     * Menampilkan halaman edit Menu.
     */
    public function edit(string $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return redirect('/admin/menus')->with('error', 'Data Menu tidak ditemukan.');
        }

        $breadcrumb = (object) [
            'title' => 'Manajemen Menu',
            'list' => ['Home', 'Menu', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Menu'
        ];

        // Ambil semua kategori untuk dropdown di form
        $categories = Category::all();
        
        // 👇 SOLUSI: Tambahkan $activeMenu
        $activeMenu = 'menu';

        return view('admin.menu.edit', compact('breadcrumb', 'page', 'menu', 'categories', 'activeMenu'));
    }

    /**
     * Memperbarui data Menu di database.
     */
    public function update(Request $request, string $id)
    {
        // Validasi data
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100|unique:menus,name,' . $id, // Unik, kecuali ID yang sedang diedit
            'description' => 'nullable|string|max:255',
            'price' => 'required|integer|min:0',
            'has_level' => 'required|boolean',
        ]);

        Menu::find($id)->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'has_level' => $request->has_level,
        ]);

        return redirect('/admin/menus')->with('success', 'Data Menu berhasil diubah.');
    }

    /**
     * Menghapus data Menu.
     */
    public function destroy(string $id)
    {
        $check = Menu::find($id);

        if (!$check) {
            return redirect('/admin/menus')->with('error', 'Data Menu tidak ditemukan.');
        }

        try {
            Menu::destroy($id); // Menghapus data
            return redirect('/admin/menus')->with('success', 'Data Menu berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Karena Menu berelasi dengan OrderItem, jika data Menu masih digunakan, hapus akan gagal
            return redirect('/admin/menus')->with('error', 'Data Menu gagal dihapus karena masih digunakan dalam transaksi.');
        }
    }
}
