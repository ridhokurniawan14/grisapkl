<div class="relative w-full flex flex-col h-[calc(100vh-4rem)] bg-slate-50 -mx-4 px-4 pb-0 pt-2">

    <div class="flex items-center gap-3 mb-2 pt-2 bg-slate-50 z-10 sticky top-0 pb-2 border-b border-slate-200">
        <a href="{{ route('siswa.beranda') }}" wire:navigate
            class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 text-slate-600 hover:bg-slate-100 shrink-0">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="w-10 h-10 rounded-full bg-[#e2dfff] flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[#3525cd]"
                style="font-variation-settings: 'FILL' 1;">smart_toy</span>
        </div>
        <div class="flex-1">
            <h2 class="text-[18px] font-extrabold text-slate-800 leading-tight">PKL Bot</h2>
            <p class="text-[11px] font-bold text-green-600 flex items-center gap-1"><span
                    class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Online AI</p>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto flex flex-col gap-4 pb-[80px] pt-4 custom-scrollbar">
        @foreach ($messages as $msg)
            @if ($msg['role'] === 'bot')
                <div class="flex gap-2 max-w-[85%] self-start animate-fade-in-up">
                    <div class="w-8 h-8 rounded-full bg-[#3525cd]/10 flex items-center justify-center shrink-0 mt-auto">
                        <span class="material-symbols-outlined text-[#3525cd] text-[18px]"
                            style="font-variation-settings: 'FILL' 1;">smart_toy</span>
                    </div>
                    <div
                        class="bg-white text-slate-700 p-3 rounded-2xl rounded-bl-none shadow-sm border border-slate-100 text-[13px] leading-relaxed">
                        {!! nl2br(e($msg['text'])) !!}
                    </div>
                </div>
            @else
                <div
                    class="max-w-[80%] self-end bg-[#3525cd] text-white p-3 rounded-2xl rounded-br-none shadow-md text-[13px] leading-relaxed animate-fade-in-up">
                    {{ $msg['text'] }}
                </div>
            @endif
        @endforeach

        <div wire:loading wire:target="sendMessage"
            class="self-start pl-10 text-[11px] font-bold text-slate-400 animate-pulse">
            PKL Bot sedang mengetik...
        </div>
    </div>

    @if (count($messages) <= 1)
        <div class="flex flex-wrap gap-2 absolute bottom-[80px] left-4 right-4 z-20">
            <button wire:click="setPrompt('Bagaimana cara edit jurnal?')"
                class="bg-white border border-slate-200 text-[#3525cd] font-bold text-[11px] px-3 h-8 rounded-full shadow-sm">Cara
                edit jurnal?</button>
            <button wire:click="setPrompt('Aturan radius absensi')"
                class="bg-white border border-slate-200 text-[#3525cd] font-bold text-[11px] px-3 h-8 rounded-full shadow-sm">Aturan
                radius absen?</button>
        </div>
    @endif

    <form wire:submit="sendMessage"
        class="absolute bottom-0 left-0 w-full bg-white border-t border-slate-200 p-3 z-30 pb-safe">
        <div class="flex items-center gap-2">
            <div class="flex-1 relative">
                <input wire:model="prompt"
                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[13px] h-[44px] rounded-full pl-4 pr-4 focus:ring-2 focus:ring-[#3525cd]/30 outline-none"
                    placeholder="Tanya sesuatu ke Bot..." type="text" autocomplete="off" />
            </div>
            <button type="submit"
                class="w-[44px] h-[44px] rounded-full bg-[#3525cd] flex items-center justify-center text-white active:scale-90 transition-all shrink-0">
                <span class="material-symbols-outlined text-[20px]"
                    style="font-variation-settings: 'FILL' 1;">send</span>
            </button>
        </div>
    </form>
</div>
