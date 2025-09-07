@props(['items' => []])

@if (!empty($items))
    <div aria-label="breadcrumb" class="card mb-3">
        <div class="card-body py-3">
            <ol class="breadcrumb mb-0">

                @foreach ($items as $item)
                    {{-- Cek apakah ini item terakhir di dalam array --}}
                    @if ($loop->last)
                        {{-- Item terakhir: tidak punya link, teksnya aktif --}}
                        <li class="breadcrumb-item text-primary" aria-current="page">
                            {{ $item['text'] }} <span class="text-light"> /</span>
                        </li>
                    @else
                        {{-- Item lainnya: punya link --}}
                        <li class="breadcrumb-item">
                            <a href="{{ $item['url'] ?? '#' }}">{{ $item['text'] }}</a>
                        </li>
                    @endif
                @endforeach

            </ol>
        </div>
    </div>
@endif
