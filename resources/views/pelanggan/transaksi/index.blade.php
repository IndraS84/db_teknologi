@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Riwayat Transaksi') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if($transaksis->isEmpty())
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Belum ada transaksi.
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach($transaksis as $t)
                    <div x-data="{ expanded: false }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            INV{{ str_pad($t->id_transaksi, 6, '0', STR_PAD_LEFT) }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $t->tanggal_transaksi ? $t->tanggal_transaksi->format('d M Y H:i') : '' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-gray-900">
                                            Rp {{ number_format($t->total_setelah_diskon, 0, ',', '.') }}
                                        </p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if(($t->status_transaksi ?? $t->status ?? '') === 'completed')
                                                bg-green-100 text-green-800
                                            @elseif(($t->status_transaksi ?? $t->status ?? '') === 'pending')
                                                bg-yellow-100 text-yellow-800
                                            @elseif(($t->status_transaksi ?? $t->status ?? '') === 'processing')
                                                bg-blue-100 text-blue-800
                                            @else
                                                bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $t->status_transaksi ?? $t->status ?? '')) }}
                                        </span>
                                    </div>
                                    <button @click="expanded = !expanded" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                        <svg x-show="!expanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        <svg x-show="expanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div x-show="expanded" x-transition class="mt-4 pt-4 border-t border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Detail Transaksi</h4>
                                        <div class="space-y-1 text-sm text-gray-600">
                                            <p>ID Transaksi: {{ $t->id_transaksi }}</p>
                                            <p>Metode Pembayaran: {{ $t->metode_pembayaran ?? 'N/A' }}</p>
                                            <p>Jumlah Item: {{ $t->detailTransaksis->count() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <a href="{{ route('pelanggan.transaksi.show', $t->id_transaksi) }}"
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Lihat Detail
                                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
