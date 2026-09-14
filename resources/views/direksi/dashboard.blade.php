<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Dashboard Direksi</h3>
        <h6 class="op-7 mb-2">Monitoring seluruh berkas kredit — semua kantor (read-only)</h6>
    </x-slot>

    <div class="card card-round">
        <div class="card-body">
            <p>Halo, {{ auth()->user()->name }} — dashboard Direksi masih dalam pengembangan.</p>
        </div>
    </div>
</x-app-layout>