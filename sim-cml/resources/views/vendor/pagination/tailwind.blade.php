@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" style="display: flex; flex-direction: column; gap: 8px;">
        <div style="font-size: 14px; color: #475569;">
            Affichage de
            <strong>{{ $paginator->firstItem() }}</strong>
            à
            <strong>{{ $paginator->lastItem() }}</strong>
            sur
            <strong>{{ $paginator->total() }}</strong>
            résultats
        </div>

        <div style="display: flex; gap: 4px; flex-wrap: wrap; align-items: center;">
            {{-- Lien Précédent --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Précédent"
                      style="padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px; color: #94a3b8; background: #f8fafc; cursor: not-allowed; font-size: 14px;">
                    &lsaquo; Précédent
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Précédent"
                   style="padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; color: #1f2937; background: #fff; text-decoration: none; font-size: 14px;">
                    &lsaquo; Précédent
                </a>
            @endif

            {{-- Numéros de pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span aria-disabled="true"
                          style="padding: 6px 12px; border: 1px solid transparent; color: #64748b; font-size: 14px;">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  style="padding: 6px 12px; border: 1px solid #2563eb; border-radius: 6px; color: #fff; background: #2563eb; font-size: 14px;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" aria-label="Aller à la page {{ $page }}"
                               style="padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; color: #1f2937; background: #fff; text-decoration: none; font-size: 14px;">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Lien Suivant --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Suivant"
                   style="padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; color: #1f2937; background: #fff; text-decoration: none; font-size: 14px;">
                    Suivant &rsaquo;
                </a>
            @else
                <span aria-disabled="true" aria-label="Suivant"
                      style="padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px; color: #94a3b8; background: #f8fafc; cursor: not-allowed; font-size: 14px;">
                    Suivant &rsaquo;
                </span>
            @endif
        </div>
    </nav>
@endif
