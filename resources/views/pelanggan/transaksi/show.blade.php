@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Detail Transaksi') }} #{{ $transaksi->id_transaksi }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Status Timeline -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status Transaksi</h3>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Pembayaran Dibuat</p>
                                <p class="text-sm text-gray-500">{{ $transaksi->tanggal_transaksi ? $transaksi->tanggal_transaksi->format('d M Y H:i') : '' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8
                                    @if(in_array($transaksi->status_transaksi, ['processing', 'completed']))
                                        bg-yellow-500
                                    @else
                                        bg-gray-300
                                    @endif
                                    rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Diproses</p>
                                <p class="text-sm text-gray-500">Menunggu konfirmasi</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8
                                    @if($transaksi->status_transaksi === 'completed')
                                        bg-green-500
                                    @else
                                        bg-gray-300
                                    @endif
                                    rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Selesai</p>
                                <p class="text-sm text-gray-500">Transaksi selesai</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembayaran</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Transaksi</dt>
                                <dd class="text-sm text-gray-900">{{ $transaksi->id_transaksi }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Metode Pembayaran</dt>
                                <dd class="text-sm text-gray-900">{{ $transaksi->metode_pembayaran }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($transaksi->status_transaksi === 'completed')
                                            bg-green-100 text-green-800
                                        @elseif($transaksi->status_transaksi === 'pending')
                                            bg-yellow-100 text-yellow-800
                                        @elseif($transaksi->status_transaksi === 'processing')
                                            bg-blue-100 text-blue-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $transaksi->status_transaksi)) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Pembayaran</dt>
                                <dd class="text-lg font-semibold text-gray-900">Rp {{ number_format($transaksi->total_setelah_diskon, 0, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if($transaksi->bukti_pembayaran)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Bukti Pembayaran</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <img src="{{ asset('storage/' . $transaksi->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="max-w-full h-auto rounded-lg shadow-sm">
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Items -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Produk</h3>
                    <div class="bg-gray-50 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <div class="grid grid-cols-12 gap-4 text-sm font-medium text-gray-500">
                                <div class="col-span-6">Produk</div>
                                <div class="col-span-2 text-center">Jumlah</div>
                                <div class="col-span-2 text-center">Harga</div>
                                <div class="col-span-2 text-right">Subtotal</div>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @foreach($transaksi->detailTransaksis as $d)
                                <div class="px-4 py-3">
                                    <div class="grid grid-cols-12 gap-4 items-center text-sm">
                                        <div class="col-span-6">
                                            <div class="font-medium text-gray-900">{{ $d->produk->nama_produk ?? '-' }}</div>
                                        </div>
                                        <div class="col-span-2 text-center">{{ $d->jumlah }}</div>
                                        <div class="col-span-2 text-center">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</div>
                                        <div class="col-span-2 text-right font-medium">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="px-4 py-3 bg-gray-100 border-t border-gray-200">
                            <div class="grid grid-cols-12 gap-4 items-center text-sm font-medium">
                                <div class="col-span-10 text-right">Total</div>
                                <div class="col-span-2 text-right text-gray-900">Rp {{ number_format($transaksi->total_setelah_diskon, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('pelanggan.transaksi.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </a>

                    @if($transaksi->struk)
                        <div class="flex items-center space-x-4">
                            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium">Pembayaran Dikonfirmasi! Struk tersedia.</span>
                            </div>
                            <a href="{{ route('pelanggan.transaksi.struk', $transaksi->id_transaksi) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Lihat Struk
                            </a>
                        </div>
                    @elseif($transaksi->status_transaksi === 'completed')
                        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Struk sedang diproses. Silakan tunggu beberapa saat atau hubungi admin.</span>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Menunggu konfirmasi pembayaran dari admin.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
