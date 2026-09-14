<x-app-layout>
    <x-slot name="header">
        <h3 class="fw-bold mb-3">Dashboard Senior Loan Officer</h3>
        <h6 class="op-7 mb-2">Berkas yang perlu di-survey dan diputuskan</h6>
    </x-slot>

    <div class="card card-round">
        <div class="card-body">
            <p>Halo, {{ auth()->user()->name }} — dashboard SLO masih dalam pengembangan.</p>
        </div>
    </div>
</x-app-layout>