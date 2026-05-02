<x-filament-widgets::widget>
    <x-filament::section>
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">

            <!-- HEADER BAGIAN ATAS -->
            <div
                style="display: flex; align-items: center; gap: 1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem;">
                <x-filament::icon icon="heroicon-m-server-stack" style="height: 2.5rem; width: 2.5rem; color: #3b82f6;" />
                <div style="flex: 1;">
                    <h2 style="font-size: 1.25rem; font-weight: bold; margin: 0; color: inherit;">Kelengkapan Data Master
                    </h2>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">{{ $totalDone }} dari
                        {{ $totalAll }} modul siap digunakan ({{ $percent }}%)</p>
                </div>
            </div>

            <!-- KUMPULAN PIL (MEMANJANG KE KANAN) -->
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                @foreach ($statuses as $item)
                    <a href="{{ url($item['link']) }}"
                        style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; text-decoration: none; border: 1px solid {{ $item['done'] ? '#10b981' : '#ef4444' }}; background-color: {{ $item['done'] ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{ $item['done'] ? '#059669' : '#dc2626' }}; transition: transform 0.2s ease;"
                        onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">

                        @if ($item['done'])
                            <x-filament::icon icon="heroicon-m-check-circle"
                                style="height: 1.25rem; width: 1.25rem; color: #10b981;" />
                        @else
                            <x-filament::icon icon="heroicon-m-x-circle"
                                style="height: 1.25rem; width: 1.25rem; color: #ef4444;" />
                        @endif

                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
