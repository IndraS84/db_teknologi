<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="waves-effect">
                        <i class="fa fa-dashboard m-r-10" aria-hidden="true"></i>Dashboard
                    </a>
                </li>
                
                <li>
                    <a class="has-arrow waves-effect" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-cube m-r-10" aria-hidden="true"></i>
                        <span class="hide-menu">Produk</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('admin.produk.index') }}">Daftar Produk</a></li>
                        <li><a href="{{ route('admin.produk.create') }}">Tambah Produk</a></li>
                    </ul>
                </li>

                <li>
                    <a class="has-arrow waves-effect" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-truck m-r-10" aria-hidden="true"></i>
                        <span class="hide-menu">Supplier</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('admin.supplier.index') }}">Daftar Supplier</a></li>
                        <li><a href="{{ route('admin.supplier.create') }}">Tambah Supplier</a></li>
                    </ul>
                </li>

                <li>
                    <a class="has-arrow waves-effect" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-arrow-down m-r-10" aria-hidden="true"></i>
                        <span class="hide-menu">Barang Masuk</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('admin.barang-masuk.index') }}">Daftar Barang Masuk</a></li>
                        <li><a href="{{ route('admin.barang-masuk.create') }}">Tambah Barang Masuk</a></li>
                    </ul>
                </li>

                <li>
                    <a class="has-arrow waves-effect" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-arrow-up m-r-10" aria-hidden="true"></i>
                        <span class="hide-menu">Barang Keluar</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('admin.barang-keluar.index') }}">Daftar Barang Keluar</a></li>
                        <li><a href="{{ route('admin.barang-keluar.create') }}">Tambah Barang Keluar</a></li>
                    </ul>
                </li>

                <li>
                    <a class="has-arrow waves-effect" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-users m-r-10" aria-hidden="true"></i>
                        <span class="hide-menu">Pelanggan</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('admin.pelanggan.index') }}">Daftar Pelanggan</a></li>
                        <li><a href="{{ route('admin.pelanggan.create') }}">Tambah Pelanggan</a></li>
                    </ul>
                </li>

                <li>
                    <a class="has-arrow waves-effect" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-shopping-cart m-r-10" aria-hidden="true"></i>
                        <span class="hide-menu">Transaksi</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('admin.transaksi.index') }}">Daftar Transaksi</a></li>
                        <li><a href="{{ route('admin.transaksi.create') }}">Tambah Transaksi</a></li>
                    </ul>
                </li>

                <li>
                    <a class="has-arrow waves-effect" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-tag m-r-10" aria-hidden="true"></i>
                        <span class="hide-menu">Diskon</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('admin.diskon.index') }}">Daftar Diskon</a></li>
                        <li><a href="{{ route('admin.diskon.create') }}">Tambah Diskon</a></li>
                    </ul>
                </li>

                <li>
                    <a class="has-arrow waves-effect" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-file-text m-r-10" aria-hidden="true"></i>
                        <span class="hide-menu">Laporan</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('admin.laporan.index') }}">Daftar Laporan</a></li>
                        <li><a href="{{ route('admin.laporan.create') }}">Buat Laporan</a></li>
                    </ul>
                </li>

                <li>
                    <a class="has-arrow waves-effect" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-user m-r-10" aria-hidden="true"></i>
                        <span class="hide-menu">Admin</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('admin.admin.index') }}">Daftar Admin</a></li>
                        <li><a href="{{ route('admin.admin.create') }}">Tambah Admin</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
