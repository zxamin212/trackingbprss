<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Dashboard CS</h3>
        <h6 class="op-7 mb-2">Berkas yang perlu diinput dan diverifikasi</h6>
    </x-slot>

    <div class="card card-round">
        <div class="card-body">
            <p>Halo, {{ auth()->user()->name }} — dashboard CS masih dalam pengembangan.</p>
        </div>
    </div>
</x-app-layout>