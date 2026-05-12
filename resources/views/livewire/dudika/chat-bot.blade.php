<div class="absolute inset-x-0 top-16 bottom-[76px] flex flex-col bg-slate-50 z-40 overflow-hidden shadow-inner">

    {{-- ===================== HEADER ===================== --}}
    <div class="flex items-center gap-3 pt-3 pb-3 px-4 bg-white z-10 shadow-sm shrink-0 border-b border-slate-100">
        <a href="{{ route('dudika.beranda') }}" wire:navigate
            class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-100 shrink-0 transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>

        <div
            class="w-10 h-10 rounded-full bg-[#e2dfff] flex items-center justify-center shrink-0 border border-[#3525cd]/20">
            <span class="material-symbols-outlined text-[#3525cd]"
                style="font-variation-settings: 'FILL' 1;">smart_toy</span>
        </div>

        <div class="flex-1">
            <h2 class="text-[18px] font-extrabold text-slate-800 leading-tight">PKL Bot</h2>
            <p class="text-[11px] font-bold text-green-600 flex items-center gap-1 mt-0.5">
                <span class="w-2 h-2 rounded-full bg-green-500 inline-block animate-pulse"></span> Online AI
            </p>
        </div>

        {{-- Tombol clear chat --}}
        <button onclick="confirmClearChat()"
            class="w-9 h-9 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
            title="Reset Chat">
            <span class="material-symbols-outlined text-[20px]">restart_alt</span>
        </button>
    </div>

    {{-- ===================== CHAT AREA ===================== --}}
    <div class="flex-1 overflow-y-auto flex flex-col gap-4 px-4 pb-4 pt-4 custom-scrollbar" x-data
        x-init="const observer = new MutationObserver(() => { $el.scrollTop = $el.scrollHeight; });
        observer.observe($el, { childList: true, subtree: true });
        $el.scrollTop = $el.scrollHeight;">

        @foreach ($messages as $msg)
            @if ($msg['role'] === 'bot')
                <div class="flex gap-2 max-w-[85%] self-start animate-fade-in-up">
                    <div
                        class="w-8 h-8 rounded-full bg-[#3525cd]/10 flex items-center justify-center shrink-0 mt-auto border border-[#3525cd]/20 shadow-inner">
                        <span class="material-symbols-outlined text-[#3525cd] text-[18px]"
                            style="font-variation-settings: 'FILL' 1;">smart_toy</span>
                    </div>
                    <div
                        class="bg-white text-slate-700 p-3 rounded-2xl rounded-bl-none shadow-sm border border-slate-100 text-[13px] leading-relaxed prose prose-sm max-w-none">
                        {!! \Illuminate\Support\Str::markdown($msg['text']) !!}
                    </div>
                </div>
            @else
                <div
                    class="max-w-[80%] self-end bg-gradient-to-br from-[#4f46e5] to-[#3525cd] text-white p-3 rounded-2xl rounded-br-none shadow-md text-[13px] leading-relaxed animate-fade-in-up">
                    {{ $msg['text'] }}
                </div>
            @endif
        @endforeach

        {{-- Loading indicator --}}
        <div wire:loading wire:target="sendMessage,setPrompt"
            class="self-start flex items-center gap-2 pl-10 text-[11px] font-bold text-slate-400 animate-pulse pb-2">
            <span class="material-symbols-outlined text-[16px] animate-spin">sync</span>
            PKL Bot sedang memikirkan jawaban...
        </div>
    </div>

    {{-- ===================== QUICK PROMPTS ===================== --}}
    @if (count($messages) <= 1)
        <div class="px-4 pb-3 flex flex-wrap gap-2 shrink-0 bg-slate-50 border-t border-slate-100 pt-3">
            <p class="w-full text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pertanyaan Umum</p>
            @foreach ($this->quickPrompts as $suggestion)
                <button wire:click="setPrompt('{{ $suggestion }}')"
                    class="bg-white border border-slate-200 text-[#3525cd] font-semibold text-[11px] px-3 h-8 rounded-full shadow-sm hover:bg-indigo-50 hover:border-[#3525cd]/30 transition-colors text-left">
                    {{ $suggestion }}
                </button>
            @endforeach
        </div>
    @endif

    {{-- ===================== INPUT AREA ===================== --}}
    <form wire:submit="sendMessage" class="w-full bg-white border-t border-slate-200 p-3 shrink-0 z-30 pb-safe">
        <div class="flex items-center gap-2">
            <div class="flex-1 relative">
                <input wire:model="prompt"
                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-[13px] font-medium h-[46px] rounded-full pl-4 pr-4 focus:ring-2 focus:ring-[#3525cd]/30 outline-none transition-all placeholder:text-slate-400 shadow-inner"
                    placeholder="Tanya sesuatu tentang aplikasi..." type="text" autocomplete="off" />
            </div>
            <button type="submit" wire:loading.attr="disabled" wire:target="sendMessage,setPrompt"
                class="w-[46px] h-[46px] rounded-full bg-gradient-to-br from-[#4f46e5] to-[#3525cd] flex items-center justify-center text-white active:scale-90 transition-all shrink-0 shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="sendMessage,setPrompt"
                    class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">send</span>
                <span wire:loading wire:target="sendMessage,setPrompt"
                    class="material-symbols-outlined text-[20px] animate-spin">sync</span>
            </button>
        </div>

        {{-- Disclaimer scope --}}
        <p class="text-center text-[10px] text-slate-400 mt-2">
            PKL Bot merespon pertanyaan terkait penggunaan aplikasi GrisaPKL
        </p>
    </form>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmClearChat() {
            Swal.fire({
                title: 'Reset Chat?',
                text: 'Semua riwayat percakapan akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3525cd',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                borderRadius: '16px',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-bold text-sm',
                    cancelButton: 'rounded-xl font-bold text-sm',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.clearChat();
                }
            });
        }
    </script>
@endpush
