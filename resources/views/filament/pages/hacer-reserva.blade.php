<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 antialiased">
        
        <div class="lg:col-span-4 space-y-4">
            <h3 class="text-[#800020] text-[10px] font-black uppercase tracking-[0.2em] text-center mb-4">1. Selecciona Foro o Cabina</h3>
            
            <div class="space-y-3 overflow-y-auto pr-1 custom-scrollbar" style="max-height: 700px;">
                @foreach(\App\Models\Foro::all() as $foro)
                    <button wire:click="seleccionarForo({{ $foro->id }})" 
                        class="w-full flex flex-col items-center justify-center p-6 rounded-2xl border shadow-sm transition-all duration-300 group
                        {{ $selectedForoId == $foro->id 
                            ? 'bg-[#fff5f5] border-[#800020] ring-1 ring-[#800020] scale-[1.02] shadow-md' 
                            : 'bg-white border-gray-200 hover:border-[#800020]/30 hover:shadow-md' }}">
                        
                        <div class="mb-3 p-4 rounded-full transition-colors duration-300 {{ $selectedForoId == $foro->id ? 'bg-[#800020] text-white' : 'bg-gray-100 text-gray-400 group-hover:bg-[#800020]/10 group-hover:text-[#800020]' }}">
                            @if(str_contains(strtolower($foro->nombre), 'cabina'))
                                <x-heroicon-o-microphone class="w-6 h-6"/>
                            @else
                                <x-heroicon-o-video-camera class="w-6 h-6"/>
                            @endif
                        </div>
                        
                        <div class="text-center">
                            <span class="block font-black text-sm uppercase tracking-tight {{ $selectedForoId == $foro->id ? 'text-[#800020]' : 'text-gray-900' }}">
                                {{ $foro->nombre }}
                            </span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1 block">SICOM</span>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-8 bg-white border border-gray-200 rounded-[2.5rem] p-8 shadow-xl flex flex-col min-h-[650px] relative overflow-hidden text-black">
            @if($selectedForoId)
                <div class="flex flex-col flex-grow">
                    <div class="border-b border-gray-100 pb-6 mb-8 text-black">
                        <h2 class="text-4xl font-black tracking-tighter uppercase leading-none">Reservar {{ $tipoEspacio }}</h2>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="w-2 h-2 rounded-full bg-[#800020] animate-pulse"></span>
                            <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest">
                                Seleccionado: <span class="text-[#800020] font-bold">{{ $foroNombre }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block px-1">2. Fecha de Sesión</label>
                            <input type="date" wire:model.live="selectedDate" 
                                class="w-full bg-gray-50 border-gray-200 border rounded-2xl p-5 text-black font-black text-xl focus:ring-2 focus:ring-[#800020] focus:border-[#800020] outline-none transition-all shadow-sm">
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block px-1">3. Horarios Disponibles</label>
                            <div class="grid grid-cols-2 gap-3">
                                @php $ocupados = $this->getHorariosOcupados(); @endphp
                                @foreach($horarios as $hora)
                                    @php $isOcupado = in_array($hora, $ocupados); @endphp
                                    <button 
                                        wire:click="{{ $isOcupado ? '' : '$set(\'selectedTime\', \''.$hora.'\')' }}"
                                        {{ $isOcupado ? 'disabled' : '' }}
                                        class="py-4 rounded-xl border-2 font-black text-xs transition-all
                                        {{ $isOcupado 
                                            ? 'bg-gray-100 border-gray-200 text-gray-300 cursor-not-allowed opacity-50' 
                                            : ($selectedTime == $hora 
                                                ? 'bg-[#800020] border-[#800020] text-white shadow-lg scale-105' 
                                                : 'bg-white border-gray-200 text-gray-500 hover:border-[#800020] hover:text-[#800020]') 
                                        }}">
                                        {{ $hora }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                 <div class="mt-auto pt-10">
    @if($selectedDate && $selectedTime)
        <div class="bg-gray-50 border border-gray-200 p-8 rounded-[2rem] shadow-inner flex flex-col md:flex-row items-center justify-between gap-6 animate-in fade-in slide-in-from-bottom-4">
            <div class="text-center md:text-left">
                <p class="text-[15px] font-black text-[#800020] uppercase tracking-widest mb-1">Confirmación de Cita</p>
                <h4 class="text-black font-black text-3xl uppercase tracking-tighter leading-none">
                    {{ $selectedTime }} <span class="text-gray-400 font-bold">HRS</span>
                </h4>
                <p class="text-black font-bold text-sm mt-1 uppercase">
                    {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l d \d\e F') }}
                </p>
            </div>

            <button wire:click="guardarReserva" 
                class="w-full md:w-auto bg-[#800020] hover:bg-[#600018] text-black px-12 py-6 rounded-2xl font-black text-md uppercase tracking-widest shadow-xl transition-all hover:scale-105 active:scale-95">
                Confirmar 
            </button>
        </div>
    @else
        <div class="p-8 border-2 border-dashed border-gray-200 rounded-[2rem] text-center">
            <p class="text-gray-400 font-bold uppercase text-[10px] tracking-[0.2em]">Selecciona fecha y hora para habilitar el botón</p>
        </div>
    @endif
</div>
            @else
                <div class="flex-grow flex flex-col items-center justify-center space-y-4 opacity-30">
                    <x-heroicon-o-finger-print class="w-16 h-16 text-[#800020] animate-pulse"/>
                    <p class="text-[#800020] font-black uppercase text-[10px] tracking-[0.3em]">Selecciona Foro o Cabina</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #800020; border-radius: 10px; }
    </style>
</x-filament-panels::page>